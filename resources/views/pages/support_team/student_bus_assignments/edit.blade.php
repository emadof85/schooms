@extends('layouts.master')
@section('page_title', __('msg.edit_student_bus_assignment'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_student_bus_assignment') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('bus.student_assignments.update', $student_bus_assignment->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label for="student_record_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.student') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_student') }}" class="form-control select" name="student_record_id" id="student_record_id">
                                    @foreach($students as $student)
                                        <option {{ old('student_record_id', $student_bus_assignment->student_record_id) == $student->id ? 'selected' : '' }} value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_assignment_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_assignment') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus_assignment') }}" class="form-control select" name="bus_assignment_id" id="bus_assignment_id">
                                    @foreach($bus_assignments as $bus_assignment)
                                        <option {{ old('bus_assignment_id', $student_bus_assignment->bus_assignment_id) == $bus_assignment->id ? 'selected' : '' }} value="{{ $bus_assignment->id }}">{{ $bus_assignment->bus->bus_number }} - {{ $bus_assignment->busRoute->route_name ?? 'No Route' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_stop_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_stop') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus_stop') }}" class="form-control select" name="bus_stop_id" id="bus_stop_id">
                                    @foreach($bus_stops as $bus_stop)
                                        <option {{ old('bus_stop_id', $student_bus_assignment->bus_stop_id) == $bus_stop->id ? 'selected' : '' }} value="{{ $bus_stop->id }}">{{ $bus_stop->stop_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.fee') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="fee" value="{{ old('fee', $student_bus_assignment->fee) }}" required type="number" step="0.01" class="form-control" placeholder="{{ __('msg.fee') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                    <option {{ old('status', $student_bus_assignment->status) == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                    <option {{ old('status', $student_bus_assignment->status) == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button id="ajax-btn" type="submit" class="btn btn-primary">{{__('msg.update')}} <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
