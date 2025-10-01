@extends('layouts.master')
@section('page_title', __('msg.edit_subject_name', ['name' => $ex->name, 'year'=>$ex->year]))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_exam') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form method="post" action="{{ route('exams.update', $ex->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.name_49ee') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $ex->name }}" required type="text" class="form-control" placeholder="{{ __('msg.name_of_exam') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="term" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.term_cf5f') }}</label>
                            <div class="col-lg-9">
                                <select data-placeholder="{{ __('msg.select_teacher') }}" class="form-control select-search" name="term" id="term">
                                    <option {{ $ex->term == 1 ? 'selected' : '' }} value="1">{{ __('msg.first_term') }}</option>
                                    <option {{ $ex->term == 2 ? 'selected' : '' }} value="2">{{ __('msg.second_term') }}</option>
                                    <option {{ $ex->term == 3 ? 'selected' : '' }} value="3">{{ __('msg.third_term') }}</option>
                                </select>
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
