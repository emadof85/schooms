@extends('layouts.master')
@section('page_title', __('msg.manage_bus_routes'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_bus_routes') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-bus-routes" class="nav-link active" data-toggle="tab">{{ __('msg.manage_bus_routes') }}</a></li>
                <li class="nav-item"><a href="#new-bus-route" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_bus_route') }}</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-bus-routes">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.route_name') }}</th>
                                <th>{{ __('msg.start_location') }}</th>
                                <th>{{ __('msg.end_location') }}</th>
                                <th>{{ __('msg.distance') }}</th>
                                <th>{{ __('msg.departure_time') }}</th>
                                <th>{{ __('msg.arrival_time') }}</th>
                                <th>{{ __('msg.status') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bus_routes as $bus_route)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $bus_route->route_name }}</td>
                                    <td>{{ $bus_route->start_location }}</td>
                                    <td>{{ $bus_route->end_location }}</td>
                                    <td>{{ $bus_route->distance_km }} km</td>
                                    <td>{{ $bus_route->departure_time }}</td>
                                    <td>{{ $bus_route->arrival_time }}</td>
                                    <td>{{ $bus_route->active ? __('msg.active') : __('msg.inactive') }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('bus.routes.edit', $bus_route->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                   @endif
                                                        @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $bus_route->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                    <form method="post" id="item-delete-{{ $bus_route->id }}" action="{{ route('bus.routes.destroy', $bus_route->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-bus-route">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span> {{ __('msg.bus_route_creation_note') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('bus.routes.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.route_name') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="route_name" value="{{ old('route_name') }}" required type="text" class="form-control" placeholder="{{ __('msg.route_name') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.start_location') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="start_location" value="{{ old('start_location') }}" required type="text" class="form-control" placeholder="{{ __('msg.start_location') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.end_location') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="end_location" value="{{ old('end_location') }}" required type="text" class="form-control" placeholder="{{ __('msg.end_location') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.distance') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="distance_km" value="{{ old('distance_km') }}" required type="number" step="0.01" class="form-control" placeholder="{{ __('msg.distance') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.departure_time') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="departure_time" value="{{ old('departure_time') }}" required type="time" class="form-control" placeholder="{{ __('msg.departure_time') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.arrival_time') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="arrival_time" value="{{ old('arrival_time') }}" required type="time" class="form-control" placeholder="{{ __('msg.arrival_time') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="active" id="active">
                                            <option {{ old('active', 1) ? 'selected' : '' }} value="1">{{ __('msg.active') }}</option>
                                            <option {{ old('active') == '0' ? 'selected' : '' }} value="0">{{ __('msg.inactive') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.description') }}</label>
                                    <div class="col-lg-9">
                                        <textarea name="description" class="form-control" rows="3" placeholder="{{ __('msg.description') }}">{{ old('description') }}</textarea>
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
