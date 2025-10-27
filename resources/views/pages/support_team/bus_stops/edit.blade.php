@extends('layouts.master')
@section('page_title', __('msg.edit_bus_stop'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_bus_stop') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('bus.stops.update', $bus_stop->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.stop_name') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="stop_name" value="{{ old('stop_name', $bus_stop->stop_name) }}" required type="text" class="form-control" placeholder="{{ __('msg.stop_name') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bus_route_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_route') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_bus_route') }}" class="form-control select" name="bus_route_id" id="bus_route_id">
                                    @foreach($bus_routes as $bus_route)
                                        <option {{ old('bus_route_id', $bus_stop->bus_route_id) == $bus_route->id ? 'selected' : '' }} value="{{ $bus_route->id }}">{{ $bus_route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.stop_order') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="order" value="{{ old('order', $bus_stop->order) }}" required type="number" class="form-control" placeholder="{{ __('msg.stop_order') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.pickup_time') }}</label>
                            <div class="col-lg-9">
                                <input name="pickup_time" value="{{ old('pickup_time', $bus_stop->pickup_time) }}" type="time" class="form-control" placeholder="{{ __('msg.pickup_time') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.dropoff_time') }}</label>
                            <div class="col-lg-9">
                                <input name="dropoff_time" value="{{ old('dropoff_time', $bus_stop->dropoff_time) }}" type="time" class="form-control" placeholder="{{ __('msg.dropoff_time') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                    <option {{ old('status', $bus_stop->status) == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                    <option {{ old('status', $bus_stop->status) == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.address') }}</label>
                            <div class="col-lg-9">
                                <textarea name="address" class="form-control" rows="3" placeholder="{{ __('msg.address') }}">{{ old('address', $bus_stop->address) }}</textarea>
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
