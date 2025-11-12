<?php
// app/Http/Controllers/SupportTeam/FinanceController.php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\FinanceRepositoryInterface;
use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Exports\IncomesExport;
use App\Exports\ExpensesExport;
use App\Exports\BadMethodCallException;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    protected $financeRepository;
    protected $salaryRepository;
    protected $userRepository;
    
    public function __construct(
        FinanceRepositoryInterface $financeRepository,
        SalaryRepositoryInterface $salaryRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->financeRepository = $financeRepository;
        $this->salaryRepository = $salaryRepository;
        $this->userRepository = $userRepository;
    }
    
    public function dashboard()
    {
        Log::info('hiuhihiu');
        return view('pages.support_team.finance.dashboard');
    }
    
    public function getDashboardData(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $data = $this->financeRepository->getFinancialDashboard($period);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== INCOME METHODS ====================
    
    public function incomeIndex()
    {
        $categories = IncomeCategory::where('is_active', true)->get();
        return view('pages.support_team.finance.income.index', compact('categories'));
    }
    
    public function getIncomes(Request $request)
    {
        try {
            $filters = $request->only(['start_date', 'end_date', 'category_id']);
            $incomes = $this->financeRepository->getAllIncomes($filters);
            
            return response()->json([
                'success' => true,
                'data' => $incomes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeIncome(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:income_categories,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'income_date' => 'required|date',
                'payment_method' => 'required|string',
                'received_from' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $result = $this->financeRepository->createIncome($validated);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'reference_no' => $result['reference_no']
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error_type' => $result['error_type'] ?? 'unknown_error'
                ], 400);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Income store validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Income store controller error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process income creation request'
            ], 500);
        }
    }

    public function getIncome($incomeId)
    {
        try {
            $income = $this->financeRepository->getIncomeById($incomeId);
            
            return response()->json([
                'success' => true,
                'data' => $income
            ]);
        } catch (\Exception $e) {
            \Log::error('Get income error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function editIncome($incomeId)
    {
        try {
            \Log::info('Edit income called for ID: ' . $incomeId);
            $income = $this->financeRepository->getIncomeById($incomeId);
            
            return response()->json([
                'success' => true,
                'data' => $income
            ]);
        } catch (\Exception $e) {
            \Log::error('Edit income error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateIncome(Request $request, $incomeId)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:income_categories,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'income_date' => 'required|date',
                'payment_method' => 'required|string',
                'received_from' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $income = $this->financeRepository->updateIncome($incomeId, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Income updated successfully',
                'data' => $income
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteIncome($incomeId)
    {
        try {
            Log::info('Deleting income ID: ' . $incomeId);
            $this->financeRepository->deleteIncome($incomeId);
            
            return response()->json([
                'success' => true,
                'message' => 'Income deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== EXPENSE METHODS ====================
    
    public function expenseIndex()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('pages.support_team.finance.expense.index', compact('categories'));
    }
    
    public function getExpenses(Request $request)
    {
        try {
            $filters = $request->only(['start_date', 'end_date', 'category_id']);
            $expenses = $this->financeRepository->getAllExpenses($filters);
            
            return response()->json([
                'success' => true,
                'data' => $expenses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeExpense(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:expense_categories,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'payment_method' => 'required|string',
                'paid_to' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $expense = $this->financeRepository->createExpense($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateExpense(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:expense_categories,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'payment_method' => 'required|string',
                'paid_to' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $expense = $this->financeRepository->updateExpense($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteExpense($id)
    {
        try {
            $this->financeRepository->deleteExpense($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== CATEGORY MANAGEMENT METHODS ====================
    
    public function incomeCategoryIndex()
    {
        $categories = $this->financeRepository->getAllIncomeCategories();
        return view('pages.support_team.finance.categories.income-index', compact('categories'));
    }
    
    public function getIncomeCategories(Request $request)
    {
        try {
            $filters = $request->only(['is_active', 'search']);
            $categories = $this->financeRepository->getAllIncomeCategories($filters);
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeIncomeCategory(Request $request)
    {
        Log::info('storeIncomeCategory');
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:income_categories,name',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);
            
            $category = $this->financeRepository->createIncomeCategory($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Income category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateIncomeCategory(Request $request, $categoryId)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:income_categories,name,' . $categoryId,
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);
            
            $category = $this->financeRepository->updateIncomeCategory($categoryId, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Income category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteIncomeCategory($categoryId)
    {
        try {
            $this->financeRepository->deleteIncomeCategory($categoryId);
            
            return response()->json([
                'success' => true,
                'message' => 'Income category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function expenseCategoryIndex()
    {
        $categories = $this->financeRepository->getAllExpenseCategories();
        return view('pages.support_team.finance.categories.expense-index', compact('categories'));
    }
    
    public function getExpenseCategories(Request $request)
    {
        try {
            $filters = $request->only(['is_active', 'search']);
            $categories = $this->financeRepository->getAllExpenseCategories($filters);
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeExpenseCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:expense_categories,name',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);
            
            $category = $this->financeRepository->createExpenseCategory($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense category created successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateExpenseCategory(Request $request, $categoryId)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:expense_categories,name,' . $categoryId,
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);
            
            $category = $this->financeRepository->updateExpenseCategory($categoryId, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteExpenseCategory($categoryId)
    {
        try {
            $this->financeRepository->deleteExpenseCategory($categoryId);
            
            return response()->json([
                'success' => true,
                'message' => 'Expense category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== REPORT METHODS ====================
    
    public function incomeExpenseReport()
    {
        return view('pages.support_team.finance.reports.income-expense');
    }
    
    public function getIncomeExpenseReport(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date'
            ]);
            
            $report = $this->financeRepository->getIncomeVsExpenseReport(
                $request->start_date,
                $request->end_date
            );
            
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function payrollReport()
    {
        return view('pages.support_team.finance.reports.payroll');
    }
    
    public function getPayrollReport(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date'
            ]);
            
            $report = $this->salaryRepository->getPayrollReport(
                $request->start_date,
                $request->end_date
            );
            
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== EXPORT METHODS ====================
    
    public function exportExcel(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $categoryId = $request->get('category_id');
            
            $incomes = $this->financeRepository->getIncomesForExport($startDate, $endDate, $categoryId);
            $category = $categoryId ? IncomeCategory::find($categoryId) : null;
            
            $filename = 'incomes-' . date('Y-m-d') . '.xlsx';
            
            return Excel::download(new IncomesExport($incomes, 'income', $startDate, $endDate, $category), $filename);
        } catch (\Exception $e) {
            \Log::error('Income Excel export error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting to Excel: ' . $e->getMessage());
        }
    }
    
    public function exportPdf(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $categoryId = $request->get('category_id');
            
            $incomes = $this->financeRepository->getIncomesForExport($startDate, $endDate, $categoryId);
            $totalAmount = $incomes->sum('amount');
            $category = $categoryId ? IncomeCategory::find($categoryId) : null;
            
            $filename = 'incomes-' . date('Y-m-d') . '.pdf';
            
            $pdf = PDF::loadView('exports.incomes-pdf', [
                'incomes' => $incomes,
                'totalAmount' => $totalAmount,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'category' => $category,
                'title' => 'Incomes Report'
            ]);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Income PDF export error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting to PDF: ' . $e->getMessage());
        }
    }
    
    public function exportExpenseExcel(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $categoryId = $request->get('category_id');
            
            $expenses = $this->financeRepository->getExpensesForExport($startDate, $endDate, $categoryId);
            $category = $categoryId ? ExpenseCategory::find($categoryId) : null;
            
            $filename = 'expenses-' . date('Y-m-d') . '.xlsx';
            
            return Excel::download(new ExpensesExport($expenses, 'expense', $startDate, $endDate, $category), $filename);
        } catch (\Exception $e) {
            \Log::error('Expense Excel export error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting to Excel: ' . $e->getMessage());
        }
    }
    
    public function exportExpensePdf(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $categoryId = $request->get('category_id');
            
            $expenses = $this->financeRepository->getExpensesForExport($startDate, $endDate, $categoryId);
            $totalAmount = $expenses->sum('amount');
            $category = $categoryId ? ExpenseCategory::find($categoryId) : null;
            
            $filename = 'expenses-' . date('Y-m-d') . '.pdf';
            
            $pdf = PDF::loadView('exports.expenses-pdf', [
                'expenses' => $expenses,
                'totalAmount' => $totalAmount,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'category' => $category,
                'title' => 'Expenses Report'
            ]);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Expense PDF export error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting to PDF: ' . $e->getMessage());
        }
    }
}