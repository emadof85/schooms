<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="card-body">
                <div class="media">
                    <div class="mr-3">
                        <a href="{{ route('my_account') }}"><img src="{{ Auth::user()->photo }}" width="38" height="38" class="rounded-circle" alt="{{ __('msg.photo_5ae0') }}"></a>
                    </div>

                    <div class="media-body">
                        <div class="media-title font-weight-semibold">{{ Auth::user()->name }}</div>
                        <div class="font-size-xs opacity-50">
                            <i class="icon-user font-size-sm"></i> &nbsp;{{ ucwords(str_replace('_', ' ', Auth::user()->user_type)) }}
                        </div>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('my_account') }}" class="text-white"><i class="icon-cog3"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item">
                    <a href="{{ route('livewire.dashboard') }}" class="nav-link {{ (Route::is('livewire.dashboard')) ? 'active' : '' }}">
                        <i class="icon-home4"></i>
                        <span>{{ __('msg.dashboard') }}</span>
                    </a>
                </li>

                {{--Academics--}}
                </li>

                {{--Academics--}}
                @if(Qs::userIsAcademic())
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> {{ __('msg.academics') }}</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.manage_academics') }}">

                        {{--Timetables--}}
                            <li class="nav-item"><a href="{{ route('tt.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['tt.index']) ? 'active' : '' }}">{{ __('msg.timetables') }}</a></li>
                            <li class="nav-item"><a href="{{ route('attendance.ui') }}" class="nav-link {{ Route::is('attendance.ui') ? 'active' : '' }}"><i class="icon-check"></i> {{ __('msg.attendance') }}</a></li>
                            <li class="nav-item"><a href="{{ route('reports.index') }}" class="nav-link {{ Route::is('reports.index') ? 'active' : '' }}"><i class="icon-file-pdf"></i> {{ __('msg.reports') }}</a></li>
                        </ul>
                    </li>
                    @endif

                  {{--Administrative--}}
@if(Qs::userIsAdministrative())
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), [
    'payments.index', 'payments.create', 'payments.invoice', 'payments.receipts', 
    'payments.edit', 'payments.manage', 'payments.show',
    'finance.dashboard', 'finance.salaries.index', 'finance.incomes.index', 
    'finance.expenses.index', 'finance.deductions-bonuses.index',
    'finance.reports.income-expense', 'finance.reports.payroll',
    'finance.categories.income.index', 'finance.categories.expense.index' // ADDED
]) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-office"></i> <span> {{ __('msg.administrative') }}</span></a>

    <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.administrative') }}">

        {{--Finance--}}
        @if(Qs::userIsTeamAccount())
        <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), [
            'finance.dashboard', 'finance.salaries.index', 'finance.incomes.index', 
            'finance.expenses.index', 'finance.deductions-bonuses.index',
            'finance.reports.income-expense', 'finance.reports.payroll',
            'finance.categories.income.index', 'finance.categories.expense.index' // ADDED
        ]) ? 'nav-item-expanded' : '' }}">

            <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), [
                'finance.dashboard', 'finance.salaries.index', 'finance.incomes.index', 
                'finance.expenses.index', 'finance.deductions-bonuses.index',
                'finance.reports.income-expense', 'finance.reports.payroll',
                'finance.categories.income.index', 'finance.categories.expense.index' // ADDED
            ]) ? 'active' : '' }}">{{ __('msg.finance') }}</a>

            <ul class="nav nav-group-sub">
                {{-- Finance Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('finance.dashboard') }}" class="nav-link {{ Route::is('finance.dashboard') ? 'active' : '' }}">
                        <i class="icon-dashboard"></i> {{ __('msg.finance_dashboard') }}
                    </a>
                </li>

                {{-- Income Management --}}
                <li class="nav-item">
                    <a href="{{ route('finance.incomes.index') }}" class="nav-link {{ Route::is('finance.incomes.index') ? 'active' : '' }}">
                        <i class="icon-coin-dollar"></i> {{ __('msg.income_management') }}
                    </a>
                </li>

                {{-- Expense Management --}}
                <li class="nav-item">
                    <a href="{{ route('finance.expenses.index') }}" class="nav-link {{ Route::is('finance.expenses.index') ? 'active' : '' }}">
                        <i class="icon-credit-card"></i> {{ __('msg.expense_management') }}
                    </a>
                </li>

                {{-- Category Management --}}
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['finance.categories.income.index', 'finance.categories.expense.index']) ? 'active' : '' }}">
                        <i class="icon-folder"></i> {{ __('msg.category_management') }}
                    </a>
                    <ul class="nav nav-group-sub">
                        <li class="nav-item">
                            <a href="{{ route('finance.categories.income.index') }}" class="nav-link {{ Route::is('finance.categories.income.index') ? 'active' : '' }}">
                                <i class="icon-coin-dollar"></i> {{ __('msg.income_categories') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('finance.categories.expense.index') }}" class="nav-link {{ Route::is('finance.categories.expense.index') ? 'active' : '' }}">
                                <i class="icon-credit-card"></i> {{ __('msg.expense_categories') }}
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Salary Management --}}
                <li class="nav-item">
                    <a href="{{ route('finance.salaries.index') }}" class="nav-link {{ Route::is('finance.salaries.index') ? 'active' : '' }}">
                        <i class="icon-users"></i> {{ __('msg.salary_management') }}
                    </a>
                </li>

                {{-- Deductions & Bonuses --}}
                <li class="nav-item">
                    <a href="{{ route('finance.deductions-bonuses.index') }}" class="nav-link {{ Route::is('finance.deductions-bonuses.index') ? 'active' : '' }}">
                        <i class="icon-percent"></i> {{ __('msg.deductions_bonuses') }}
                    </a>
                </li>

                {{-- Reports --}}
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link">{{ __('msg.reports') }}</a>
                    <ul class="nav nav-group-sub">
                        <li class="nav-item">
                            <a href="{{ route('finance.reports.income-expense') }}" class="nav-link {{ Route::is('finance.reports.income-expense') ? 'active' : '' }}">
                                {{ __('msg.income_expense_report') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('finance.reports.payroll') }}" class="nav-link {{ Route::is('finance.reports.payroll') ? 'active' : '' }}">
                                {{ __('msg.payroll_report') }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

        </li>
        @endif
                            {{--Payments--}}
                            @if(Qs::userIsTeamAccount())
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), [
                                'payments.index', 'payments.create', 'payments.edit', 'payments.manage', 
                                'payments.show', 'payments.invoice'
                            ]) ? 'nav-item-expanded' : '' }}">

                                <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), [
                                    'payments.index', 'payments.edit', 'payments.create', 'payments.manage', 
                                    'payments.show', 'payments.invoice'
                                ]) ? 'active' : '' }}">{{ __('msg.payments') }}</a>

                                <ul class="nav nav-group-sub">
                                    <li class="nav-item">
                                        <a href="{{ route('payments.create') }}" class="nav-link {{ Route::is('payments.create') ? 'active' : '' }}">
                                            {{ __('msg.create_payment') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('payments.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.edit', 'payments.show']) ? 'active' : '' }}">
                                            {{ __('msg.manage_payments') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('payments.manage') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.manage', 'payments.invoice', 'payments.receipts']) ? 'active' : '' }}">
                                            {{ __('msg.student_payments') }}
                                        </a>
                                    </li>
                                </ul>

                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                {{--Manage Students--}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.create', 'students.list', 'students.edit', 'students.show', 'students.promotion', 'students.promotion_manage', 'students.graduated']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-users"></i> <span> {{ __('msg.students') }}</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.manage_students') }}">
                            {{--Admit Student--}}
                            @if(Qs::userIsTeamSA())
                                <li class="nav-item">
                                    <a href="{{ route('students.create') }}"
                                       class="nav-link {{ (Route::is('students.create')) ? 'active' : '' }}">{{ __('msg.admit_student') }}</a>
                                </li>
                            @endif

                            {{--Student Information--}}
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit', 'students.show']) ? 'nav-item-expanded' : '' }}">
                                <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit', 'students.show']) ? 'active' : '' }}">{{ __('msg.student_information_1749') }}</a>
                                <ul class="nav nav-group-sub">
                                    @foreach(App\Models\MyClass::orderBy('name')->get() as $c)
                                        <li class="nav-item"><a href="{{ route('students.list', $c->id) }}" class="nav-link ">{{ $c->name }}</a></li>
                                    @endforeach
                                </ul>
                            </li>

                            @if(Qs::userIsTeamSA())

                            {{--Student Promotion--}}
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage']) ? 'nav-item-expanded' : '' }}"><a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage' ]) ? 'active' : '' }}">{{ __('msg.student_promotion') }}</a>
                            <ul class="nav nav-group-sub">
                                <li class="nav-item"><a href="{{ route('students.promotion') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion']) ? 'active' : '' }}">{{ __('msg.promote_students') }}</a></li>
                                <li class="nav-item"><a href="{{ route('students.promotion_manage') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion_manage']) ? 'active' : '' }}">{{ __('msg.manage_promotions') }}</a></li>
                            </ul>

                            </li>

                            {{--Student Graduated--}}
                            <li class="nav-item"><a href="{{ route('students.graduated') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.graduated' ]) ? 'active' : '' }}">{{ __('msg.students_graduated') }}</a></li>
                                @endif

                        </ul>
                    </li>
                @endif

                @if(Qs::userIsTeamSA())
                    {{--Manage Users--}}
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['users.index', 'users.show', 'users.edit']) ? 'active' : '' }}"><i class="icon-users4"></i> <span> {{ __('msg.users') }}</span></a>
                    </li>

                    {{--Manage Classes--}}
                    <li class="nav-item">
                        <a href="{{ route('classes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['classes.index','classes.edit']) ? 'active' : '' }}"><i class="icon-windows2"></i> <span> {{ __('msg.classes') }}</span></a>
                    </li>

                    {{--Manage Dorms--}}
                    <li class="nav-item">
                        <a href="{{ route('dorms.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['dorms.index','dorms.edit']) ? 'active' : '' }}"><i class="icon-home9"></i> <span> {{ __('msg.dormitories') }}</span></a>
                    </li>

                {{--Manage Sections--}}
                    <li class="nav-item">
                        <a href="{{ route('sections.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['sections.index','sections.edit',]) ? 'active' : '' }}"><i class="icon-fence"></i> <span>{{ __('msg.sections') }}</span></a>
                    </li>

                    {{--Manage Subjects--}}
                    <li class="nav-item">
                        <a href="{{ route('subjects.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['subjects.index','subjects.edit',]) ? 'active' : '' }}"><i class="icon-pin"></i> <span>{{ __('msg.subjects_8b2f') }}</span></a>
                    </li>

                    {{--Field Definitions--}}
                    <li class="nav-item">
                        <a href="{{ route('field-definitions.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['field-definitions.index','field-definitions.create','field-definitions.edit']) ? 'active' : '' }}"><i class="icon-list"></i> <span>{{ __('msg.field_definitions') }}</span></a>
                    </li>

                    {{--Bus Management--}}
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['buses.index', 'buses.create', 'buses.edit', 'bus.drivers', 'bus.drivers.create', 'bus.drivers.edit', 'bus.routes', 'bus.routes.create', 'bus.routes.edit', 'bus.stops', 'bus.stops.create', 'bus.stops.edit', 'bus.assignments', 'bus.assignments.create', 'bus.assignments.edit', 'bus.student_assignments', 'bus.student_assignments.create', 'bus.student_assignments.edit', 'bus.student.assignments']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-bus"></i> <span> {{ __('msg.bus_management') }}</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.bus_management') }}">

                            {{--Buses--}}
                            <li class="nav-item"><a href="{{ route('buses.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.buses', 'bus.buses.create', 'bus.buses.edit']) ? 'active' : '' }}">{{ __('msg.buses') }}</a></li>

                            {{--Bus Drivers--}}
                            <li class="nav-item"><a href="{{ route('bus.drivers') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.drivers', 'bus.drivers.create', 'bus.drivers.edit']) ? 'active' : '' }}">{{ __('msg.bus_drivers') }}</a></li>

                            {{--Bus Routes--}}
                            <li class="nav-item"><a href="{{ route('bus.routes') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.routes', 'bus.routes.create', 'bus.routes.edit']) ? 'active' : '' }}">{{ __('msg.bus_routes') }}</a></li>

                            {{--Bus Stops--}}
                            <li class="nav-item"><a href="{{ route('bus.stops') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.stops', 'bus.stops.create', 'bus.stops.edit']) ? 'active' : '' }}">{{ __('msg.bus_stops') }}</a></li>

                            {{--Bus Assignments--}}
                            <li class="nav-item"><a href="{{ route('bus.assignments') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.assignments', 'bus.assignments.create', 'bus.assignments.edit']) ? 'active' : '' }}">{{ __('msg.bus_assignments') }}</a></li>

                            {{--Student Bus Assignments--}}
                            <li class="nav-item"><a href="{{ route('bus.student_assignments') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['bus.student_assignments', 'bus.student_assignments.create', 'bus.student_assignments.edit', 'bus.student.assignments']) ? 'active' : '' }}">{{ __('msg.student_bus_assignments') }}</a></li>

                        </ul>
                    </li>

                    {{--Employee Management--}}
                    <li class="nav-item">
                        <a href="{{ route('employees.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['employees.index', 'employees.create', 'employees.edit']) ? 'active' : '' }}"><i class="icon-users4"></i> <span> {{ __('msg.employee_management') }}</span></a>
                    </li>
                @endif

                {{--Exam--}}
                @if(Qs::userIsTeamSAT())
                <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['exams.index', 'exams.edit', 'grades.index', 'grades.edit', 'marks.index', 'marks.manage', 'marks.bulk', 'marks.tabulation', 'marks.show', 'marks.batch_fix',]) ? 'nav-item-expanded nav-item-open' : '' }} ">
                    <a href="#" class="nav-link"><i class="icon-books"></i> <span> {{ __('msg.exams') }}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.manage_exams') }}">
                        @if(Qs::userIsTeamSA())

                        {{--Exam list--}}
                            <li class="nav-item">
                                <a href="{{ route('exams.index') }}"
                                   class="nav-link {{ (Route::is('exams.index')) ? 'active' : '' }}">{{ __('msg.exam_list') }}</a>
                            </li>

                            {{--Grades list--}}
                            <li class="nav-item">
                                    <a href="{{ route('grades.index') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), ['grades.index', 'grades.edit']) ? 'active' : '' }}">{{ __('msg.grades') }}</a>
                            </li>

                            {{--Tabulation Sheet--}}
                            <li class="nav-item">
                                <a href="{{ route('marks.tabulation') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.tabulation']) ? 'active' : '' }}">{{ __('msg.tabulation_sheet') }}</a>
                            </li>

                            {{--Marks Batch Fix--}}
                            <li class="nav-item">
                                <a href="{{ route('marks.batch_fix') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.batch_fix']) ? 'active' : '' }}">{{ __('msg.batch_fix') }}</a>
                            </li>
                        @endif

                        @if(Qs::userIsTeamSAT())
                            {{--Marks Manage--}}
                            <li class="nav-item">
                                <a href="{{ route('marks.index') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), ['marks.index']) ? 'active' : '' }}">{{ __('msg.marks') }}</a>
                            </li>

                            {{--Marksheet--}}
                            <li class="nav-item">
                                <a href="{{ route('marks.bulk') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.bulk', 'marks.show']) ? 'active' : '' }}">{{ __('msg.marksheet') }}</a>
                            </li>

                            @endif

                    </ul>
                </li>
                @endif


                {{--Communication Module--}}
                @if(Qs::userIsTeamSA())
                <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['communication.email', 'communication.sms']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                    <a href="#" class="nav-link"><i class="icon-envelop5"></i> <span> {{ __('msg.communication') }}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('msg.communication') }}">
                        {{--Email Communication--}}
                        <li class="nav-item">
                            <a href="{{ route('communication.email') }}" class="nav-link {{ Route::is('communication.email') ? 'active' : '' }}">{{ __('msg.email_communication') }}</a>
                        </li>

                        {{--SMS Communication--}}
                        <li class="nav-item">
                            <a href="{{ route('communication.sms') }}" class="nav-link {{ Route::is('communication.sms') ? 'active' : '' }}">{{ __('msg.sms_communication') }}</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{--End Exam--}}

                @include('pages.'.Qs::getUserType().'.menu')

                {{--Manage Account--}}
                <li class="nav-item">
                    <a href="{{ route('my_account') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['my_account']) ? 'active' : '' }}"><i class="icon-user"></i> <span>{{ __('msg.my_account') }}</span></a>
                </li>

                </ul>
            </div>
        </div>
</div>
