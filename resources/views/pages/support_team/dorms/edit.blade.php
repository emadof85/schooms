@extends('layouts.master')
@section('page_title', __('msg.edit_dorm_name', ['name' => $dorm->name]))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_dorm') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-update" data-reload="#page-header" method="post" action="{{ route('dorms.update', $dorm->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.name_49ee') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $dorm->name }}" required type="text" class="form-control" placeholder="{{ __('msg.name_of_dormitory') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.description_b5a7') }}</label>
                            <div class="col-lg-9">
                                <input name="description" value="{{ $dorm->description }}"  type="text" class="form-control" placeholder="{{ __('msg.description_of_dormitory') }}">
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{__('msg.submit_form') }} <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--Class Edit Ends--}}

@endsection
