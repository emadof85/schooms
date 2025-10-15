<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info text-center">
            <span>{{ __('msg.add_time_slots_note', replace: ['nb' =>  __('msg.nb') ]) }}</span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header header-elements-inline bg-danger">
                <h6 class="font-weight-bold card-title">{{ __('msg.add_time_slots') }}</h6>
                {!! Qs::getPanelOptions() !!}
            </div>

            <div class="card-body collapse">
                <div class="col-md-12">
                    <form data-reload="#time_slots_table" class="ajax-store" method="post" action="{{ route('ts.store') }}">
                        @csrf
                        <input name="ttr_id" value="{{ $ttr->id }}" type="hidden">

                        {{--TIME BEGIN--}}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.start_time') }} <span
                                        class="text-danger">*</span></label>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.hour') }}" required class="select-search form-control" name="hour_from" id="hour_from">

                                    <option value=""></option>
                                    @for($t=1; $t<=12; $t++)
                                        <option {{ old('hour_from') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.minute') }}" required class="select-search form-control" name="min_from" id="min_from">

                                    <option value=""></option>
                                    <option value="00">00</option>
                                    <option value="05">05</option>
                                    @for($t=10; $t<=55; $t+=5)
                                        <option {{ old('min_from') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.meridian') }}" required class="select form-control" name="meridian_from" id="meridian_from">

                                    <option value=""></option>
                                    <option {{ old('meridian_from') == 'AM' ? 'selected' : '' }} value="AM">{{ __('msg.am') }}
                                    </option>
                                    <option {{ old('meridian_from') == 'PM' ? 'selected' : '' }} value="PM">{{ __('msg.pm') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{--TIME END--}}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.end_time') }} <span class="text-danger">*</span></label>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.hour') }}" required class="select-search form-control" name="hour_to" id="hour_to">

                                    <option value=""></option>
                                    @for($t=1; $t<=12; $t++)
                                        <option {{ old('hour_to') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.minute') }}" required class="select-search form-control" name="min_to" id="min_to">

                                    <option value=""></option>
                                    <option value="00">00</option>
                                    <option value="05">05</option>
                                    @for($t=10; $t<=55; $t+=5)
                                        <option {{ old('min_to') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <select data-placeholder="{{ __('msg.meridian') }}" required class="select form-control"
                                        name="meridian_to" id="meridian_to">

                                    <option value=""></option>
                                    <option {{ old('meridian_to') == 'AM' ? 'selected' : '' }} value="AM">{{ __('msg.am') }}
                                    </option>
                                    <option {{ old('meridian_to') == 'PM' ? 'selected' : '' }} value="PM">{{ __('msg.pm') }}
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="text-right">
                            <button  type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header header-elements-inline bg-dark">
                <h6 class="font-weight-bold card-title">{{ __('msg.use_existing_time_slots') }}</h6>
                {!! Qs::getPanelOptions() !!}
            </div>

            <div class="card-body collapse">
                <div class="col-md-12">
                    <form method="post" action="{{ route('ts.use', $ttr->id) }}">
                        @csrf

                        {{--TIME BEGIN--}}
                        <div class="form-group">
                            <label for="ttr_id" class="col-form-label-lg font-weight-semibold mb-lg-2">Select Existing Time Slots <span class="text-danger">*</span></label>

                            <div class="col-lg-8">
                                <select id="ttr_id" data-placeholder="{{ __('msg.select_7774') }}" required class="select-search form-control-lg" name="ttr_id">

                                    <option value=""></option>
                                    @foreach($ts_existing as $ttr_ts)
                                        <option value="{{ $ttr_ts->id }}">{{ $ttr_ts->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-lg btn-success">Submit form <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
