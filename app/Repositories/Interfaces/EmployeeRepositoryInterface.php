<?php

namespace App\Repositories\Interfaces;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    /**
     * Get all employees
     */
    public function getAllEmployees(): Collection;

    /**
     * Find employee by ID
     */
    public function findEmployee(int $id): ?Employee;

    /**
     * Create a new employee
     */
    public function createEmployee(array $data): Employee;

    /**
     * Update employee
     */
    public function updateEmployee(int $id, array $data): bool;

    /**
     * Delete employee
     */
    public function deleteEmployee(int $id): bool;

    /**
     * Get employees by type
     */
    public function getEmployeesByType(string $type): Collection;

    /**
     * Get active employees
     */
    public function getActiveEmployees(): Collection;

    /**
     * Get drivers only
     */
    public function getDrivers(): Collection;

    /**
     * Get employee by user ID
     */
    public function getEmployeeByUserId(int $userId): ?Employee;

    /**
     * Check if license number exists
     */
    public function licenseNumberExists(string $licenseNumber, ?int $excludeEmployeeId = null): bool;

    /**
     * Get employees with expired licenses
     */
    public function getEmployeesWithExpiredLicenses(): Collection;

    /**
     * Get employees with expiring licenses
     */
    public function getEmployeesWithExpiringLicenses(int $days = 30): Collection;

    /**
     * Activate employee
     */
    public function activateEmployee(int $id): bool;

    /**
     * Deactivate employee
     */
    public function deactivateEmployee(int $id): bool;
}