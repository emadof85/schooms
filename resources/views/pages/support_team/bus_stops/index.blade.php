@extends('layouts.master')
@section('page_title', __('msg.manage_bus_stops'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_bus_stops') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-bus-stops" class="nav-link active" data-toggle="tab">{{ __('msg.manage_bus_stops') }}</a></li>
                <li class="nav-item"><a href="#new-bus-stop" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_bus_stop') }}</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-bus-stops">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.stop_name') }}</th>
                                <th>{{ __('msg.route') }}</th>
                                <th>{{ __('msg.stop_order') }}</th>
                                <th>{{ __('msg.pickup_time') }}</th>
                                <th>{{ __('msg.dropoff_time') }}</th>
                                <th>{{ __('msg.status') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bus_stops as $bus_stop)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $bus_stop->stop_name }}</td>
                                    <td>{{ $bus_stop->busRoute? $bus_stop->busRoute->route_name : 'N/A' }}</td>
                                    <td>{{ $bus_stop->order }}</td>
                                    <td>{{ $bus_stop->pickup_time }}</td>
                                    <td>{{ $bus_stop->dropoff_time }}</td>
                                    <td>{{ $bus_stop->active ? 'Active' : 'Inactive' }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('bus.stops.edit', $bus_stop->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                   @endif
                                                        @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $bus_stop->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                    <form method="post" id="item-delete-{{ $bus_stop->id }}" action="{{ route('bus.stops.destroy', $bus_stop->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-bus-stop">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span> {{ __('msg.bus_stop_creation_note') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('bus.stops.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.stop_name') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="stop_name" value="{{ old('stop_name') }}" required type="text" class="form-control" placeholder="{{ __('msg.stop_name') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="bus_route_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_route') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_bus_route') }}" class="form-control select" name="bus_route_id" id="bus_route_id">
                                            @foreach($bus_routes as $bus_route)
                                                <option {{ old('bus_route_id') == $bus_route->id ? 'selected' : '' }} value="{{ $bus_route->id }}">{{ $bus_route->route_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.stop_order') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="stop_order" value="{{ old('stop_order') }}" required type="number" class="form-control" placeholder="{{ __('msg.stop_order') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.pickup_time') }}</label>
                                    <div class="col-lg-9">
                                        <input name="pickup_time" value="{{ old('pickup_time') }}" type="time" class="form-control" placeholder="{{ __('msg.pickup_time') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.dropoff_time') }}</label>
                                    <div class="col-lg-9">
                                        <input name="dropoff_time" value="{{ old('dropoff_time') }}" type="time" class="form-control" placeholder="{{ __('msg.dropoff_time') }}">
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

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.address') }}</label>
                                    <div class="col-lg-9">
                                        <textarea name="address" class="form-control" rows="3" placeholder="{{ __('msg.address') }}">{{ old('address') }}</textarea>
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
