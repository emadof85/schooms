@extends('layouts.master')
@section('page_title', __('msg.edit_bus'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_bus') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('buses.update', $bus->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_number') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="bus_number" value="{{ old('bus_number', $bus->bus_number) }}" required type="text" class="form-control" placeholder="{{ __('msg.bus_number') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.plate_number') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="plate_number" value="{{ old('plate_number', $bus->plate_number) }}" required type="text" class="form-control" placeholder="{{ __('msg.plate_number') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.model') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="model" value="{{ old('model', $bus->model) }}" required type="text" class="form-control" placeholder="{{ __('msg.model') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.capacity') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="capacity" value="{{ old('capacity', $bus->capacity) }}" required type="number" class="form-control" placeholder="{{ __('msg.capacity') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                    <option {{ old('status', $bus->status) == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                    <option {{ old('status', $bus->status) == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
                                    <option {{ old('status', $bus->status) == 'maintenance' ? 'selected' : '' }} value="maintenance">{{ __('msg.maintenance') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.description') }}</label>
                            <div class="col-lg-9">
                                <textarea name="description" class="form-control" rows="3" placeholder="{{ __('msg.description') }}">{{ old('description', $bus->description) }}</textarea>
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
