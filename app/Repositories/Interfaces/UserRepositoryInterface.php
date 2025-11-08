<?php
// app/Repositories/Interfaces/UserRepositoryInterface.php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getUserById($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function deleteUser($userId);
    public function getUsersByType($userTypeId);
    public function getActiveEmployees();
    public function getUsersWithSalaryStructures();
}