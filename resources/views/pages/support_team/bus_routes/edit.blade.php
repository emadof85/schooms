@extends('layouts.master')
@section('page_title', __('msg.edit_bus_route'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_bus_route') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('bus.routes.update', $bus_route->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.route_name') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="route_name" value="{{ old('route_name', $bus_route->route_name) }}" required type="text" class="form-control" placeholder="{{ __('msg.route_name') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.start_location') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="start_location" value="{{ old('start_location', $bus_route->start_location) }}" required type="text" class="form-control" placeholder="{{ __('msg.start_location') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.end_location') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="end_location" value="{{ old('end_location', $bus_route->end_location) }}" required type="text" class="form-control" placeholder="{{ __('msg.end_location') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.distance') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="distance_km" value="{{ old('distance_km', $bus_route->distance_km) }}" required type="number" step="0.01" class="form-control" placeholder="{{ __('msg.distance') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.departure_time') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="departure_time" value="{{ old('departure_time', $bus_route->departure_time ? $bus_route->departure_time->format('H:i') : '') }}" required type="time" class="form-control" placeholder="{{ __('msg.departure_time') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.arrival_time') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="arrival_time" value="{{ old('arrival_time', $bus_route->arrival_time ? $bus_route->arrival_time->format('H:i') : '') }}" required type="time" class="form-control" placeholder="{{ __('msg.arrival_time') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                            <div class="col-lg-9">
                                <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="active" id="active">
                                    <option {{ old('active', $bus_route->active) ? 'selected' : '' }} value="1">{{ __('msg.active') }}</option>
                                    <option {{ old('active', $bus_route->active) == false ? 'selected' : '' }} value="0">{{ __('msg.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.description') }}</label>
                            <div class="col-lg-9">
                                <textarea name="description" class="form-control" rows="3" placeholder="{{ __('msg.description') }}">{{ old('description', $bus_route->description) }}</textarea>
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
