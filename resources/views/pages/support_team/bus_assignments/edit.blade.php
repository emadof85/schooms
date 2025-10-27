@extends('layouts.master')
@section('page_title', __('msg.edit_bus_assignment'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_bus_assignment') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('bus.assignments.update', $bus_assignment->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label for="bus_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus') }}" class="form-control select" name="bus_id" id="bus_id">
                                    @foreach($buses as $bus)
                                        <option {{ old('bus_id', $bus_assignment->bus_id) == $bus->id ? 'selected' : '' }} value="{{ $bus->id }}">{{ $bus->bus_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_route_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_route') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus_route') }}" class="form-control select" name="bus_route_id" id="bus_route_id">
                                    @foreach($bus_routes as $bus_route)
                                        <option {{ old('bus_route_id', $bus_assignment->bus_route_id) == $bus_route->id ? 'selected' : '' }} value="{{ $bus_route->id }}">{{ $bus_route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_driver_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_driver') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus_driver') }}" class="form-control select" name="bus_driver_id" id="bus_driver_id">
                                    @foreach($bus_drivers as $bus_driver)
                                        <option {{ old('bus_driver_id', $bus_assignment->bus_driver_id) == $bus_driver->id ? 'selected' : '' }} value="{{ $bus_driver->id }}">{{ $bus_driver->employee->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.assignment_date') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="assignment_date" value="{{ old('assignment_date', $bus_assignment->assignment_date ? $bus_assignment->assignment_date->format('Y-m-d') : '') }}" required type="date" class="form-control" placeholder="{{ __('msg.assignment_date') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                    <option {{ old('status', $bus_assignment->status) == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                    <option {{ old('status', $bus_assignment->status) == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
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
