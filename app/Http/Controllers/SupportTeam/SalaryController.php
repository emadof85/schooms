<?php
// app/Http/Controllers/SalaryController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\FinanceRepositoryInterface;

class SalaryController extends Controller
{
    protected $salaryRepository;
    protected $userRepository;
    protected $financeRepository;
    
    public function __construct(
        SalaryRepositoryInterface $salaryRepository,
        UserRepositoryInterface $userRepository,
        FinanceRepositoryInterface $financeRepository
    ) {
        $this->salaryRepository = $salaryRepository;
        $this->userRepository = $userRepository;
        $this->financeRepository = $financeRepository;
    }
    
    public function index()
    {
        $userTypes = \App\Models\UserType::all();
        $employees = $this->userRepository->getActiveEmployees();
        return view('pages.finance.salaries.index', compact('userTypes', 'employees'));
    }
    
    public function getLevels()
    {
        try {
            $levels = $this->salaryRepository->getAllSalaryLevels();
            
            return response()->json([
                'success' => true,
                'data' => $levels
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeLevel(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'user_type_id' => 'required|exists:user_types,id',
                'base_salary' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);
            
            $level = $this->salaryRepository->createSalaryLevel($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Salary level created successfully',
                'data' => $level
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateLevel(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'user_type_id' => 'required|exists:user_types,id',
                'base_salary' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);
            
            $level = $this->salaryRepository->updateSalaryLevel($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Salary level updated successfully',
                'data' => $level
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteLevel($id)
    {
        try {
            $this->salaryRepository->updateSalaryLevel($id, ['is_active' => false]);
            
            return response()->json([
                'success' => true,
                'message' => 'Salary level deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getStructures(Request $request)
    {
        try {
            $structures = \App\Models\SalaryStructure::with(['user', 'salaryLevel'])
                ->where('is_active', true)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $structures
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeStructure(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'salary_level_id' => 'required|exists:salary_levels,id',
                'basic_salary' => 'required|numeric|min:0',
                'housing_allowance' => 'nullable|numeric|min:0',
                'transport_allowance' => 'nullable|numeric|min:0',
                'medical_allowance' => 'nullable|numeric|min:0',
                'other_allowances' => 'nullable|numeric|min:0',
                'effective_date' => 'required|date'
            ]);
            
            // Calculate total salary
            $validated['total_salary'] = 
                $validated['basic_salary'] + 
                ($validated['housing_allowance'] ?? 0) +
                ($validated['transport_allowance'] ?? 0) +
                ($validated['medical_allowance'] ?? 0) +
                ($validated['other_allowances'] ?? 0);
            
            $structure = $this->salaryRepository->createSalaryStructure($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Salary structure created successfully',
                'data' => $structure
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function processSalary(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'month_year' => 'required|date_format:Y-m',
                'payment_method' => 'required|string',
                'payment_date' => 'required|date',
                'transaction_reference' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);
            
            $salaryRecord = $this->salaryRepository->processSalaryPayment($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Salary processed successfully',
                'data' => $salaryRecord
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deductionsBonuses()
    {
        $employees = $this->userRepository->getActiveEmployees();
        return view('pages.finance.deductions-bonuses.index', compact('employees'));
    }
    
    public function getDeductionsBonuses(Request $request)
    {
        try {
            $filters = $request->only(['user_id', 'type', 'month_year']);
            $records = $this->financeRepository->getDeductionsBonuses($filters['user_id'] ?? null, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $records
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeDeductionBonus(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'type' => 'required|in:deduction,bonus',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'calculation_type' => 'required|in:fixed,percentage',
                'description' => 'nullable|string',
                'effective_date' => 'required|date',
                'end_date' => 'nullable|date',
                'is_recurring' => 'boolean',
                'month_year' => 'required|date_format:Y-m'
            ]);
            
            $record = $this->financeRepository->createDeductionBonus($validated);
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' created successfully',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateDeductionBonus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'calculation_type' => 'required|in:fixed,percentage',
                'description' => 'nullable|string',
                'effective_date' => 'required|date',
                'end_date' => 'nullable|date',
                'is_recurring' => 'boolean',
                'month_year' => 'required|date_format:Y-m'
            ]);
            
            $record = $this->financeRepository->updateDeductionBonus($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'data' => $record
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteDeductionBonus($id)
    {
        try {
            \App\Models\DeductionBonus::destroy($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSalaryCalculation(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'month_year' => 'required|date_format:Y-m'
            ]);
            
            $calculation = $this->salaryRepository->getMonthlySalary(
                $request->user_id,
                $request->month_year
            );
            
            return response()->json([
                'success' => true,
                'data' => $calculation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}