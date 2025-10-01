@extends('layouts.master')
@section('page_title', __('msg.manage_exam_marks'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-bold">{{ __('msg.fill_the_form_to_manage_marks') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.selector')
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <div class="row">
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.subject') }} </strong> {{ $m->subject->name }}</h6></div>
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.class_6788') }} </strong> {{ $m->my_class->name.' '.$m->section->name }}</h6></div>
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.exam_9410') }} </strong> {{ $m->exam->name.' - '.$m->year }}</h6></div>
            </div>
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.edit')
            {{--@include('pages.support_team.marks.random')--}}
        </div>
    </div>

    {{--Marks Manage End--}}

@endsection
