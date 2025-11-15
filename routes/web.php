<?php
 
Auth::routes();

Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

//Route::get('/test', 'TestController@index')->name('test');
Route::get('/privacy-policy', 'HomeController@privacy_policy')->name('privacy_policy');
Route::get('/terms-of-use', 'HomeController@terms_of_use')->name('terms_of_use');


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'HomeController@dashboard')->name('home');
    Route::get('/home', 'HomeController@dashboard')->name('home_alt');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    Route::get('/livewire-dashboard', function () {
        return view('livewire-dashboard');
    })->name('livewire.dashboard');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::group(['prefix' => 'my_account'], function() {
        Route::get('/', 'MyAccountController@edit_profile')->name('my_account');
        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password', 'MyAccountController@change_pass')->name('my_account.change_pass');
    });

    /*************** Support Team *****************/
    Route::group(['namespace' => 'SupportTeam',], function(){

        /*************** Students *****************/
        Route::group(['prefix' => 'students'], function(){
            Route::get('reset_pass/{st_id}', 'StudentRecordController@reset_pass')->name('st.reset_pass');
            Route::get('graduated', 'StudentRecordController@graduated')->name('students.graduated');
            Route::put('not_graduated/{id}', 'StudentRecordController@not_graduated')->name('st.not_graduated');
            Route::get('list/{class_id}', 'StudentRecordController@listByClass')->name('students.list')->middleware('teamSAT');

            /* Promotions */
            Route::post('promote_selector', 'PromotionController@selector')->name('students.promote_selector');
            Route::get('promotion/manage', 'PromotionController@manage')->name('students.promotion_manage');
            Route::delete('promotion/reset/{pid}', 'PromotionController@reset')->name('students.promotion_reset');
            Route::delete('promotion/reset_all', 'PromotionController@reset_all')->name('students.promotion_reset_all');
            Route::get('promotion/{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion')->name('students.promotion');
            Route::post('promote/{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote')->name('students.promote');

        });

        /*************** Users *****************/
        Route::group(['prefix' => 'users'], function(){
            Route::get('reset_pass/{id}', 'UserController@reset_pass')->name('users.reset_pass');
        });

        /*************** TimeTables *****************/
        Route::group(['prefix' => 'timetables'], function(){
            Route::get('/', 'TimeTableController@index')->name('tt.index');

            Route::group(['middleware' => 'teamSA'], function() {
                Route::post('/', 'TimeTableController@store')->name('tt.store');
                Route::put('/{tt}', 'TimeTableController@update')->name('tt.update');
                Route::delete('/{tt}', 'TimeTableController@delete')->name('tt.delete');
            });

            /*************** TimeTable Records *****************/
            Route::group(['prefix' => 'records'], function(){

                Route::group(['middleware' => 'teamSA'], function(){
                    Route::get('manage/{ttr}', 'TimeTableController@manage')->name('ttr.manage');
                    Route::post('/', 'TimeTableController@store_record')->name('ttr.store');
                    Route::get('edit/{ttr}', 'TimeTableController@edit_record')->name('ttr.edit');
                    Route::put('/{ttr}', 'TimeTableController@update_record')->name('ttr.update');
                });

                Route::get('show/{ttr}', 'TimeTableController@show_record')->name('ttr.show');
                Route::get('print/{ttr}', 'TimeTableController@print_record')->name('ttr.print');
                Route::delete('/{ttr}', 'TimeTableController@delete_record')->name('ttr.destroy');

            });

            /*************** Time Slots *****************/
            Route::group(['prefix' => 'time_slots', 'middleware' => 'teamSA'], function(){
                Route::post('/', 'TimeTableController@store_time_slot')->name('ts.store');
                Route::post('/use/{ttr}', 'TimeTableController@use_time_slot')->name('ts.use');
                Route::get('edit/{ts}', 'TimeTableController@edit_time_slot')->name('ts.edit');
                Route::delete('/{ts}', 'TimeTableController@delete_time_slot')->name('ts.destroy');
                Route::put('/{ts}', 'TimeTableController@update_time_slot')->name('ts.update');
            });

        });

        /*************** Payments *****************/
        Route::group(['prefix' => 'payments'], function(){

            Route::get('manage/{class_id?}', 'PaymentController@manage')->name('payments.manage');
            Route::get('invoice/{id}/{year?}', 'PaymentController@invoice')->name('payments.invoice');
            Route::get('receipts/{id}', 'PaymentController@receipts')->name('payments.receipts');
            Route::get('pdf_receipts/{id}', 'PaymentController@pdf_receipts')->name('payments.pdf_receipts');
            Route::post('select_year', 'PaymentController@select_year')->name('payments.select_year');
            Route::post('select_class', 'PaymentController@select_class')->name('payments.select_class');
            Route::delete('reset_record/{id}', 'PaymentController@reset_record')->name('payments.reset_record');
            Route::post('pay_now/{id}', 'PaymentController@pay_now')->name('payments.pay_now');
        });

        // Finance Routes
     // Finance Routes
        /*************** Finance *****************/
        Route::group(['prefix' => 'finance'], function() {

            /*************** Dashboard *****************/
            Route::get('dashboard', 'FinanceController@dashboard')->name('finance.dashboard');
            Route::get('dashboard/data', 'FinanceController@getDashboardData')->name('finance.dashboard.data');
            
           /*************** Salaries *****************/
        Route::group(['prefix' => 'salaries'], function() {
            // Salary Records
            Route::get('/', 'SalaryController@index')->name('finance.salaries.index');
            Route::get('create', 'SalaryController@create')->name('finance.salaries.create');
            Route::post('store', 'SalaryController@store')->name('finance.salaries.store');
            Route::get('{id}/show', 'SalaryController@show')->name('finance.salaries.show');
            Route::get('{id}/edit', 'SalaryController@edit')->name('finance.salaries.edit');
            Route::put('{id}/update', 'SalaryController@update')->name('finance.salaries.update');
            Route::delete('{id}/destroy', 'SalaryController@destroy')->name('finance.salaries.destroy');
            
            // Salary Levels - Add new routes for user type functionality
            Route::get('levels', 'SalaryController@getLevels')->name('finance.salaries.levels');
            Route::get('levels/create', 'SalaryController@createLevel')->name('finance.salaries.levels.create');
            Route::post('levels/store', 'SalaryController@storeLevel')->name('finance.salaries.levels.store');
            Route::get('levels/{id}/edit', 'SalaryController@editLevel')->name('finance.salaries.levels.edit');
            Route::put('levels/{id}/update', 'SalaryController@updateLevel')->name('finance.salaries.levels.update');
            Route::delete('levels/{id}/destroy', 'SalaryController@destroyLevel')->name('finance.salaries.levels.destroy');
            
            // NEW: Salary Levels by User Type
            Route::get('levels/by-user-type/{userTypeId}', 'SalaryController@getLevelsByUserType')->name('finance.salaries.levels.by_user_type');
            Route::post('levels/bulk-assign', 'SalaryController@bulkAssignLevels')->name('finance.salaries.levels.bulk_assign');
            Route::put('employees/{employeeId}/salary-level', 'SalaryController@updateEmployeeSalaryLevel')->name('finance.salaries.employees.update_level');
            
            // Salary Structures
            Route::get('structures', 'SalaryController@getStructures')->name('finance.salaries.structures');
            Route::get('structures/create', 'SalaryController@createStructure')->name('finance.salaries.structures.create');
            Route::post('structures/store', 'SalaryController@storeStructure')->name('finance.salaries.structures.store');
            Route::get('structures/{id}/edit', 'SalaryController@editStructure')->name('finance.salaries.structures.edit');
            Route::put('structures/{id}/update', 'SalaryController@updateStructure')->name('finance.salaries.structures.update');
            Route::delete('structures/{id}/destroy', 'SalaryController@destroyStructure')->name('finance.salaries.structures.destroy');
            Route::get('structures/filter', 'SalaryController@filterStructures')->name('finance.salaries.structures.filter');
            
            // NEW: Employee Salary Structure
            Route::get('employees/{employeeId}/salary-structure', 'SalaryController@getEmployeeSalaryStructure')->name('finance.salaries.employees.structure');
            
            // Deductions & Bonuses
            Route::get('deductions-bonuses', 'SalaryController@getDeductionsBonuses')->name('finance.salaries.deductions_bonuses');
            Route::get('deductions-bonuses/create', 'SalaryController@createDeductionsBonuses')->name('finance.salaries.deductions_bonuses.create');
            Route::post('deductions-bonuses/store', 'SalaryController@storeDeductionsBonuses')->name('finance.salaries.deductions_bonuses.store');
            Route::get('deductions-bonuses/{id}/edit', 'SalaryController@editDeductionsBonuses')->name('finance.salaries.deductions_bonuses.edit');
            Route::put('deductions-bonuses/{id}/update', 'SalaryController@updateDeductionsBonuses')->name('finance.salaries.deductions_bonuses.update');
            Route::delete('deductions-bonuses/{id}/destroy', 'SalaryController@destroyDeductionsBonuses')->name('finance.salaries.deductions_bonuses.destroy');
            Route::get('deductions-bonuses/filter', 'SalaryController@filterDeductionsBonuses')->name('finance.salaries.deductions_bonuses.filter');
            
            // Utility Routes
            Route::post('calculate-net-salary', 'SalaryController@calculateNetSalary')->name('finance.salaries.calculate_net_salary');
            Route::get('{id}/payslip', 'SalaryController@generatePayslip')->name('finance.salaries.payslip');
            Route::post('bulk-process', 'SalaryController@bulkSalaryProcessing')->name('finance.salaries.bulk_process');
            Route::get('summary', 'SalaryController@getSalarySummary')->name('finance.salaries.summary');
            Route::post('filter', 'SalaryController@filterSalaries')->name('finance.salaries.filter');
            Route::get('export', 'SalaryController@exportSalaries')->name('finance.salaries.export');
            
            // NEW: Employee Management Routes
            Route::get('employees', 'SalaryController@getEmployees')->name('finance.salaries.employees');
            Route::get('employees/{employeeId}/assign-level', 'SalaryController@assignSalaryLevelForm')->name('finance.salaries.employees.assign_level_form');
            Route::post('employees/{employeeId}/assign-level', 'SalaryController@assignSalaryLevel')->name('finance.salaries.employees.assign_level');
        });

            /*************** Incomes *****************/
            Route::group(['prefix' => 'incomes'], function() {
                Route::get('/', 'FinanceController@incomeIndex')->name('finance.incomes.index');
                Route::get('data', 'FinanceController@getIncomes')->name('finance.incomes.data');
                Route::post('store', 'FinanceController@storeIncome')->name('finance.incomes.store');
                
                // Specific routes with different parameter names to avoid conflicts
                Route::get('edit/{incomeId}', 'FinanceController@editIncome')->name('finance.incomes.edit');
                Route::get('get/{incomeId}', 'FinanceController@getIncome')->name('finance.incomes.get');
                Route::put('update/{incomeId}', 'FinanceController@updateIncome')->name('finance.incomes.update');
                Route::delete('delete/{incomeId}', 'FinanceController@deleteIncome')->name('finance.incomes.delete');
                
                // Export routes
                Route::get('export/excel', 'FinanceController@exportExcel')->name('finance.incomes.export.excel');
                Route::get('export/pdf', 'FinanceController@exportPdf')->name('finance.incomes.export.pdf');
            });
            
            /*************** Expenses *****************/
            Route::group(['prefix' => 'expenses'], function() {
                Route::get('/', 'FinanceController@expenseIndex')->name('finance.expenses.index');
                Route::get('data', 'FinanceController@getExpenses')->name('finance.expenses.data');
                Route::post('store', 'FinanceController@storeExpense')->name('finance.expenses.store');
                Route::get('edit/{expenseId}', 'FinanceController@editExpense')->name('finance.expenses.edit');
                Route::put('update/{expenseId}', 'FinanceController@updateExpense')->name('finance.expenses.update');
                Route::delete('{id}', 'FinanceController@deleteExpense')->name('finance.expenses.delete');
            });
            
            /*************** Deductions & Bonuses *****************/
            Route::group(['prefix' => 'deductions-bonuses'], function() {
                Route::get('/', 'SalaryController@deductionsBonuses')->name('finance.deductions-bonuses.index');
                Route::get('data', 'SalaryController@getDeductionsBonuses')->name('finance.deductions-bonuses.data');
                Route::post('/', 'SalaryController@storeDeductionBonus')->name('finance.deductions-bonuses.store');
                Route::put('{id}', 'SalaryController@updateDeductionBonus')->name('finance.deductions-bonuses.update');
                Route::delete('{id}', 'SalaryController@deleteDeductionBonus')->name('finance.deductions-bonuses.delete');
            });
             /*************** Category Management *****************/
            Route::group(['prefix' => 'categories'], function() {
                // Income Categories
                Route::group(['prefix' => 'income'], function() {
                    Route::get('/', 'FinanceController@incomeCategoryIndex')->name('finance.categories.income.index');
                    Route::get('data', 'FinanceController@getIncomeCategories')->name('finance.categories.income.data');
                    Route::post('/', 'FinanceController@storeIncomeCategory')->name('finance.categories.income.store');
                    Route::get('edit/{categoryId}', 'FinanceController@editIncomeCategory')->name('finance.categories.income.edit');
                  // Route::get('{categoryId}', 'FinanceController@getIncomeCategory')->name('finance.categories.income.get');
                    Route::put('{categoryId}', 'FinanceController@updateIncomeCategory')->name('finance.categories.income.update');
                    Route::delete('{categoryId}', 'FinanceController@deleteIncomeCategory')->name('finance.categories.income.delete');
                });
                
                // Expense Categories
                Route::group(['prefix' => 'expense'], function() {
                    Route::get('/', 'FinanceController@expenseCategoryIndex')->name('finance.categories.expense.index');
                    Route::get('data', 'FinanceController@getExpenseCategories')->name('finance.categories.expense.data');
                    Route::post('/', 'FinanceController@storeExpenseCategory')->name('finance.categories.expense.store');
                    Route::put('{categoryId}', 'FinanceController@updateExpenseCategory')->name('finance.categories.expense.update');
                   
                    Route::get('edit/{categoryId}', 'FinanceController@editExpenseCategory')->name('finance.categories.income.edit');
                    Route::delete('{categoryId}', 'FinanceController@deleteExpenseCategory')->name('finance.categories.expense.delete');
                });
            });
             /*************** Reports *****************/
            Route::group(['prefix' => 'reports'], function() {
                Route::get('income-expense', 'FinanceController@incomeExpenseReport')->name('finance.reports.income-expense');
                Route::get('income-expense/data', 'FinanceController@getIncomeExpenseReport')->name('finance.reports.income-expense.data');
                Route::get('payroll', 'FinanceController@payrollReport')->name('finance.reports.payroll');
                Route::get('payroll/data', 'FinanceController@getPayrollReport')->name('finance.reports.payroll.data');
            });
        });
        /*************** Pins *****************/
        Route::group(['prefix' => 'pins'], function(){
            Route::get('create', 'PinController@create')->name('pins.create');
            Route::get('/', 'PinController@index')->name('pins.index');
            Route::post('/', 'PinController@store')->name('pins.store');
            Route::get('enter/{id}', 'PinController@enter_pin')->name('pins.enter');
            Route::post('verify/{id}', 'PinController@verify')->name('pins.verify');
            Route::delete('/', 'PinController@destroy')->name('pins.destroy');
        });

        /*************** Marks *****************/
        Route::group(['prefix' => 'marks'], function(){

           // FOR teamSA
            Route::group(['middleware' => 'teamSA'], function(){
                Route::get('batch_fix', 'MarkController@batch_fix')->name('marks.batch_fix');
                Route::put('batch_update', 'MarkController@batch_update')->name('marks.batch_update');
                Route::get('tabulation/{exam?}/{class?}/{sec_id?}', 'MarkController@tabulation')->name('marks.tabulation');
                Route::post('tabulation', 'MarkController@tabulation_select')->name('marks.tabulation_select');
                Route::get('tabulation/print/{exam}/{class}/{sec_id}', 'MarkController@print_tabulation')->name('marks.print_tabulation');
            });

            // FOR teamSAT
            Route::group(['middleware' => 'teamSAT'], function(){
                Route::get('/', 'MarkController@index')->name('marks.index');
                Route::get('manage/{exam}/{class}/{section}/{subject}', 'MarkController@manage')->name('marks.manage');
                Route::put('update/{exam}/{class}/{section}/{subject}', 'MarkController@update')->name('marks.update');
                Route::put('comment_update/{exr_id}', 'MarkController@comment_update')->name('marks.comment_update');
                Route::put('skills_update/{skill}/{exr_id}', 'MarkController@skills_update')->name('marks.skills_update');
                Route::post('selector', 'MarkController@selector')->name('marks.selector');
                Route::get('bulk/{class?}/{section?}', 'MarkController@bulk')->name('marks.bulk');
                Route::post('bulk', 'MarkController@bulk_select')->name('marks.bulk_select');
            });

            Route::get('select_year/{id}', 'MarkController@year_selector')->name('marks.year_selector');
            Route::post('select_year/{id}', 'MarkController@year_selected')->name('marks.year_select');
            Route::get('show/{id}/{year}', 'MarkController@show')->name('marks.show');
            Route::get('print/{id}/{exam_id}/{year}', 'MarkController@print_view')->name('marks.print');

        });

        Route::resource('students', 'StudentRecordController');
        Route::resource('users', 'UserController');
        Route::resource('classes', 'MyClassController');
        Route::resource('sections', 'SectionController');
        Route::resource('subjects', 'SubjectController');
        Route::resource('grades', 'GradeController');
        Route::resource('exams', 'ExamController');
        Route::resource('dorms', 'DormController');
        Route::resource('payments', 'PaymentController');
        Route::resource('field-definitions', 'FieldDefinitionController');
        Route::patch('field-definitions/{field_definition}/toggle', 'FieldDefinitionController@toggle')->name('field-definitions.toggle');

        /*************** Attendance *****************/
        Route::group(['prefix' => 'attendance'], function(){
            Route::get('/', 'AttendanceController@index')->name('attendance.index');
            Route::get('ui', 'AttendanceController@showPage')->name('attendance.ui');
            Route::post('/', 'AttendanceController@store')->name('attendance.store');
        });

        /*************** Reports *****************/
        Route::group(['prefix' => 'reports'], function(){
            Route::get('/', 'ReportsController@index')->name('reports.index');
            Route::post('daily-summary', 'ReportsController@dailySummary')->name('reports.daily_summary');
            Route::post('student-sheet', 'ReportsController@studentSheet')->name('reports.student_sheet');
        });

        /*************** Employee Management *****************/
        Route::resource('employees', 'EmployeeController');

        /*************** Communication Module *****************/
        Route::group(['prefix' => 'communication'], function(){
            Route::get('email', 'CommunicationController@email')->name('communication.email');
            Route::get('sms', 'CommunicationController@sms')->name('communication.sms');
            Route::post('send_sms', 'CommunicationController@sendSms')->name('communication.send_sms');
            Route::post('send_email', 'CommunicationController@sendEmail')->name('communication.send_email');
            Route::post('filter_recipients', 'CommunicationController@filterRecipients')->name('communication.filter_recipients');
            Route::post('get_classes', 'CommunicationController@getClasses')->name('communication.get_classes');
            Route::post('get_sections', 'CommunicationController@getSections')->name('communication.get_sections');
            Route::post('search_students', 'CommunicationController@searchStudents')->name('communication.search_students');
                // New Employee Communication Routes
            Route::get('employees/email', 'CommunicationController@employeeEmail')->name('communication.employees.email');
            Route::get('employees/sms', 'CommunicationController@employeeSms')->name('communication.employees.sms');
            Route::post('employees/send_sms', 'CommunicationController@sendEmployeeSms')->name('communication.employees.send_sms');
            Route::post('employees/send_email', 'CommunicationController@sendEmployeeEmail')->name('communication.employees.send_email');
            Route::post('employees/filter_recipients', 'CommunicationController@filterEmployeeRecipients')->name('communication.employees.filter_recipients');
            Route::post('employees/search_employees', 'CommunicationController@searchEmployees')->name('communication.employees.search_employees');
            Route::post('employees/get_by_department', 'CommunicationController@getEmployeesByDepartment')->name('communication.employees.get_by_department');
            Route::post('employees/get_by_type', 'CommunicationController@getEmployeesByType')->name('communication.employees.get_by_type');
        });

        /*************** Bus Management *****************/
        Route::get('bus-management', 'BusController@bus_management')->name('bus.management');

        Route::group(['prefix' => 'bus'], function(){

            // Buses
            Route::resource('buses', 'BusController');
            // Route::get('buses', 'BusController@buses')->name('bus.buses');
            // Route::get('buses/create', 'BusController@create_bus')->name('bus.buses.create');
            // Route::post('buses', 'BusController@store_bus')->name('bus.buses.store');
            // Route::get('buses/{id}/edit', 'BusController@edit_bus')->name('bus.buses.edit');
            // Route::put('buses/{id}', 'BusController@update_bus')->name('bus.buses.update');
            // Route::delete('buses/{id}', 'BusController@destroy_bus')->name('bus.buses.destroy');

            // Bus Drivers
            Route::get('drivers', 'BusController@bus_drivers')->name('bus.drivers');
            Route::get('drivers/create', 'BusController@create_bus_driver')->name('bus.drivers.create');
            Route::post('drivers', 'BusController@store_bus_driver')->name('bus.drivers.store');
            Route::get('drivers/{bus_driver}/edit', 'BusController@edit_bus_driver')->name('bus.drivers.edit');
            Route::put('drivers/{bus_driver}', 'BusController@update_bus_driver')->name('bus.drivers.update');
            Route::delete('drivers/{bus_driver}', 'BusController@destroy_bus_driver')->name('bus.drivers.destroy');

            // Bus Routes
            Route::get('routes', 'BusController@bus_routes')->name('bus.routes');
            Route::get('routes/create', 'BusController@create_bus_route')->name('bus.routes.create');
            Route::post('routes', 'BusController@store_bus_route')->name('bus.routes.store');
            Route::get('routes/{bus_route}/edit', 'BusController@edit_bus_route')->name('bus.routes.edit');
            Route::put('routes/{bus_route}', 'BusController@update_bus_route')->name('bus.routes.update');
            Route::delete('routes/{bus_route}', 'BusController@destroy_bus_route')->name('bus.routes.destroy');

            // Bus Stops
            Route::get('stops', 'BusController@all_bus_stops')->name('bus.stops');
            Route::get('routes/{route_id}/stops', 'BusController@bus_stops')->name('bus.route.stops');
            Route::get('routes/{route_id}/stops/create', 'BusController@create_bus_stop')->name('bus.stops.create');
            Route::post('stops', 'BusController@store_bus_stop')->name('bus.stops.store');
            Route::get('stops/{stop_id}/edit', 'BusController@edit_bus_stop')->name('bus.stops.edit');
            Route::put('stops/{stop_id}', 'BusController@update_bus_stop')->name('bus.stops.update');
            Route::delete('stops/{stop_id}', 'BusController@destroy_bus_stop')->name('bus.stops.destroy');

            // Bus Assignments
            Route::get('assignments', 'BusController@bus_assignments')->name('bus.assignments');
            Route::get('assignments/create', 'BusController@create_bus_assignment')->name('bus.assignments.create');
            Route::post('assignments', 'BusController@store_bus_assignment')->name('bus.assignments.store');
            Route::get('assignments/{assignment_id}/edit', 'BusController@edit_bus_assignment')->name('bus.assignments.edit');
            Route::put('assignments/{assignment_id}', 'BusController@update_bus_assignment')->name('bus.assignments.update');
            Route::delete('assignments/{assignment_id}', 'BusController@destroy_bus_assignment')->name('bus.assignments.destroy');

            // Student Bus Assignments
            Route::get('student-assignments', 'BusController@student_bus_assignments')->name('bus.student_assignments');
            Route::get('student-assignments/create', 'BusController@create_student_bus_assignment')->name('bus.student_assignments.create');
            Route::post('student-assignments', 'BusController@store_student_bus_assignment')->name('bus.student_assignments.store');
            Route::get('student-assignments/{student_assignment_id}/edit', 'BusController@edit_student_bus_assignment')->name('bus.student_assignments.edit');
            Route::put('student-assignments/{student_assignment_id}', 'BusController@update_student_bus_assignment')->name('bus.student_assignments.update');
            Route::delete('student-assignments/{student_assignment_id}', 'BusController@destroy_student_bus_assignment')->name('bus.student_assignments.destroy');
            Route::get('student/{student_id}/assignments', 'BusController@student_bus_assignments_by_student')->name('bus.student.assignments');
        });

    });

    /************************ AJAX ****************************/
    Route::group(['prefix' => 'ajax'], function() {
        Route::get('get_class_sections/{class_id}', 'AjaxController@get_class_sections')->name('get_class_sections');
        Route::get('get_class_subjects/{class_id}', 'AjaxController@get_class_subjects')->name('get_class_subjects');
        Route::get('get_educational_stage_classes/{educational_stage_id}', 'AjaxController@get_educational_stage_classes')->name('get_educational_stage_classes');
        Route::post('filter_students', 'AjaxController@filterStudents')->name('filter_students');
    });

});

/************************ SUPER ADMIN ****************************/
Route::group(['namespace' => 'SuperAdmin','middleware' => 'super_admin', 'prefix' => 'super_admin'], function(){

    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::put('/settings', 'SettingController@update')->name('settings.update');

});

/************************ PARENT ****************************/
Route::group(['namespace' => 'MyParent','middleware' => 'my_parent',], function(){

    Route::get('/my_children', 'MyController@children')->name('my_children');

});
