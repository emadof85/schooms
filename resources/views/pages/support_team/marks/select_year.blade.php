@extends('layouts.master')
@section('page_title',  __('msg.select_exam_year') )
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-alarm mr-2"></i> {{ __('msg.select_exam_year') }}</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <form method="post" action="{{ route('marks.year_select', $student_id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="year" class="font-weight-bold col-form-label-lg">{{ __('msg.select_exam_year_31f9') }}</label>
                            <select required id="year" name="year" data-placeholder="{{ __('msg.select_exam_year') }}" class="form-control select select-lg">
                                @foreach($years as $y)
                                    <option value="{{ $y->year }}">{{ $y->year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-primary btn-lg">{{__('msg.submit')}} <i class="icon-paperplane ml-2"></i></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
