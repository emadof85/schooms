<?php
namespace App\Repositories;

use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Models\SalaryLevel;
use App\Models\SalaryStructure;
use App\Models\SalaryRecord;
use App\Models\DeductionBonus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryRepository implements SalaryRepositoryInterface
{
    public function getAllSalaryLevels()
    {
        return SalaryLevel::with('userType')->where('is_active', true)->get();
    }
    
    public function getSalaryLevelById($levelId)
    {
        return SalaryLevel::with('userType')->findOrFail($levelId);
    }
    
    public function createSalaryLevel(array $levelDetails)
    {
        return SalaryLevel::create($levelDetails);
    }
    
    public function updateSalaryLevel($levelId, array $newDetails)
    {
        $level = SalaryLevel::findOrFail($levelId);
        $level->update($newDetails);
        return $level;
    }
    
    public function getSalaryLevelsByUserType($userTypeId)
    {
        return SalaryLevel::where('user_type_id', $userTypeId)
            ->where('is_active', true)
            ->get();
    }
    
    public function getSalaryStructure($userId)
    {
        return SalaryStructure::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['salaryLevel', 'user'])
            ->first();
    }
    
    public function createSalaryStructure(array $structureDetails)
    {
        SalaryStructure::where('user_id', $structureDetails['user_id'])
            ->where('is_active', true)
            ->update(['is_active' => false]);
            
        return SalaryStructure::create($structureDetails);
    }
    
    public function updateSalaryStructure($userId, array $newDetails)
    {
        $structure = SalaryStructure::where('user_id', $userId)
            ->where('is_active', true)
            ->firstOrFail();
            
        $structure->update($newDetails);
        return $structure;
    }
    
    public function getMonthlySalary($userId, $monthYear)
    {
        $salaryStructure = $this->getSalaryStructure($userId);
        
        if (!$salaryStructure) {
            throw new \Exception("No active salary structure found for user.");
        }
        
        $deductions = DeductionBonus::where('user_id', $userId)
            ->where('type', 'deduction')
            ->where('month_year', $monthYear)
            ->where('applied', false)
            ->get();
            
        $bonuses = DeductionBonus::where('user_id', $userId)
            ->where('type', 'bonus')
            ->where('month_year', $monthYear)
            ->where('applied', false)
            ->get();
        
        $grossSalary = $salaryStructure->calculateTotalSalary();
        $totalDeductions = $deductions->sum('amount');
        $totalBonuses = $bonuses->sum('amount');
        $netSalary = $grossSalary - $totalDeductions + $totalBonuses;
        
        return [
            'basic_salary' => $salaryStructure->basic_salary,
            'total_allowances' => $grossSalary - $salaryStructure->basic_salary,
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeductions,
            'total_bonuses' => $totalBonuses,
            'net_salary' => $netSalary,
            'deductions' => $deductions,
            'bonuses' => $bonuses
        ];
    }
    
    public function processSalaryPayment(array $paymentData)
    {
        DB::beginTransaction();
        try {
            $calculation = $this->getMonthlySalary($paymentData['user_id'], $paymentData['month_year']);
            
            $salaryRecord = SalaryRecord::updateOrCreate(
                [
                    'user_id' => $paymentData['user_id'],
                    'payroll_period' => $paymentData['month_year']
                ],
                [
                    'payment_date' => $paymentData['payment_date'] ?? Carbon::now(),
                    'basic_salary' => $calculation['basic_salary'],
                    'total_allowances' => $calculation['total_allowances'],
                    'gross_salary' => $calculation['gross_salary'],
                    'total_deductions' => $calculation['total_deductions'],
                    'total_bonuses' => $calculation['total_bonuses'],
                    'net_salary' => $calculation['net_salary'],
                    'payment_method' => $paymentData['payment_method'] ?? 'bank_transfer',
                    'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                    'notes' => $paymentData['notes'] ?? null,
                    'status' => 'paid',
                    'paid_by' => auth()->id()
                ]
            );
            
            DeductionBonus::where('user_id', $paymentData['user_id'])
                ->where('month_year', $paymentData['month_year'])
                ->where('applied', false)
                ->update(['applied' => true]);
            
            DB::commit();
            return $salaryRecord;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getSalaryRecords($filters = [])
    {
        $query = SalaryRecord::with(['user', 'processedBy']);
        
        if (isset($filters['month_year'])) {
            $query->where('payroll_period', $filters['month_year']);
        }
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->latest()->paginate(20);
    }
    
    public function getPayrollReport($startDate, $endDate)
    {
        return SalaryRecord::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->with('user')
            ->get()
            ->groupBy('payroll_period')
            ->map(function($records, $period) {
                return [
                    'period' => $period,
                    'total_paid' => $records->sum('net_salary'),
                    'employee_count' => $records->count(),
                    'records' => $records
                ];
            });
    }
}