<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('msg.manage_bus_drivers') }}</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-bus-drivers" class="nav-link active" data-toggle="tab">{{ __('msg.manage_bus_drivers') }}</a></li>
            <li class="nav-item"><a href="#new-bus-driver" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_bus_driver') }}</a></li>
        </ul>

        <div class="tab-content">
                <div class="tab-pane fade show active" id="all-bus-drivers">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>{{ __('msg.sn') }}</th>
                            <th>{{ __('msg.employee') }}</th>
                            <th>{{ __('msg.bus') }}</th>
                            <th>{{ __('msg.assignment_date') }}</th>
                            <th>{{ __('msg.end_date') }}</th>
                            <th>{{ __('msg.status') }}</th>
                            <th>{{ __('msg.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bus_drivers as $bus_driver)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bus_driver->employee->user->name }}</td>
                                <td>{{ $bus_driver->bus->bus_number }}</td>
                                <td>{{ $bus_driver->assignment_date }}</td>
                                <td>{{ $bus_driver->end_date }}</td>
                                <td>{{ $bus_driver->active ? __('msg.active') : __('msg.inactive') }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                @if(Qs::userIsTeamSA())
                                                {{--Edit--}}
                                                <a href="{{ route('bus.drivers.edit', $bus_driver->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                               @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                {{--Delete--}}
                                                <a id="{{ $bus_driver->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                <form method="post" id="item-delete-{{ $bus_driver->id }}" action="{{ route('bus.drivers.destroy', $bus_driver->id) }}" class="hidden">@csrf @method('delete')</form>
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

            <div class="tab-pane fade" id="new-bus-driver">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                            <span> {{ __('msg.bus_driver_creation_note') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form class="ajax-store" method="post" action="{{ route('bus.drivers.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="employee_id" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.employee') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select required data-placeholder="{{ __('msg.select_employee') }}" class="form-control select" name="employee_id" id="employee_id">
                                        @foreach($employees as $employee)
                                            <option {{ old('employee_id') == $employee->id ? 'selected' : '' }} value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.assignment_date') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="assignment_date" value="{{ old('assignment_date') }}" required type="date" class="form-control" placeholder="{{ __('msg.assignment_date') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.end_date') }}</label>
                                <div class="col-lg-9">
                                    <input name="end_date" value="{{ old('end_date') }}" type="date" class="form-control" placeholder="{{ __('msg.end_date') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.active') }}</label>
                                <div class="col-lg-9">
                                    <div class="form-check form-check-switchery">
                                        <label class="form-check-label">
                                            <input name="active" value="1" {{ old('active', 1) ? 'checked' : '' }} type="checkbox" class="form-check-input-switchery" data-fouc>
                                            {{ __('msg.active') }}
                                        </label>
                                    </div>
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