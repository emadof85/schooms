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
    
    public function createExpense11(array $expenseDetails)
    {
        DB::beginTransaction();
    
        try {
            // Validate required fields
            $requiredFields = ['amount', 'description']; // Add your required fields
            foreach ($requiredFields as $field) {
                if (!isset($expenseDetails[$field]) || empty($expenseDetails[$field])) {
                    throw new \InvalidArgumentException("Required field missing: {$field}");
                }
            }
    
            $expenseDetails['reference_no'] = $this->generateReferenceNumber('EXP');
            $expenseDetails['recorded_by'] = auth()->id();
    
            // Additional validation
            if (!is_numeric($expenseDetails['amount']) || $expenseDetails['amount'] <= 0) {
                throw new \InvalidArgumentException('Amount must be a positive number');
            }
    
            $expense = ExpenseRecord::create($expenseDetails);
    
            if (!$expense) {
                throw new \RuntimeException('Expense record creation failed');
            }
    
            DB::commit();
            return $expense;
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Expense creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'expense_details' => $expenseDetails
            ]);
    
            throw $e; // Re-throw the exception for the caller to handle
        }
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
    
    public function getIncomeCategoryById($id)
    {
        Log::info($id);
        return IncomeCategory::findOrFail($id);
    }
    
    public function createIncomeCategory(array $categoryDetails)
    {
        Log::info('storeIncomeCategory11');
        return IncomeCategory::create($categoryDetails);
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
    
    public function createExpenseCategory(array $categoryDetails)
    {
        return ExpenseCategory::create($categoryDetails);
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
        $maxAttempts = 5;
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            try {
                return \DB::transaction(function () use ($prefix) {
                    $yearMonth = now()->format('Ym');
                    $baseReference = $prefix . '-' . $yearMonth . '-';
                    
                    // Get the current max number with lock
                    $latestRecord = IncomeRecord::where('reference_no', 'like', $baseReference . '%')
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)
                        ->lockForUpdate()
                        ->orderBy('reference_no', 'desc')
                        ->first();
                    
                    $count = $latestRecord ? (intval(substr($latestRecord->reference_no, -4)) + 1) : 1;
                    
                    if ($count > 9999) {
                        throw new \RuntimeException('Reference number sequence exhausted for month ' . $yearMonth);
                    }
                    
                    $referenceNumber = $baseReference . str_pad($count, 6, '0', STR_PAD_LEFT);
                    
                    // Immediate check if this exists (shouldn't due to lock, but just in case)
                    if (IncomeRecord::where('reference_no', $referenceNumber)->exists()) {
                        throw new \Exception('Duplicate reference number detected');
                    }
                    
                    Log::info('Generated unique reference number: ' . $referenceNumber);
                    return $referenceNumber;
                });
                
            } catch (\Exception $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw new \RuntimeException('Failed to generate unique reference number: ' . $e->getMessage());
                }
                usleep(100000); // Wait 100ms before retry
            }
        }
    }
    private function generateReferenceNumber111($prefix)
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