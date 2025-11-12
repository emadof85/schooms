<?php
namespace App\Repositories;

use App\Repositories\Interfaces\FinanceRepositoryInterface;
use App\Models\IncomeRecord;
use App\Models\ExpenseRecord;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\DeductionBonus;
use App\Models\SalaryRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinanceRepository implements FinanceRepositoryInterface
{
    public function getAllIncomes($filters = [])
    {
        $query = IncomeRecord::with(['category', 'recordedBy']);
        
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('income_date', [$filters['start_date'], $filters['end_date']]);
        }
        
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        return $query->latest()->paginate(20);
    }
    
    public function getIncomeById($incomeId)
    {
        return IncomeRecord::with(['category', 'recordedBy'])->findOrFail($incomeId);
    }
    
    public function createIncome11(array $incomeDetails)
    {
      
        $incomeDetails['reference_no'] = $this->generateReferenceNumber('INC');
        $incomeDetails['recorded_by'] = auth()->id();
       
        return IncomeRecord::create($incomeDetails);
    }

 
    
    public function updateIncome($incomeId, array $newDetails)
    {
        $income = IncomeRecord::findOrFail($incomeId);
        $income->update($newDetails);
        return $income;
    }
    
    public function deleteIncome($incomeId)
    {
        Log::info('sddsdssssss: '.$incomeId);
        return IncomeRecord::destroy($incomeId);
    }
    
    public function getIncomeSummary($startDate, $endDate)
    {
        return IncomeRecord::whereBetween('income_date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->get();
    }
    
    public function getAllExpenses($filters = [])
    {
        $query = ExpenseRecord::with(['category', 'recordedBy']);
        
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('expense_date', [$filters['start_date'], $filters['end_date']]);
        }
        
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        return $query->latest()->paginate(20);
    }
    
    public function getExpenseById($expenseId)
    {
        return ExpenseRecord::with(['category', 'recordedBy'])->findOrFail($expenseId);
    }
    
    public function createExpense(array $expenseDetails)
    {
        $expenseDetails['reference_no'] = $this->generateReferenceNumber('EXP');
        $expenseDetails['recorded_by'] = auth()->id();
        
        return ExpenseRecord::create($expenseDetails);
    }
    
    public function updateExpense($expenseId, array $newDetails)
    {
        $expense = ExpenseRecord::findOrFail($expenseId);
        $expense->update($newDetails);
        return $expense;
    }
    
    public function deleteExpense($expenseId)
    {
        return ExpenseRecord::destroy($expenseId);
    }
    
    public function getExpenseSummary($startDate, $endDate)
    {
        return ExpenseRecord::whereBetween('expense_date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->get();
    }
    
    public function getDeductionsBonuses($userId = null, $filters = [])
    {
        $query = DeductionBonus::with('user');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['month_year'])) {
            $query->where('month_year', $filters['month_year']);
        }
        
        return $query->latest()->get();
    }
    
    public function createDeductionBonus(array $details)
    {
        return DeductionBonus::create($details);
    }
    
    public function updateDeductionBonus($id, array $newDetails)
    {
        $record = DeductionBonus::findOrFail($id);
        $record->update($newDetails);
        return $record;
    }
    
    public function getFinancialDashboard($period = 'month')
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        if ($period === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }
        
        $totalIncome = IncomeRecord::whereBetween('income_date', [$startDate, $endDate])->sum('amount');
        $totalExpense = ExpenseRecord::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $totalSalaries = SalaryRecord::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('net_salary');
            
        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense + $totalSalaries,
            'total_salaries' => $totalSalaries,
            'net_balance' => $totalIncome - ($totalExpense + $totalSalaries),
            'period' => $period
        ];
    }
    
    public function getIncomeVsExpenseReport($startDate, $endDate)
    {
        $incomeByCategory = $this->getIncomeSummary($startDate, $endDate);
        $expenseByCategory = $this->getExpenseSummary($startDate, $endDate);
        $salaryExpense = SalaryRecord::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('net_salary');
            
        return [
            'income' => [
                'total' => $incomeByCategory->sum('total'),
                'by_category' => $incomeByCategory
            ],
            'expense' => [
                'total' => $expenseByCategory->sum('total') + $salaryExpense,
                'operational' => $expenseByCategory,
                'salaries' => $salaryExpense
            ],
            'net_profit' => $incomeByCategory->sum('total') - ($expenseByCategory->sum('total') + $salaryExpense)
        ];
    }
    
    public function getCashFlowStatement($startDate, $endDate)
    {
        return [
            'operating_activities' => [],
            'investing_activities' => [],
            'financing_activities' => []
        ];
    }
   

  

    public function createIncome(array $incomeDetails)
    {
        \DB::beginTransaction();
        
        try {
            Log::info('=== CREATE INCOME PROCESS STARTED ===');
            Log::info('Income details received:', $incomeDetails);
            
            // Validate required fields
            $requiredFields = ['title', 'amount', 'income_date', 'category_id', 'payment_method', 'received_from'];
            foreach ($requiredFields as $field) {
                if (empty($incomeDetails[$field])) {
                    $errorMessage = "Required field '{$field}' is missing or empty";
                    Log::error($errorMessage);
                    throw new \InvalidArgumentException($errorMessage);
                }
            }

            // Generate reference number within transaction
            $incomeDetails['reference_no'] = $this->generateReferenceNumber('INC');
            $incomeDetails['recorded_by'] = auth()->id();
            
            Log::info('Generated reference number: ' . $incomeDetails['reference_no']);
            Log::info('Recorded by user ID: ' . auth()->id());

            // Create the income record
            $income = IncomeRecord::create($incomeDetails);
            
            if (!$income) {
                $errorMessage = 'Failed to create income record in database';
                Log::error($errorMessage);
                throw new \RuntimeException($errorMessage);
            }

            \DB::commit();
            
            Log::info('=== CREATE INCOME SUCCESS ===');
            Log::info('Income created with ID: ' . $income->id);
            Log::info('Income reference: ' . $income->reference_no);
            Log::info('Income amount: ' . $income->amount);
            
            return [
                'success' => true,
                'message' => 'Income record created successfully',
                'data' => $income,
                'reference_no' => $income->reference_no
            ];

        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Income creation failed: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() == '23000') {
                return [
                    'success' => false,
                    'message' => 'Duplicate reference number detected. Please try again.',
                    'error_type' => 'duplicate_reference'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to create income record: ' . $e->getMessage(),
                'error_type' => 'creation_failed'
            ];
        }
    }

    // ==================== CATEGORY MANAGEMENT ====================

    // Income Categories
    public function getAllIncomeCategories($filters = [])
    {
        $query = IncomeCategory::query();
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->orderBy('name')->get();
    }
    
    public function getIncomeCategoryById($categoryId)
    {
        return IncomeCategory::findOrFail($categoryId);
    }
    
    public function createIncomeCategory1(array $categoryDetails)
    {
        Log::info('storeIncomeCategory11');
        return IncomeCategory::create($categoryDetails);
    }
    public function createIncomeCategory(array $categoryDetails)
{
    \DB::beginTransaction();
    
    try {
        \Log::info('=== CREATE INCOME CATEGORY PROCESS STARTED ===');
        \Log::info('Category details received:', $categoryDetails);
        
        // Validate required fields
        if (empty($categoryDetails['name'])) {
            $errorMessage = "Category name is required";
            \Log::error($errorMessage);
            throw new \InvalidArgumentException($errorMessage);
        }

        // Check for duplicate category name
        $existingCategory = IncomeCategory::where('name', $categoryDetails['name'])->first();
        if ($existingCategory) {
            $errorMessage = "Category name '{$categoryDetails['name']}' already exists";
            \Log::error($errorMessage);
            throw new \InvalidArgumentException($errorMessage);
        }

        // Set default values if not provided
        if (!isset($categoryDetails['is_active'])) {
            $categoryDetails['is_active'] = true;
        }

        \Log::info('Creating income category with data:', $categoryDetails);

        // Create the category
        $category = IncomeCategory::create($categoryDetails);
        
        if (!$category) {
            $errorMessage = 'Failed to create income category in database';
            \Log::error($errorMessage);
            throw new \RuntimeException($errorMessage);
        }

        \DB::commit();
        
        \Log::info('=== CREATE INCOME CATEGORY SUCCESS ===');
        \Log::info('Category created with ID: ' . $category->id);
        \Log::info('Category name: ' . $category->name);
        
        return [
            'success' => true,
            'message' => 'Income category created successfully',
            'data' => $category
        ];

    } catch (\InvalidArgumentException $e) {
        \DB::rollBack();
        \Log::error('Income category creation validation error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_type' => 'validation_error'
        ];

    } catch (\Illuminate\Database\QueryException $e) {
        \DB::rollBack();
        \Log::error('Income category creation database error: ' . $e->getMessage());
        \Log::error('SQL Error Code: ' . $e->getCode());
        
        $errorMessage = 'Database error occurred while creating income category';
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $errorMessage = 'Category with this name already exists';
        }
        
        return [
            'success' => false,
            'message' => $errorMessage,
            'error_type' => 'database_error',
            'sql_error_code' => $e->getCode()
        ];

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Income category creation general error: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return [
            'success' => false,
            'message' => 'An unexpected error occurred while creating income category',
            'error_type' => 'general_error',
            'system_error' => config('app.debug') ? $e->getMessage() : null
        ];
    }
}
    public function updateIncomeCategory($categoryId, array $newDetails)
    {
        $category = IncomeCategory::findOrFail($categoryId);
        $category->update($newDetails);
        return $category;
    }
    
    public function deleteIncomeCategory($categoryId)
    {
        // Check if category is being used
        $incomeCount = IncomeRecord::where('category_id', $categoryId)->count();
        if ($incomeCount > 0) {
            throw new \Exception('Cannot delete category. It is being used by ' . $incomeCount . ' income records.');
        }
        
        return IncomeCategory::destroy($categoryId);
    }
    
    // Expense Categories
    public function getAllExpenseCategories($filters = [])
    {
        $query = ExpenseCategory::query();
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->orderBy('name')->get();
    }
    
    public function getExpenseCategoryById($categoryId)
    {
        return ExpenseCategory::findOrFail($categoryId);
    }
    
    public function createExpenseCategory11(array $categoryDetails)
    {
        return ExpenseCategory::create($categoryDetails);
    }
    public function createExpenseCategory(array $categoryDetails)
{
    \DB::beginTransaction();
    
    try {
        \Log::info('=== CREATE EXPENSE CATEGORY PROCESS STARTED ===');
        \Log::info('Category details received:', $categoryDetails);
        
        // Validate required fields
        if (empty($categoryDetails['name'])) {
            $errorMessage = "Category name is required";
            \Log::error($errorMessage);
            throw new \InvalidArgumentException($errorMessage);
        }

        // Check for duplicate category name
        $existingCategory = ExpenseCategory::where('name', $categoryDetails['name'])->first();
        if ($existingCategory) {
            $errorMessage = "Category name '{$categoryDetails['name']}' already exists";
            \Log::error($errorMessage);
            throw new \InvalidArgumentException($errorMessage);
        }

        // Set default values if not provided
        if (!isset($categoryDetails['is_active'])) {
            $categoryDetails['is_active'] = true;
        }

        \Log::info('Creating expense category with data:', $categoryDetails);

        // Create the category
        $category = ExpenseCategory::create($categoryDetails);
        
        if (!$category) {
            $errorMessage = 'Failed to create expense category in database';
            \Log::error($errorMessage);
            throw new \RuntimeException($errorMessage);
        }

        \DB::commit();
        
        \Log::info('=== CREATE EXPENSE CATEGORY SUCCESS ===');
        \Log::info('Category created with ID: ' . $category->id);
        \Log::info('Category name: ' . $category->name);
        
        return [
            'success' => true,
            'message' => 'Expense category created successfully',
            'data' => $category
        ];

    } catch (\InvalidArgumentException $e) {
        \DB::rollBack();
        \Log::error('Expense category creation validation error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error_type' => 'validation_error'
        ];

    } catch (\Illuminate\Database\QueryException $e) {
        \DB::rollBack();
        \Log::error('Expense category creation database error: ' . $e->getMessage());
        \Log::error('SQL Error Code: ' . $e->getCode());
        
        $errorMessage = 'Database error occurred while creating expense category';
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $errorMessage = 'Category with this name already exists';
        }
        
        return [
            'success' => false,
            'message' => $errorMessage,
            'error_type' => 'database_error',
            'sql_error_code' => $e->getCode()
        ];

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Expense category creation general error: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return [
            'success' => false,
            'message' => 'An unexpected error occurred while creating expense category',
            'error_type' => 'general_error',
            'system_error' => config('app.debug') ? $e->getMessage() : null
        ];
    }
}
    public function updateExpenseCategory($categoryId, array $newDetails)
    {
        $category = ExpenseCategory::findOrFail($categoryId);
        $category->update($newDetails);
        return $category;
    }
    
    public function deleteExpenseCategory($categoryId)
    {
        // Check if category is being used
        $expenseCount = ExpenseRecord::where('category_id', $categoryId)->count();
        if ($expenseCount > 0) {
            throw new \Exception('Cannot delete category. It is being used by ' . $expenseCount . ' expense records.');
        }
        
        return ExpenseCategory::destroy($categoryId);
    }

    // ==================== DRY EXPORT METHODS ====================

    public function getIncomesForExport($startDate, $endDate, $categoryId = null)
    {
        return $this->getRecordsForExport('income', $startDate, $endDate, $categoryId);
    }
    
    public function getExpensesForExport($startDate, $endDate, $categoryId = null)
    {
        return $this->getRecordsForExport('expense', $startDate, $endDate, $categoryId);
    }
    
    public function getRecordsForExport($type, $startDate, $endDate, $categoryId = null)
    {
        if ($type === 'income') {
            $query = IncomeRecord::with('category')
                ->whereBetween('income_date', [$startDate, $endDate])
                ->orderBy('income_date', 'desc');
                
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        } else {
            $query = ExpenseRecord::with('category')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->orderBy('expense_date', 'desc');
                
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }
        
        return $query->get();
    }

    // ==================== UTILITY METHODS ====================

    private function generateReferenceNumber($prefix)
    {
        return \DB::transaction(function () use ($prefix) {
            $yearMonth = now()->format('Ym');
            $baseReference = $prefix . '-' . $yearMonth . '-';
            
            // Get the latest reference number for this month
            $latestRecord = IncomeRecord::where('reference_no', 'like', $baseReference . '%')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->orderBy('reference_no', 'desc')
                ->first();
            
            if ($latestRecord) {
                // Extract the number part and increment
                $lastNumber = intval(substr($latestRecord->reference_no, -4));
                $count = $lastNumber + 1;
            } else {
                $count = 1;
            }
            
            $referenceNumber = $baseReference . str_pad($count, 4, '0', STR_PAD_LEFT);
            
            Log::info('Generated reference number: ' . $referenceNumber);
            
            return $referenceNumber;
        });
    }
}