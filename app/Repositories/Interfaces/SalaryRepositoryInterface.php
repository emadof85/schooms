<?php
// app/Repositories/Interfaces/SalaryRepositoryInterface.php

namespace App\Repositories\Interfaces;

interface SalaryRepositoryInterface
{
    public function getAllSalaryLevels();
    public function getSalaryLevelById($levelId);
    public function createSalaryLevel(array $levelDetails);
    public function updateSalaryLevel($levelId, array $newDetails);
    public function getSalaryLevelsByUserType($userTypeId);
    
    public function getSalaryStructure($userId);
    public function createSalaryStructure(array $structureDetails);
    public function updateSalaryStructure($userId, array $newDetails);
    
    public function getMonthlySalary($userId, $monthYear);
    public function processSalaryPayment(array $paymentData);
    public function getSalaryRecords($filters = []);
    public function getPayrollReport($startDate, $endDate);
}