<?php
// app/Http/Controllers/FinanceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalaryService;
use App\Services\FinancialReportService;
use App\Models\SalaryRecord;
use App\Models\IncomeRecord;
use App\Models\ExpenseRecord;

class FinanceController extends Controller
{
    protected $salaryService;
    protected $reportService;
    
    public function __construct(SalaryService $salaryService, FinancialReportService $reportService)
    {
        $this->salaryService = $salaryService;
        $this->reportService = $reportService;
    }
    
    public function payrollIndex()
    {
        $salaries = SalaryRecord::with('user')->latest()->paginate(20);
        return view('finance.payroll.index', compact('salaries'));
    }
    
    public function processSalary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month_year' => 'required|date_format:Y-m',
            'payment_method' => 'required|string'
        ]);
        
        try {
            $salaryRecord = $this->salaryService->processSalaryPayment(
                $request->user_id,
                $request->month_year,
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Salary processed successfully',
                'data' => $salaryRecord
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    public function financialDashboard()
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        
        $currentReport = $this->reportService->getIncomeVsExpense(
            now()->startOfMonth(),
            now()->endOfMonth()
        );
        
        $previousReport = $this->reportService->getIncomeVsExpense(
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        );
        
        return view('finance.dashboard', compact('currentReport', 'previousReport'));
    }
}