@extends('layouts.master')
@section('page_title', __('msg.edit_bus_driver'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_bus_driver') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('bus.drivers.update', $bus_driver->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label for="employee_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.employee') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_employee') }}" class="form-control select" name="employee_id" id="employee_id">
                                    @foreach($employees as $employee)
                                        <option {{ old('employee_id', $bus_driver->employee_id) == $employee->id ? 'selected' : '' }} value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus') }}" class="form-control select" name="bus_id" id="bus_id">
                                    @foreach($buses as $bus)
                                        <option {{ old('bus_id', $bus_driver->bus_id) == $bus->id ? 'selected' : '' }} value="{{ $bus->id }}">{{ $bus->bus_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.assignment_date') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="assignment_date" value="{{ old('assignment_date', $bus_driver->assignment_date ? $bus_driver->assignment_date->format('Y-m-d') : '') }}" required type="date" class="form-control" placeholder="{{ __('msg.assignment_date') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.end_date') }}</label>
                            <div class="col-lg-9">
                                <input name="end_date" value="{{ old('end_date', $bus_driver->end_date ? $bus_driver->end_date->format('Y-m-d') : '') }}" type="date" class="form-control" placeholder="{{ __('msg.end_date') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.active') }}</label>
                            <div class="col-lg-9">
                                <div class="form-check form-check-switchery">
                                    <label class="form-check-label">
                                        <input name="active" value="1" {{ old('active', $bus_driver->active) ? 'checked' : '' }} type="checkbox" class="form-check-input-switchery">
                                        {{ __('msg.active') }}
                                    </label>
                                </div>
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
