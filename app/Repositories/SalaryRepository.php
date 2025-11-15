<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Models\SalaryRecord;
use App\Models\SalaryLevel;
use App\Models\SalaryStructure;
use App\Models\DeductionsBonuses;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
class SalaryRepository implements SalaryRepositoryInterface
{
    // Salary Records
    public function getAllSalaryRecords(array $filters = []): LengthAwarePaginator
    {
        $query = SalaryRecord::with(['employee.user', 'deductionsBonuses'])
            ->when(isset($filters['employee_id']), function ($q) use ($filters) {
                return $q->where('employee_id', $filters['employee_id']);
            })
            ->when(isset($filters['pay_period']), function ($q) use ($filters) {
                return $q->where('pay_period', $filters['pay_period']);
            })
            ->when(isset($filters['payment_status']), function ($q) use ($filters) {
                return $q->where('payment_status', $filters['payment_status']);
            })
            ->when(isset($filters['month']), function ($q) use ($filters) {
                return $q->where('pay_period', 'like', "%-{$filters['month']}-%");
            })
            ->when(isset($filters['year']), function ($q) use ($filters) {
                return $q->where('pay_period', 'like', "{$filters['year']}-%");
            })
            ->orderBy('pay_period', 'desc')
            ->orderBy('created_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getSalaryRecordById(int $id): ?SalaryRecord
    {
        return SalaryRecord::with(['employee.user', 'deductionsBonuses'])->find($id);
    }

    public function getSalaryRecordsByEmployee(int $employeeId): Collection
    {
        return SalaryRecord::with(['employee.user', 'deductionsBonuses'])
            ->where('employee_id', $employeeId)
            ->orderBy('pay_period', 'desc')
            ->get();
    }

    public function getSalaryRecordsByPeriod(string $period): Collection
    {
        return SalaryRecord::with(['employee.user', 'deductionsBonuses'])
            ->where('pay_period', $period)
            ->orderBy('employee_id')
            ->get();
    }

    public function createSalaryRecord(array $data): SalaryRecord
    {
        return DB::transaction(function () use ($data) {
            // Validate employee exists
            if (!Employee::where('id', $data['employee_id'])->exists()) {
                throw new \InvalidArgumentException('Employee does not exist');
            }

            // Check if salary record already exists for this period
            $existingRecord = SalaryRecord::where('employee_id', $data['employee_id'])
                ->where('pay_period', $data['pay_period'])
                ->first();

            if ($existingRecord) {
                throw new \InvalidArgumentException('Salary record already exists for this employee and period');
            }

            // Calculate net salary
            $data['net_salary'] = $this->calculateNetSalary(
                $data['basic_salary'],
                $data['deductions'] ?? [],
                $data['bonuses'] ?? []
            );

            return SalaryRecord::create($data);
        });
    }

    public function updateSalaryRecord(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $salaryRecord = SalaryRecord::find($id);
            
            if (!$salaryRecord) {
                return false;
            }

            // Recalculate net salary if relevant fields changed
            if (isset($data['basic_salary']) || isset($data['deductions']) || isset($data['bonuses'])) {
                $data['net_salary'] = $this->calculateNetSalary(
                    $data['basic_salary'] ?? $salaryRecord->basic_salary,
                    $data['deductions'] ?? $salaryRecord->deductions ?? [],
                    $data['bonuses'] ?? $salaryRecord->bonuses ?? []
                );
            }

            return $salaryRecord->update($data);
        });
    }

    public function deleteSalaryRecord(int $id): bool
    {
        $salaryRecord = SalaryRecord::find($id);
        return $salaryRecord ? $salaryRecord->delete() : false;
    }

    // Salary Levels
    public function getAllSalaryLevels(): Collection
    {
        return SalaryLevel::with('salaryStructures')->orderBy('name')->get();
    }
   
    public function getSalaryLevelById(int $id): ?SalaryLevel
    {
        $salaryLevel = SalaryLevel::find($id);
        return $salaryLevel;
    }

    
    public function getAllLevels($activeOnly = false)
    {
        $query = SalaryLevel::with('userType');
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('name')->get();
    }
    public function updateSalaryLevel(int $id, array $data): bool
    {
        $salaryLevel = SalaryLevel::find($id);
        return $salaryLevel ? $salaryLevel->update($data) : false;
    }
    public function deleteSalaryLevel(int $id): bool
    {
         
        $salaryLevel = SalaryLevel::find($id);
        
        if (!$salaryLevel) {
            return false;
        }
    
        // Check if salary level is being used by employees
       
       
    
        return $salaryLevel->delete();
    }
    

    // Salary Structures
    public function getSalaryStructuresQuery(): Builder
    {
        return SalaryStructure::with('salaryLevel');
    }
    
    public function getAllSalaryLevelsName(): Collection
    {
        return SalaryLevel::orderBy('name')->get();
    }
    
    public function getAllSalaryStructures(): Collection
    {
        return SalaryStructure::with('salaryLevel')
            ->orderBy('salary_level_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function findSalaryStructure($id): SalaryStructure
    {
        return SalaryStructure::with('salaryLevel')->find($id);
    }
    public function getSalaryStructureById(int $id): ?SalaryStructure
    {
        return SalaryStructure::with('salaryLevel')->find($id);
    }

    public function getStructuresByLevel(int $levelId): Collection
    {
        return SalaryStructure::where('salary_level_id', $levelId)
            ->with('salaryLevel')
            ->orderBy('component_name')
            ->get();
    }

    public function createSalaryStructure(array $data): SalaryStructure
    {
       
        
       // Log::info('user: ',  $data['user_id']);
        return DB::transaction(function () use ($data) {
            // Validate salary level exists
            if (!SalaryLevel::where('id', $data['salary_level_id'])->exists()) {
                throw new \InvalidArgumentException('Salary level does not exist');
            }
            
            return SalaryStructure::create($data);
        });
    }

    public function updateSalaryStructure(int $id, array $data): bool
    {
        $structure = SalaryStructure::find($id);
        return $structure ? $structure->update($data) : false;
    }

    public function deleteSalaryStructure(int $id): bool
    {
        $structure = SalaryStructure::find($id);
        return $structure ? $structure->delete() : false;
    }

    // Deductions & Bonuses
    public function getAllDeductionsBonuses(): Collection
    {
        return DeductionsBonuses::with(['employee.user'])->orderBy('created_at', 'desc')->get();
    }

    public function getDeductionsBonusesById(int $id): ?DeductionsBonuses
    {
        return DeductionsBonuses::with(['employee.user'])->find($id);
    }

    public function getDeductionsBonusesByEmployee(int $employeeId): Collection
    {
        return DeductionsBonuses::with(['employee.user'])
            ->where('employee_id', $employeeId)
            ->where('status', 'active')
            ->orderBy('type')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createDeductionsBonuses(array $data): DeductionsBonuses
    {
        return DB::transaction(function () use ($data) {
            // Validate employee exists
            if (!Employee::where('id', $data['employee_id'])->exists()) {
                throw new \InvalidArgumentException('Employee does not exist');
            }

            return DeductionsBonuses::create($data);
        });
    }

    public function updateDeductionsBonuses(int $id, array $data): bool
    {
        $deductionBonus = DeductionsBonuses::find($id);
        return $deductionBonus ? $deductionBonus->update($data) : false;
    }

    public function deleteDeductionsBonuses(int $id): bool
    {
        $deductionBonus = DeductionsBonuses::find($id);
        return $deductionBonus ? $deductionBonus->delete() : false;
    }

    // Utility Methods
    public function calculateNetSalary(float $basicSalary, array $deductions = [], array $bonuses = []): float
    {
        $totalDeductions = array_sum($deductions);
        $totalBonuses = array_sum($bonuses);
        
        return $basicSalary - $totalDeductions + $totalBonuses;
    }

    public function generatePayslip(int $salaryRecordId): array
    {
        $salaryRecord = $this->getSalaryRecordById($salaryRecordId);
        
        if (!$salaryRecord) {
            throw new \InvalidArgumentException('Salary record not found');
        }

        return [
            'employee' => [
                'name' => $salaryRecord->employee->user->name ?? 'Unknown',
                'employee_id' => $salaryRecord->employee->id,
                'type' => $salaryRecord->employee->type,
            ],
            'pay_period' => $salaryRecord->pay_period,
            'basic_salary' => $salaryRecord->basic_salary,
            'deductions' => $salaryRecord->deductions ?? [],
            'bonuses' => $salaryRecord->bonuses ?? [],
            'total_deductions' => array_sum($salaryRecord->deductions ?? []),
            'total_bonuses' => array_sum($salaryRecord->bonuses ?? []),
            'net_salary' => $salaryRecord->net_salary,
            'payment_date' => $salaryRecord->payment_date,
            'payment_status' => $salaryRecord->payment_status,
        ];
    }

    public function getSalarySummaryByPeriod(string $period): array
    {
        $records = $this->getSalaryRecordsByPeriod($period);

        $totalBasicSalary = $records->sum('basic_salary');
        $totalDeductions = $records->sum(function ($record) {
            return array_sum($record->deductions ?? []);
        });
        $totalBonuses = $records->sum(function ($record) {
            return array_sum($record->bonuses ?? []);
        });
        $totalNetSalary = $records->sum('net_salary');

        return [
            'period' => $period,
            'total_employees' => $records->count(),
            'total_basic_salary' => $totalBasicSalary,
            'total_deductions' => $totalDeductions,
            'total_bonuses' => $totalBonuses,
            'total_net_salary' => $totalNetSalary,
            'records' => $records->map(function ($record) {
                return [
                    'employee_name' => $record->employee->user->name ?? 'Unknown',
                    'basic_salary' => $record->basic_salary,
                    'net_salary' => $record->net_salary,
                    'payment_status' => $record->payment_status,
                ];
            }),
        ];
    }

    public function processBulkSalaries(array $employeeData, string $period): array
    {
        return DB::transaction(function () use ($employeeData, $period) {
            $results = [
                'successful' => [],
                'failed' => [],
            ];

            foreach ($employeeData as $data) {
                try {
                    // Check if record already exists
                    $existing = SalaryRecord::where('employee_id', $data['employee_id'])
                        ->where('pay_period', $period)
                        ->first();

                    if ($existing) {
                        $results['failed'][] = [
                            'employee_id' => $data['employee_id'],
                            'error' => 'Salary record already exists for this period',
                        ];
                        continue;
                    }

                    // Create salary record
                    $salaryData = array_merge($data, ['pay_period' => $period]);
                    $salaryRecord = $this->createSalaryRecord($salaryData);
                    
                    $results['successful'][] = [
                        'employee_id' => $data['employee_id'],
                        'salary_record_id' => $salaryRecord->id,
                        'net_salary' => $salaryRecord->net_salary,
                    ];
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'employee_id' => $data['employee_id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }

    // Add these methods to your existing SalaryRepository class

/**
 * Get salary levels by user type
 */
public function getSalaryLevelsByUserType(int $userTypeId): Collection
{
    return SalaryLevel::with('userType')
        ->where('user_type_id', $userTypeId)
        ->where('is_active', true)
        ->orderBy('base_salary', 'asc')
        ->get();
}

/**
 * Get default salary level for a user type
 */
public function getDefaultSalaryLevelForUserType(int $userTypeId): ?SalaryLevel
{
    return SalaryLevel::where('user_type_id', $userTypeId)
        ->where('is_active', true)
        ->orderBy('base_salary', 'asc')
        ->first();
}

/**
 * Assign salary level to employee based on user type
 */
public function assignSalaryLevelToEmployee(int $employeeId, int $userTypeId): bool
{
    return DB::transaction(function () use ($employeeId, $userTypeId) {
        $defaultLevel = $this->getDefaultSalaryLevelForUserType($userTypeId);
        
        if (!$defaultLevel) {
            throw new \InvalidArgumentException("No active salary level found for this user type");
        }
        
        $employee = Employee::find($employeeId);
        if (!$employee) {
            throw new \InvalidArgumentException("Employee not found");
        }
        
        $employee->salary_level_id = $defaultLevel->id;
        return $employee->save();
    });
}

/**
 * Get salary structure for employee based on their salary level
 */
public function getSalaryStructureForEmployee(int $employeeId): ?SalaryStructure
{
    $employee = Employee::with('salaryLevel.salaryStructures')->find($employeeId);
    
    if (!$employee || !$employee->salaryLevel) {
        return null;
    }
    
    // Get the active salary structure for this salary level
    return SalaryStructure::where('salary_level_id', $employee->salaryLevel->id)
        ->where('is_active', true)
        ->first();
}

/**
 * Create salary level with user type validation
 */
public function createSalaryLevel(array $data): SalaryLevel
{
    return DB::transaction(function () use ($data) {
        // Validate user type exists
        /*if (!UserType::where('id', $data['user_type_id'])->exists()) {
            throw new \InvalidArgumentException('User type does not exist');
        }*/
        
        return SalaryLevel::create($data);
    });
}

/**
 * Update employee salary level
 */
public function updateEmployeeSalaryLevel(int $employeeId, int $salaryLevelId): bool
{
    return DB::transaction(function () use ($employeeId, $salaryLevelId) {
        $employee = Employee::find($employeeId);
        $salaryLevel = SalaryLevel::find($salaryLevelId);
        
        if (!$employee) {
            throw new \InvalidArgumentException('Employee not found');
        }
        
        if (!$salaryLevel) {
            throw new \InvalidArgumentException('Salary level not found');
        }
        
        // Verify the salary level matches the employee's user type
        $userTypeId = $employee->user->user_type ?? null;
        if ($salaryLevel->user_type_id != $userTypeId) {
            throw new \InvalidArgumentException('Salary level does not match employee user type');
        }
        
        $employee->salary_level_id = $salaryLevelId;
        return $employee->save();
    });
}

/**
 * Get salary levels with user type information
 */
public function getAllSalaryLevelsWithUserTypes(): Collection
{
    return SalaryLevel::with('userType')
        ->orderBy('user_type_id')
        ->orderBy('base_salary')
        ->get();
}

/**
 * Bulk assign salary levels to employees by user type
 */
public function bulkAssignSalaryLevelsByUserType(array $mapping): array
{
    return DB::transaction(function () use ($mapping) {
        $results = [
            'successful' => 0,
            'failed' => [],
        ];
        
        foreach ($mapping as $userTypeId => $salaryLevelId) {
            try {
                // Validate salary level belongs to user type
                $salaryLevel = SalaryLevel::where('id', $salaryLevelId)
                    ->where('user_type_id', $userTypeId)
                    ->first();
                    
                if (!$salaryLevel) {
                    throw new \InvalidArgumentException("Salary level does not belong to user type");
                }
                
                // Update all employees of this user type
                $updatedCount = Employee::whereHas('user', function ($query) use ($userTypeId) {
                    $query->where('user_type', $userTypeId);
                })->update(['salary_level_id' => $salaryLevelId]);
                
                $results['successful'] += $updatedCount;
                
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'user_type_id' => $userTypeId,
                    'salary_level_id' => $salaryLevelId,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    });
}
}