@extends('layouts.master')
@section('page_title', __('msg.manage_student_bus_assignments'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_student_bus_assignments') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-student-bus-assignments" class="nav-link active" data-toggle="tab">{{ __('msg.manage_student_bus_assignments') }}</a></li>
                <li class="nav-item"><a href="#new-student-bus-assignment" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_student_bus_assignment') }}</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-student-bus-assignments">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.student') }}</th>
                                <th>{{ __('msg.bus_assignment') }}</th>
                                <th>{{ __('msg.bus_stop') }}</th>
                                <th>{{ __('msg.fee') }}</th>
                                <th>{{ __('msg.status') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($student_bus_assignments as $student_bus_assignment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student_bus_assignment->student ? $student_bus_assignment->student->user->name : 'N/A' }}</td>
                                    <td>{{ $student_bus_assignment->busAssignment? ($student_bus_assignment->busAssignment->bus->bus_number . ' - ' . ($student_bus_assignment->busAssignment->busRoute ? $student_bus_assignment->busAssignment->busRoute->route_name : 'N/A')) : 'N/A' }}</td>
                                    <td>{{ $student_bus_assignment->busStop? $student_bus_assignment->busStop->stop_name : 'N/A' }}</td>
                                    <td>{{ $student_bus_assignment->fee }}</td>
                                    <td>{{ $student_bus_assignment->status == 'active' ? 'Active' : 'Inactive' }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('bus.student_assignments.edit', $student_bus_assignment->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                   @endif
                                                        @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $student_bus_assignment->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                    <form method="post" id="item-delete-{{ $student_bus_assignment->id }}" action="{{ route('bus.student_assignments.destroy', $student_bus_assignment->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-student-bus-assignment">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span> {{ __('msg.student_bus_assignment_creation_note') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('bus.student_assignments.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label for="student_record_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.student') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_student') }}" class="form-control select" name="student_record_id" id="student_record_id">
                                            @foreach($students as $student)
                                                <option {{ old('student_record_id') == $student->id ? 'selected' : '' }} value="{{ $student->id }}">{{ $student->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="bus_assignment_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_assignment') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_bus_assignment') }}" class="form-control select" name="bus_assignment_id" id="bus_assignment_id">
                                            @foreach($bus_assignments as $bus_assignment)
                                                <option {{ old('bus_assignment_id') == $bus_assignment->id ? 'selected' : '' }} value="{{ $bus_assignment->id }}">{{ $bus_assignment->bus->bus_number }} - {{ $bus_assignment->bus_route ? $bus_assignment->bus_route->route_name : 'N/A' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="bus_stop_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_stop') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_bus_stop') }}" class="form-control select" name="bus_stop_id" id="bus_stop_id">
                                            @foreach($bus_stops as $bus_stop)
                                                <option {{ old('bus_stop_id') == $bus_stop->id ? 'selected' : '' }} value="{{ $bus_stop->id }}">{{ $bus_stop->stop_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.fee') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="fee" value="{{ old('fee') }}" required type="number" step="0.01" class="form-control" placeholder="{{ __('msg.fee') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                            <option {{ old('status') == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                            <option {{ old('status') == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
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
