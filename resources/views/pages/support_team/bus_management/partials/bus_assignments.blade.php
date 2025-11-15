<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('msg.manage_bus_assignments') }}</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-bus-assignments" class="nav-link active" data-toggle="tab">{{ __('msg.manage_bus_assignments') }}</a></li>
            <li class="nav-item"><a href="#new-bus-assignment" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_bus_assignment') }}</a></li>
        </ul>

        <div class="tab-content">
                <div class="tab-pane fade show active" id="all-bus-assignments">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>{{ __('msg.sn') }}</th>
                            <th>{{ __('msg.bus') }}</th>
                            <th>{{ __('msg.bus_route') }}</th>
                            <th>{{ __('msg.bus_driver') }}</th>
                            <th>{{ __('msg.assignment_date') }}</th>
                            <th>{{ __('msg.status') }}</th>
                            <th>{{ __('msg.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bus_assignments as $bus_assignment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bus_assignment->bus->bus_number }}</td>
                                <td>{{ $bus_assignment->busRoute? $bus_assignment->busRoute->route_name : 'N/A' }}</td>
                                <td>{{ $bus_assignment->bus->currentDriver? $bus_assignment->bus->currentDriver->employee->user->name : 'N/A' }}</td>
                                <td>{{ $bus_assignment->assignment_date }}</td>
                                <td>{{ $bus_assignment->active? 'Active' : 'Inactive' }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                @if(Qs::userIsTeamSA())
                                                {{--Edit--}}
                                                <a href="{{ route('bus.assignments.edit', $bus_assignment->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                               @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                {{--Delete--}}
                                                <a id="{{ $bus_assignment->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                <form method="post" id="item-delete-{{ $bus_assignment->id }}" action="{{ route('bus.assignments.destroy', $bus_assignment->id) }}" class="hidden">@csrf @method('delete')</form>
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

            <div class="tab-pane fade" id="new-bus-assignment">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                            <span> {{ __('msg.bus_assignment_creation_note') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form class="ajax-store" method="post" action="{{ route('bus.assignments.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="bus_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select required data-placeholder="{{ __('msg.select_bus') }}" class="form-control select" name="bus_id" id="bus_id">
                                        @foreach($buses as $bus)
                                            <option {{ old('bus_id') == $bus->id ? 'selected' : '' }} value="{{ $bus->id }}">{{ $bus->bus_number }}</option>
                                        @endforeach
                                    </select>
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
                                <label for="bus_driver_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_driver') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select required data-placeholder="{{ __('msg.select_bus_driver') }}" class="form-control select" name="bus_driver_id" id="bus_driver_id">
                                        @foreach($bus_drivers as $bus_driver)
                                            <option {{ old('bus_driver_id') == $bus_driver->id ? 'selected' : '' }} value="{{ $bus_driver->id }}">{{ $bus_driver->employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.assignment_date') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="assignment_date" value="{{ old('assignment_date') }}" required type="date" class="form-control" placeholder="{{ __('msg.assignment_date') }}">
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