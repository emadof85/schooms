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
use Illuminate\Support\Facades\Log;
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

    private function isRTL()
    {
        return app()->getLocale() === 'ar';
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
// Add these methods to your SalaryController

public function editLevel($editId)
{
    try {
       // Log::info('edit:'.$editId);
        $level = $this->salaryRepository->getSalaryLevelById($editId);
        
        if (!$level) {
            return response()->json([
                'success' => false,
                'message' => __('salary.salary_level_not_found')
            ], 404);
        }

        $d['level'] = $level;
        $d['user_types'] = $this->userRepo->getAllTypes();
        
        $html = view('pages.support_team.finance.salaries.modals.edit_salary_level', $d)->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load edit form: ' . $e->getMessage()
        ], 500);
    }
}
public function updateLevel(Request $request, $updateId)
{
    $id= $updateId;
  

    // Validate input
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:salary_levels,name,' . $id,
        'user_type_id' => 'required|exists:user_types,id',
        'base_salary' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ]);

   

    try {
        // Convert checkbox value to proper boolean
        $isActive = $request->has('is_active') ? true : false;
        $validated['is_active'] = $isActive;

       
        // Check if salary level exists before update
        $existingLevel = $this->salaryRepository->getSalaryLevelById($id);
        if (!$existingLevel) {
          
            return response()->json([
                'success' => false,
                'message' => __('salary.salary_level_not_found')
            ], 404);
        }

     

        // Perform the update
        $success = $this->salaryRepository->updateSalaryLevel($id, $validated);

        if (!$success) {
        
            return response()->json([
                'success' => false,
                'message' => __('salary.salary_level_not_found')
            ], 404);
        }

   

        return response()->json([
            'success' => true,
            'message' => __('salary.salary_level_updated'),
            'data' => [
                'id' => $id,
                'name' => $validated['name'],
                'base_salary' => $validated['base_salary'],
                'is_active' => $validated['is_active']
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
      

        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Illuminate\Database\QueryException $e) {
     

        return response()->json([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);

    } catch (\Exception $e) {
      
        return response()->json([
            'success' => false,
            'message' => 'Failed to update salary level: ' . $e->getMessage()
        ], 500);
    }
}
 

public function getLevelsByUserType($userTypeId)
{
    try {
        $levels = $this->salaryRepository->getSalaryLevelsByUserType($userTypeId);
        
        return response()->json([
            'success' => true,
            'data' => $levels
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch salary levels: ' . $e->getMessage()
        ], 500);
    }
}

public function bulkAssignLevels(Request $request)
{
    $validated = $request->validate([
        'user_type_id' => 'required|exists:user_types,id',
        'salary_level_id' => 'required|exists:salary_levels,id',
    ]);

    try {
        $mapping = [
            $validated['user_type_id'] => $validated['salary_level_id']
        ];

        $results = $this->salaryRepository->bulkAssignSalaryLevelsByUserType($mapping);

        return response()->json([
            'success' => true,
            'message' => __('salary.bulk_assignment_success', ['count' => $results['successful']]),
            'data' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to bulk assign salary levels: ' . $e->getMessage()
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
public function getLevels(Request $request)
{
    try {
        $levels = $this->salaryRepository->getAllLevels();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $levels
            ]);
        }
        
        return $levels;
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch salary levels: ' . $e->getMessage()
            ], 500);
        }
        
        throw $e;
    }
}

 

public function destroyLevel(Request $request, $levelId)
{
    try {
       
        // Check if ID is coming from route parameters
        $routeId = $request->route('id');
     
        // Validate ID
        if (empty($levelId) || $levelId === 'undefined' || $levelId === 'null') {
           
            return response()->json([
                'success' => false,
                'message' => 'Invalid salary level ID'
            ], 400);
        }
        
        // Convert to integer and use the actual ID from the parameter
        $id = (int) $levelId;
         
        // FIX: Use the $id parameter instead of hardcoded 1
        $result = $this->salaryRepository->deleteSalaryLevel($levelId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => __('salary.salary_level_not_found')
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('salary.salary_level_deleted')
        ]);

    } catch (\Exception $e) {
        \Log::error("Error deleting salary level ID {$id}: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete salary level: ' . $e->getMessage()
        ], 500);
    }
}
///////////////////////Salary Structure///////////////////////////////////////////
 // Salary Structures Methods
 
 public function getStructures(Request $request)
 {
     
 
     try {
         $filters = $request->only(['salary_level_id', 'is_active']);
         
         // Use repository method for filtering
         $salary_structures = $this->salaryRepository->getFilteredStructures($filters);

 
         $data = [
             'salary_structures' => $salary_structures,
             'salary_levels' => $this->salaryRepository->getAllSalaryLevels(),
             'is_rtl' => $this->isRTL()
         ];
  
 
         if ($request->ajax() || $request->has('partial')) {
             
             
             try {
                 $html = view('pages.support_team.finance.salaries.partials.salary_structures_table', $data)->render();
                 
                 
                 
                 return response()->json([
                     'success' => true,
                     'html' => $html
                 ]);
             } catch (\Exception $viewException) {
                 \Log::error('❌ Error rendering partial view: ' . $viewException->getMessage(), [
                     'exception' => $viewException->getTraceAsString()
                 ]);
                 
                 $fallbackHtml = '<div class="alert alert-danger">Error rendering salary structures: ' . $viewException->getMessage() . '</div>';
                 
                 return response()->json([
                     'success' => false,
                     'html' => $fallbackHtml,
                     'message' => 'View rendering failed'
                 ], 500);
             }
         }
 
         \Log::info('🌐 Full page request, returning complete view');
         return view('pages.support_team.finance.salaries.structures', $data);
          
     } catch (\Exception $e) {
         \Log::error('💥 Error in getStructures method: ' . $e->getMessage(), [
             'exception' => $e->getTraceAsString(),
             'filters' => $filters ?? [],
             'request_data' => $request->all()
         ]);
         
         $errorMessage = 'Failed to load salary structures: ' . $e->getMessage();
         
         if ($request->ajax() || $request->has('partial')) {
             $errorHtml = '<div class="alert alert-danger">' . $errorMessage . '</div>';
             
             return response()->json([
                 'success' => false,
                 'message' => $errorMessage,
                 'html' => $errorHtml
             ], 500);
         }
         
         return back()->with('flash_danger', $errorMessage);
     }
 }
 public function getStructures22(Request $request)
 {
    Log::info('Error getting salary structures: '  );
     try {
         $filters = $request->only(['salary_level_id', 'is_active']);
         
         // Use repository method for filtering
         $salary_structures = $this->salaryRepository->getFilteredStructures($filters);
 
         $data = [
             'salary_structures' => $salary_structures,
             'salary_levels' => $this->salaryRepository->getAllSalaryLevels(),
             'is_rtl' => $this->isRTL()
         ];
 
         if ($request->ajax() || $request->has('partial')) {
             $html = view('pages.support_team.finance.salaries.partials.salary_structures_table', $data)->render();
             return response()->json([
                 'success' => true,
                 'html' => $html
             ]);
         }
 
         return view('pages.support_team.finance.salaries.structures', $data);
         
     } catch (\Exception $e) {
         Log::error('Error getting salary structures: ' . $e->getMessage());
         
         if ($request->ajax() || $request->has('partial')) {
             return response()->json([
                 'success' => false,
                 'message' => 'Failed to load salary structures',
                 'html' => '<div class="alert alert-danger">Failed to load salary structures: ' . $e->getMessage() . '</div>'
             ], 500);
         }
         
         return back()->with('flash_danger', 'Failed to load salary structures');
     }
 }

 public function createStructure()
 {
     try {
         $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
         $d['is_rtl'] = $this->isRTL();
         
         $html = view('pages.support_team.finance.salaries.modals.add_salary_structure', $d)->render();
         
         return response()->json([
             'success' => true,
             'html' => $html
         ]);
     } catch (\Exception $e) {
         Log::error('Error loading create structure form: ' . $e->getMessage());
         return response()->json([
             'success' => false,
             'message' => 'Failed to load form'
         ], 500);
     }
 }

 public function storeStructure(Request $request)
 {
     Log::debug('🎯 CONTROLLER: Storing salary structure', $request->all());
 
     $validated = $request->validate([
         'salary_level_id' => 'required|exists:salary_levels,id',
         'basic_salary' => 'required|numeric|min:0',
         'housing_allowance' => 'nullable|numeric|min:0',
         'transport_allowance' => 'nullable|numeric|min:0',
         'medical_allowance' => 'nullable|numeric|min:0',
         'other_allowances' => 'nullable|numeric|min:0',
         'effective_date' => 'required|date',
         'is_active' => 'required|boolean',
     ]);
 
     try {
         // Calculate total salary
         $validated['total_salary'] = 
             $validated['basic_salary'] + 
             ($validated['housing_allowance'] ?? 0) + 
             ($validated['transport_allowance'] ?? 0) + 
             ($validated['medical_allowance'] ?? 0) + 
             ($validated['other_allowances'] ?? 0);
 
         // Add user_id to the data
         $validated['user_id'] = auth()->id();
 
         $salaryStructure = $this->salaryRepository->createSalaryStructure($validated);
 
          
 
         return response()->json([
             'success' => true,
             'message' => __('salary.salary_structure_created'),
             'data' => $salaryStructure
         ]);
     } catch (\Exception $e) {
         Log::error('💥 CONTROLLER: Error creating salary structure', [
             'error' => $e->getMessage(),
             'trace' => $e->getTraceAsString()
         ]);
 
         return response()->json([
             'success' => false,
             'message' => 'Failed to create salary structure: ' . $e->getMessage()
         ], 500);
     }
 }
 
  

 public function editStructure($structureEditId)
 {
    $id= $structureEditId;
     try {
         Log::debug('🎯 CONTROLLER: Loading edit structure form', ['id' => $id]);

         $structure = $this->salaryRepository->getSalaryStructureById($id);
         
         if (!$structure) {
             Log::warning('❌ CONTROLLER: Salary structure not found', ['id' => $id]);
             return response()->json([
                 'success' => false,
                 'message' => __('salary.salary_structure_not_found')
             ], 404);
         }

         $d['structure'] = $structure;
         $d['salary_levels'] = $this->salaryRepository->getAllSalaryLevels();
         $d['is_rtl'] = $this->isRTL();

         Log::debug('✅ CONTROLLER: Salary structure found', [
             'id' => $structure->id,
             'name' => $structure->component_name
         ]);

         $html = view('pages.support_team.finance.salaries.modals.edit_salary_structure', $d)->render();
         
         return response()->json([
             'success' => true,
             'html' => $html,
             'structure' => $structure
         ]);
     } catch (\Exception $e) {
         Log::error('💥 CONTROLLER: Error loading edit structure form', [
             'id' => $id,
             'error' => $e->getMessage()
         ]);

         return response()->json([
             'success' => false,
             'message' => 'Failed to load edit form: ' . $e->getMessage()
         ], 500);
     }
 }

 public function updateStructure(Request $request, $structuresUpdateId)
 {
     $id = $structuresUpdateId;
     Log::debug('🎯 CONTROLLER: Updating salary structure', [
         'id' => $id,
         'data' => $request->all()
     ]);
 
     $validated = $request->validate([
         'salary_level_id' => 'required|exists:salary_levels,id',
         'basic_salary' => 'required|numeric|min:0',
         'housing_allowance' => 'nullable|numeric|min:0',
         'transport_allowance' => 'nullable|numeric|min:0',
         'medical_allowance' => 'nullable|numeric|min:0',
         'other_allowances' => 'nullable|numeric|min:0',
         'effective_date' => 'required|date',
         'is_active' => 'required|boolean',
     ]);
 
     try {
         // Calculate total salary (same logic as storeStructure)
         $validated['total_salary'] = 
             $validated['basic_salary'] + 
             ($validated['housing_allowance'] ?? 0) + 
             ($validated['transport_allowance'] ?? 0) + 
             ($validated['medical_allowance'] ?? 0) + 
             ($validated['other_allowances'] ?? 0);
 
         $success = $this->salaryRepository->updateSalaryStructure($id, $validated);
 
         if (!$success) {
             Log::warning('⚠️ CONTROLLER: Salary structure not found for update', ['id' => $id]);
             
             return response()->json([
                 'success' => false,
                 'message' => __('salary.salary_structure_not_found')
             ], 404);
         }
 
         Log::debug('✅ CONTROLLER: Salary structure updated successfully', [
             'id' => $id,
             'total_salary' => $validated['total_salary'],
             'basic_salary' => $validated['basic_salary']
         ]);
 
         return response()->json([
             'success' => true,
             'message' => __('salary.salary_structure_updated')
         ]);
     } catch (\Exception $e) {
         Log::error('💥 CONTROLLER: Error updating salary structure', [
             'id' => $id,
             'error' => $e->getMessage(),
             'trace' => $e->getTraceAsString()
         ]);
 
         return response()->json([
             'success' => false,
             'message' => 'Failed to update salary structure: ' . $e->getMessage()
         ], 500);
     }
 }
 public function destroyStructure($id)
 {
     try {
        Log::info('❌ CONTROLLER: Salary structure not found for deletion', ['id' => $id]);

         $success = $this->salaryRepository->deleteSalaryStructure($id);

         if (!$success) {
             Log::warning('❌ CONTROLLER: Salary structure not found for deletion', ['id' => $id]);
             return response()->json([
                 'success' => false,
                 'message' => __('salary.salary_structure_not_found')
             ], 404);
         }

         

         return response()->json([
             'success' => true,
             'message' => __('salary.salary_structure_deleted')
         ]);
     } catch (\Exception $e) {
         Log::error('💥 CONTROLLER: Error deleting salary structure', [
             'id' => $id,
             'error' => $e->getMessage()
         ]);

         return response()->json([
             'success' => false,
             'message' => 'Failed to delete salary structure: ' . $e->getMessage()
         ], 500);
     }
 }

 /**
     * Filter salary structures
     */
    public function filterStructures(Request $request)
    {
         
        
        try {
            $filters = $request->only(['salary_level_id', 'is_active']);
            
            // Use repository to get filtered structures
            $structures = $this->salaryRepository->getFilteredStructures($filters);
            
            
            $html = view('pages.support_team.finance.salaries.partials.salary_structures_table', [
                'salary_structures' => $structures
            ])->render();
            
           
            return response()->json([
                'success' => true,
                'html' => $html,
                'records_count' => $structures->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Error filtering salary structures: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'filters' => $filters ?? []
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to filter salary structures',
                'html' => '<div class="alert alert-danger">Failed to filter salary structures</div>'
            ], 500);
        }
    }
 

}