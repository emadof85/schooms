<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">{{ __('msg.manage_buses') }}</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-buses" class="nav-link active" data-toggle="tab">{{ __('msg.manage_buses') }}</a></li>
            <li class="nav-item"><a href="#new-bus" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.create_new_bus') }}</a></li>
        </ul>

        <div class="tab-content">
                <div class="tab-pane fade show active" id="all-buses">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>{{ __('msg.sn') }}</th>
                            <th>{{ __('msg.bus_number') }}</th>
                            <th>{{ __('msg.plate_number') }}</th>
                            <th>{{ __('msg.model') }}</th>
                            <th>{{ __('msg.capacity') }}</th>
                            <th>{{ __('msg.status') }}</th>
                            <th>{{ __('msg.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buses as $bus)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bus->bus_number }}</td>
                                <td>{{ $bus->plate_number }}</td>
                                <td>{{ $bus->model }}</td>
                                <td>{{ $bus->capacity }}</td>
                                <td>{{ $bus->status }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                @if(Qs::userIsTeamSA())
                                                {{--Edit--}}
                                                <a href="{{ route('buses.edit', $bus->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                               @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                {{--Delete--}}
                                                <a id="{{ $bus->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                <form method="post" id="item-delete-{{ $bus->id }}" action="{{ route('buses.destroy', $bus->id) }}" class="hidden">@csrf @method('delete')</form>
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

            <div class="tab-pane fade" id="new-bus">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                            <span>{{ __('msg.bus_creation_note') }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form class="ajax-store" method="post" action="{{ route('buses.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.bus_number') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="bus_number" value="{{ old('bus_number') }}" required type="text" class="form-control" placeholder="{{ __('msg.bus_number') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.plate_number') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="plate_number" value="{{ old('plate_number') }}" required type="text" class="form-control" placeholder="{{ __('msg.plate_number') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.model') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="model" value="{{ old('model') }}" required type="text" class="form-control" placeholder="{{ __('msg.model') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.capacity') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input name="capacity" value="{{ old('capacity') }}" required type="number" class="form-control" placeholder="{{ __('msg.capacity') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.status') }}</label>
                                <div class="col-lg-9">
                                    <select required data-placeholder="{{ __('msg.select_status') }}" class="form-control select" name="status" id="status">
                                        <option {{ old('status') == 'active' ? 'selected' : '' }} value="active">{{ __('msg.active') }}</option>
                                        <option {{ old('status') == 'inactive' ? 'selected' : '' }} value="inactive">{{ __('msg.inactive') }}</option>
                                        <option {{ old('status') == 'maintenance' ? 'selected' : '' }} value="maintenance">{{ __('msg.maintenance') }}</option>
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