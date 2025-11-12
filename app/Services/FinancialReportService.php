<?php
// app/Services/FinancialReportService.php

namespace App\Services;

use App\Models\IncomeRecord;
use App\Models\ExpenseRecord;
use App\Models\SalaryRecord;
use App\Models\PaymentRecord;
use Carbon\Carbon;

class FinancialReportService
{
    public function getIncomeVsExpense($startDate, $endDate)
    {
        $income = IncomeRecord::whereBetween('income_date', [$startDate, $endDate])
            ->sum('amount');
            
        $expense = ExpenseRecord::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
            
        $salaryExpense = SalaryRecord::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('net_salary');
            
        $totalExpense = $expense + $salaryExpense;
        
        return [
            'income' => $income,
            'expense' => $totalExpense,
            'salary_expense' => $salaryExpense,
            'other_expense' => $expense,
            'net_profit' => $income - $totalExpense
        ];
    }
    
    public function getFeeCollectionReport($year)
    {
        return PaymentRecord::where('year', $year)
            ->where('paid', 1)
            ->with(['payment', 'student'])
            ->get()
            ->groupBy(function($record) {
                return Carbon::parse($record->created_at)->format('Y-m');
            })
            ->map(function($monthRecords) {
                return [
                    'total_collected' => $monthRecords->sum('amt_paid'),
                    'transactions' => $monthRecords->count()
                ];
            });
    }
}