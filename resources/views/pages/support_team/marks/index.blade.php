@extends('layouts.master')
@section('page_title', __('msg.manage_exam_marks'))
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2"></i> {{ __('msg.manage_exam_marks') }}</h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.selector')
        </div>
    </div>
    @endsection
