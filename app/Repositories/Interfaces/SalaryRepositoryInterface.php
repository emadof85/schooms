<?php

namespace App\Repositories\Interfaces;

use App\Models\SalaryRecord;
use App\Models\SalaryLevel;
use App\Models\SalaryStructure;
use App\Models\DeductionsBonuses;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SalaryRepositoryInterface
{
    // Salary Records
    public function getAllSalaryRecords(array $filters = []): LengthAwarePaginator;
    public function getSalaryRecordById(int $id): ?SalaryRecord;
    public function getSalaryRecordsByEmployee(int $employeeId): Collection;
    public function getSalaryRecordsByPeriod(string $period): Collection;
    public function createSalaryRecord(array $data): SalaryRecord;
    public function updateSalaryRecord(int $id, array $data): bool;
    public function deleteSalaryRecord(int $id): bool;

    // Salary Levels
    public function getAllSalaryLevels(): Collection;
    public function getSalaryLevelById(int $id): ?SalaryLevel;
    public function createSalaryLevel(array $data): SalaryLevel;
    public function updateSalaryLevel(int $id, array $data): bool;
    public function deleteSalaryLevel(int $id): bool;

    // New Salary Level methods based on user type
    public function getSalaryLevelsByUserType(int $userTypeId): Collection;
    public function getDefaultSalaryLevelForUserType(int $userTypeId): ?SalaryLevel;
    public function getAllSalaryLevelsWithUserTypes(): Collection;

    // Salary Structures
    public function getAllSalaryStructures(): Collection;
    public function getSalaryStructureById(int $id): ?SalaryStructure;
    public function getStructuresByLevel(int $levelId): Collection;
    public function createSalaryStructure(array $data): SalaryStructure;
    public function updateSalaryStructure(int $id, array $data): bool;
    public function deleteSalaryStructure(int $id): bool;

    // Deductions & Bonuses
    public function getAllDeductionsBonuses(): Collection;
    public function getDeductionsBonusesById(int $id): ?DeductionsBonuses;
    public function getDeductionsBonusesByEmployee(int $employeeId): Collection;
    public function createDeductionsBonuses(array $data): DeductionsBonuses;
    public function updateDeductionsBonuses(int $id, array $data): bool;
    public function deleteDeductionsBonuses(int $id): bool;

    // Utility Methods
    public function calculateNetSalary(float $basicSalary, array $deductions = [], array $bonuses = []): float;
    public function generatePayslip(int $salaryRecordId): array;
    public function getSalarySummaryByPeriod(string $period): array;
    public function processBulkSalaries(array $employeeData, string $period): array;
}