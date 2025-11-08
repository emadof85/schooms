<?php
// app/Services/SalaryService.php

namespace App\Services;

use App\Models\SalaryStructure;
use App\Models\DeductionBonus;
use App\Models\SalaryRecord;
use App\Models\User;
use Carbon\Carbon;

class SalaryService
{
    public function calculateMonthlySalary($userId, $monthYear)
    {
        $user = User::findOrFail($userId);
        $salaryStructure = SalaryStructure::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
            
        if (!$salaryStructure) {
            throw new \Exception("No active salary structure found for user.");
        }
        
        // Get deductions and bonuses for the month
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
    
    public function processSalaryPayment($userId, $monthYear, $paymentData)
    {
        $calculation = $this->calculateMonthlySalary($userId, $monthYear);
        
        $salaryRecord = SalaryRecord::updateOrCreate(
            [
                'user_id' => $userId,
                'payroll_period' => $monthYear
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
        
        // Mark deductions and bonuses as applied
        DeductionBonus::where('user_id', $userId)
            ->where('month_year', $monthYear)
            ->where('applied', false)
            ->update(['applied' => true]);
        
        return $salaryRecord;
    }
}