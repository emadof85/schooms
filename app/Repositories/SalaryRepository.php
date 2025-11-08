<?php
// app/Repositories/SalaryRepository.php

namespace App\Repositories;

use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Models\SalaryLevel;
use App\Models\SalaryStructure;
use App\Models\SalaryRecord;
use App\Models\DeductionBonus;
use App\Models\User;
use App\Services\SalaryService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryRepository implements SalaryRepositoryInterface
{
    protected $salaryService;
    
    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }
    
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
        // Deactivate any existing active structure
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
        return $this->salaryService->calculateMonthlySalary($userId, $monthYear);
    }
    
    public function processSalaryPayment(array $paymentData)
    {
        DB::beginTransaction();
        try {
            $salaryRecord = $this->salaryService->processSalaryPayment(
                $paymentData['user_id'],
                $paymentData['month_year'],
                $paymentData
            );
            
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