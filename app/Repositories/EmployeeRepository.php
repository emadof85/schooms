<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function getAllEmployees(): Collection
    {
        return Employee::with(['user', 'busDrivers.bus'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findEmployee(int $id): ?Employee
    {
        return Employee::with(['user', 'busDrivers.bus'])->find($id);
    }

    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            // Validate user exists
            if (!User::where('id', $data['user_id'])->exists()) {
                throw new \InvalidArgumentException('User does not exist');
            }

            // Check if employee already exists for this user
            if (Employee::where('user_id', $data['user_id'])->exists()) {
                throw new \InvalidArgumentException('Employee already exists for this user');
            }

            // Check license number uniqueness if provided
            if (isset($data['license_number']) && $this->licenseNumberExists($data['license_number'])) {
                throw new \InvalidArgumentException('License number already exists');
            }

            return Employee::create($data);
        });
    }

    public function updateEmployee(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $employee = Employee::find($id);
            
            if (!$employee) {
                return false;
            }

            // Check license number uniqueness if provided
            if (isset($data['license_number']) && 
                $this->licenseNumberExists($data['license_number'], $id)) {
                throw new \InvalidArgumentException('License number already exists');
            }

            return $employee->update($data);
        });
    }

    public function deleteEmployee(int $id): bool
    {
        $employee = Employee::find($id);
        
        if (!$employee) {
            return false;
        }

        // Check if employee has bus assignments before deleting
        $hasBusAssignments = $employee->busDrivers()->exists();
        
        if ($hasBusAssignments) {
            // Deactivate instead of deleting
            return $employee->update(['active' => false]);
        }

        return $employee->delete();
    }

    public function getEmployeesByType(string $type): Collection
    {
        return Employee::with(['user', 'busDrivers.bus'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveEmployees(): Collection
    {
        return Employee::with(['user', 'busDrivers.bus'])
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDrivers(): Collection
    {
        return $this->getEmployeesByType('driver');
    }

    public function getEmployeeByUserId(int $userId): ?Employee
    {
        return Employee::with(['user', 'busDrivers.bus'])
            ->where('user_id', $userId)
            ->first();
    }

    public function licenseNumberExists(string $licenseNumber, ?int $excludeEmployeeId = null): bool
    {
        $query = Employee::where('license_number', $licenseNumber);
        
        if ($excludeEmployeeId) {
            $query->where('id', '!=', $excludeEmployeeId);
        }

        return $query->exists();
    }

    public function getEmployeesWithExpiredLicenses(): Collection
    {
        return Employee::with(['user'])
            ->where('active', true)
            ->where('type', 'driver')
            ->whereNotNull('license_expiry')
            ->where('license_expiry', '<', now())
            ->get();
    }

    public function getEmployeesWithExpiringLicenses(int $days = 30): Collection
    {
        $expiryDate = now()->addDays($days);

        return Employee::with(['user'])
            ->where('active', true)
            ->where('type', 'driver')
            ->whereNotNull('license_expiry')
            ->whereBetween('license_expiry', [now(), $expiryDate])
            ->get();
    }

    public function activateEmployee(int $id): bool
    {
        $employee = Employee::find($id);
        return $employee ? $employee->update(['active' => true]) : false;
    }

    public function deactivateEmployee(int $id): bool
    {
        $employee = Employee::find($id);
        return $employee ? $employee->update(['active' => false]) : false;
    }
}