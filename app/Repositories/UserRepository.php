<?php
namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {
        return User::with('userType')->latest()->get();
    }
    
    public function getUserById($userId)
    {
        return User::with(['userType', 'salaryStructure'])->findOrFail($userId);
    }
    
    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }
    
    public function updateUser($userId, array $newDetails)
    {
        $user = User::findOrFail($userId);
        $user->update($newDetails);
        return $user;
    }
    
    public function deleteUser($userId)
    {
        return User::destroy($userId);
    }
    
    public function getUsersByType($userTypeId)
    {
        return User::where('user_type_id', $userTypeId)
            ->with('userType')
            ->get();
    }
    
    public function getActiveEmployees()
    {
        return User::whereHas('employee', function($query) {
            $query->where('active', true);
        })->with(['userType', 'employee'])->get();
    }
    
    public function getUsersWithSalaryStructures()
    {
        return User::whereHas('salaryStructure')
            ->with(['userType', 'salaryStructure.salaryLevel'])
            ->get();
    }
}