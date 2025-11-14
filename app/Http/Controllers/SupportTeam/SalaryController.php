<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SalaryRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Repositories\UserRepo;
class SalaryController extends Controller
{
    protected $salaryRepository;
    protected $employeeRepository;
    protected $userRepo;
    public function __construct(
        SalaryRepositoryInterface $salaryRepository,
        UserRepo $userRepo,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->salaryRepository = $salaryRepository;
        $this->userRepo = $userRepo;
        $this->employeeRepository = $employeeRepository;
    }

     
    /************* SALARY RECORDS ***********/

    public function index(Request $request)
    {
        $filters = $request->only(['pay_period', 'employee_id', 'payment_status', 'month', 'year']);
        
        $d['salary_records'] = $this->salaryRepository->getAllSalaryRecords($filters);
        $d['employees'] = $this->employeeRepository->getActiveEmployees();
        $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
        $d['pay_periods'] = $this->getPayPeriods();
        
        // Add user types - using UserRepo
        $d['user_types'] = $this->userRepo->getAllTypes();
        
        return view('pages.support_team.finance.salaries.index', $d);
    }

    public function index222(Request $request)
    {
        $filters = $request->only(['pay_period', 'employee_id', 'payment_status', 'month', 'year']);
        
        $d['salary_records'] = $this->salaryRepository->getAllSalaryRecords($filters);
        $d['employees'] = $this->employeeRepository->getActiveEmployees();
        $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
        $d['pay_periods'] = $this->getPayPeriods(); // Add this line
        
        return view('pages.support_team.finance.salaries.index', $d);
    }

    // Add this method to generate pay periods
    private function getPayPeriods()
    {
        $periods = [];
        $current = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();
        
        while ($current <= $end) {
            $periods[] = $current->format('Y-m');
            $current->addMonth();
        }
        
        return array_reverse($periods); // Show latest first
    }

    public function create()
    {
        $d['employees'] = $this->employeeRepository->getActiveEmployees();
        $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
        return view('pages.support_team.salaries.create', $d);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period' => 'required|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*' => 'numeric|min:0',
            'bonuses' => 'nullable|array',
            'bonuses.*' => 'numeric|min:0',
            'payment_date' => 'required|date',
            'payment_status' => 'required|in:pending,paid,failed',
            'remarks' => 'nullable|string',
        ]);

        try {
            $salaryRecord = $this->salaryRepository->createSalaryRecord($validated);
            return Qs::jsonStoreOk();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create salary record'
            ], 500);
        }
    }

    public function show($id)
    {
        $d['salary_record'] = $this->salaryRepository->getSalaryRecordById($id);
        
        if (!$d['salary_record']) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        return view('pages.support_team.salaries.show', $d);
    }

    public function edit($id)
    {
        $d['salary_record'] = $this->salaryRepository->getSalaryRecordById($id);
        $d['employees'] = $this->employeeRepository->getActiveEmployees();
        
        if (!$d['salary_record']) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        return view('pages.support_team.salaries.edit', $d);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'basic_salary' => 'sometimes|required|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*' => 'numeric|min:0',
            'bonuses' => 'nullable|array',
            'bonuses.*' => 'numeric|min:0',
            'payment_date' => 'sometimes|required|date',
            'payment_status' => 'sometimes|required|in:pending,paid,failed',
            'remarks' => 'nullable|string',
        ]);

        try {
            $success = $this->salaryRepository->updateSalaryRecord($id, $validated);
            
            if (!$success) {
                return response()->json([
                    'error' => true,
                    'message' => 'Salary record not found'
                ], 404);
            }
            
            return Qs::jsonUpdateOk();
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to update salary record'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $success = $this->salaryRepository->deleteSalaryRecord($id);
        
        if (!$success) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        
        return back()->with('flash_success', __('msg.delete_ok'));
    }

    /************* SALARY LEVELS ***********/

    public function salaryLevelsIndex()
    {
        $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
        return view('pages.support_team.salaries.levels_index', $d);
    }

    public function salaryLevelsCreate()
    {
        return view('pages.support_team.salaries.levels_create');
    }

    public function createLevel()
    {
        $d['user_types'] = $this->userRepo->getAllTypes();
        return view('pages.support_team.finance.salaries.modals.add_salary_level', $d);
    }

    public function salaryLevelsStore(Request $request)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255|unique:salary_levels,level_name',
            'description' => 'nullable|string',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gt:min_salary',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $salaryLevel = $this->salaryRepository->createSalaryLevel($validated);
            return Qs::jsonStoreOk();
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create salary level'
            ], 500);
        }
    }

    public function salaryLevelsEdit($id)
    {
        $d['salary_level'] = $this->salaryRepository->getSalaryLevelById($id);
        
        if (!$d['salary_level']) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        return view('pages.support_team.salaries.levels_edit', $d);
    }

    public function salaryLevelsUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255|unique:salary_levels,level_name,' . $id,
            'description' => 'nullable|string',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0|gt:min_salary',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $success = $this->salaryRepository->updateSalaryLevel($id, $validated);
            
            if (!$success) {
                return response()->json([
                    'error' => true,
                    'message' => 'Salary level not found'
                ], 404);
            }
            
            return Qs::jsonUpdateOk();
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to update salary level'
            ], 500);
        }
    }

    public function salaryLevelsDestroy($id)
    {
        try {
            $success = $this->salaryRepository->deleteSalaryLevel($id);
            
            if (!$success) {
                return back()->with('flash_danger', __('msg.rnf'));
            }
            
            return back()->with('flash_success', __('msg.delete_ok'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('flash_danger', $e->getMessage());
        }
    }

    /************* UTILITY METHODS ***********/

    public function calculateNetSalary(Request $request)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'deductions' => 'nullable|array',
            'deductions.*' => 'numeric|min:0',
            'bonuses' => 'nullable|array',
            'bonuses.*' => 'numeric|min:0',
        ]);

        $netSalary = $this->salaryRepository->calculateNetSalary(
            $validated['basic_salary'],
            $validated['deductions'] ?? [],
            $validated['bonuses'] ?? []
        );

        return response()->json([
            'success' => true,
            'net_salary' => $netSalary,
            'total_deductions' => array_sum($validated['deductions'] ?? []),
            'total_bonuses' => array_sum($validated['bonuses'] ?? []),
        ]);
    }

    public function generatePayslip($id)
    {
        try {
            $payslip = $this->salaryRepository->generatePayslip($id);
            return response()->json(['success' => true, 'payslip' => $payslip]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function bulkSalaryProcessing(Request $request)
    {
        $validated = $request->validate([
            'employee_data' => 'required|array',
            'employee_data.*.employee_id' => 'required|exists:employees,id',
            'employee_data.*.basic_salary' => 'required|numeric|min:0',
            'employee_data.*.deductions' => 'nullable|array',
            'employee_data.*.bonuses' => 'nullable|array',
            'pay_period' => 'required|string|max:50',
        ]);

        try {
            $results = $this->salaryRepository->processBulkSalaries(
                $validated['employee_data'],
                $validated['pay_period']
            );

            return response()->json([
                'success' => true,
                'message' => 'Bulk salary processing completed',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk salary processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function salarySummary(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:50',
        ]);

        try {
            $summary = $this->salaryRepository->getSalarySummaryByPeriod($validated['period']);
            return response()->json(['success' => true, 'summary' => $summary]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function filterStructures(Request $request)
{
    $filters = $request->only(['salary_level_id', 'component_type', 'is_active']);
    
    $query = SalaryStructure::with('salaryLevel');
    
    if (!empty($filters['salary_level_id'])) {
        $query->where('salary_level_id', $filters['salary_level_id']);
    }
    
    if (!empty($filters['component_type'])) {
        $query->where('component_type', $filters['component_type']);
    }
    
    if (isset($filters['is_active'])) {
        $query->where('is_active', $filters['is_active']);
    }
    
    $structures = $query->get();
    
    $html = view('pages.support_team.finance.salaries.partials.salary_structures_table', compact('structures'))->render();
    
    return response()->json([
        'success' => true,
        'html' => $html
    ]);
}

public function filterDeductionsBonuses(Request $request)
{
    try {
        $filters = $request->only(['type', 'employee_id', 'status', 'recurring']);
        
        $deductionsBonuses = $this->salaryRepository->getAllDeductionsBonuses()
            ->when(!empty($filters['type']), function ($query) use ($filters) {
                return $query->where('type', $filters['type']);
            })
            ->when(!empty($filters['employee_id']), function ($query) use ($filters) {
                return $query->where('employee_id', $filters['employee_id']);
            })
            ->when(!empty($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(!empty($filters['recurring']), function ($query) use ($filters) {
                return $query->where('recurring', $filters['recurring']);
            });

        $html = view('pages.support_team.finance.salaries.partials.deductions_bonuses_table', [
            'deductions_bonuses' => $deductionsBonuses
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to filter deductions and bonuses: ' . $e->getMessage()
        ], 500);
    }
}
public function storeLevel(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:salary_levels,name',
        'user_type_id' => 'required|exists:user_types,id', // Add this validation
        'base_salary' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'is_active' => 'boolean', // Changed from 'status' to 'is_active'
    ]);

    try {
        // Convert checkbox value to proper boolean
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $salaryLevel = $this->salaryRepository->createSalaryLevel($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('salary.salary_level_created'),
                'data' => $salaryLevel
            ]);
        } else {
            return redirect()->back()->with('flash_success', __('salary.salary_level_created'));
        }
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create salary level: ' . $e->getMessage()
            ], 500);
        } else {
            return redirect()->back()->with('flash_danger', 'Failed to create salary level: ' . $e->getMessage());
        }
    }
}
public function storeLevel111(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:salary_levels,name',
        'description' => 'nullable|string',
        'base_salary' => 'required|numeric|min:0',
        
        'status' => 'required|in:active,inactive',
    ]);

    try {
        $salaryLevel = $this->salaryRepository->createSalaryLevel($validated);
        return response()->json([
            'success' => true,
            'message' => __('salary.salary_level_created')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create salary level: ' . $e->getMessage()
        ], 500);
    }
}
public function storeDeductionsBonuses(Request $request)
{
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'type' => 'required|in:deduction,bonus',
        'description' => 'required|string|max:500',
        'amount' => 'required|numeric|min:0',
        'effective_date' => 'required|date',
        'end_date' => 'nullable|date|after:effective_date',
        'recurring' => 'required|in:one_time,monthly,yearly',
        'status' => 'required|in:active,inactive',
    ]);

    try {
        $deductionBonus = $this->salaryRepository->createDeductionsBonuses($validated);
        return response()->json([
            'success' => true,
            'message' => __('salary.deduction_bonus_created')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create deduction/bonus: ' . $e->getMessage()
        ], 500);
    }
}
}