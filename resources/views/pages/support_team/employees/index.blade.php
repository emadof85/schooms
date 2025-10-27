@extends('layouts.master')
@section('page_title', __('msg.manage_employees'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_employees') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-employees" class="nav-link active" data-toggle="tab">{{ __('msg.manage_employees') }}</a></li>
                <li class="nav-item"><a href="#new-employee" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_employee') }}</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-employees">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.name_49ee') }}</th>
                                <th>{{ __('msg.email') }}</th>
                                <th>{{ __('msg.phone') }}</th>
                                <th>{{ __('msg.position') }}</th>
                                <th>{{ __('msg.status') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $employee->user->name }}</td>
                                    <td>{{ $employee->user->email }}</td>
                                    <td>{{ $employee->user->phone }}</td>
                                    <td>{{ $employee->type }}</td>
                                    <td>{{ $employee->active ? __('msg.active') : __('msg.inactive') }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('employees.edit', $employee->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                   @endif
                                                        @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $employee->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                    <form method="post" id="item-delete-{{ $employee->id }}" action="{{ route('employees.destroy', $employee->id) }}" class="hidden">@csrf @method('delete')</form>
                                                        @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                <div class="tab-pane fade" id="new-employee">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span> {{ __('msg.employee_creation_note') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('employees.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.user') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select name="user_id" required class="form-control select" data-placeholder="{{ __('msg.select_user') }}">
                                            <option value="">{{ __('msg.select_user') }}</option>
                                            @foreach(\App\User::whereNotIn('user_type', ['student', 'parent'])->get() as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.type') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select name="type" required class="form-control select" data-placeholder="{{ __('msg.select_type') }}">
                                            <option value="">{{ __('msg.select_type') }}</option>
                                            <option value="driver" {{ old('type') == 'driver' ? 'selected' : '' }}>{{ __('msg.driver') }}</option>
                                            <option value="staff" {{ old('type') == 'staff' ? 'selected' : '' }}>{{ __('msg.staff') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.license_number') }}</label>
                                    <div class="col-lg-9">
                                        <input name="license_number" value="{{ old('license_number') }}" type="text" class="form-control" placeholder="{{ __('msg.license_number') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.license_expiry') }}</label>
                                    <div class="col-lg-9">
                                        <input name="license_expiry" value="{{ old('license_expiry') }}" type="date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.active') }}</label>
                                    <div class="col-lg-9">
                                        <select name="active" class="form-control select">
                                            <option value="1" {{ old('active', 1) ? 'selected' : '' }}>{{ __('msg.yes') }}</option>
                                            <option value="0" {{ !old('active', 1) ? 'selected' : '' }}>{{ __('msg.no') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button id="ajax-btn" type="submit" class="btn btn-primary">{{__('msg.submit_form')}} <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
