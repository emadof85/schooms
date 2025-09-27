@extends('layouts.master')
@section('page_title', __('msg.student_promotion'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">
                {!! __('msg.student_promotion_header', [
                    'old_year' => '<span class="text-danger">'.$old_year.'</span>',
                    'new_year' => '<span class="text-success">'.$new_year.'</span>'
                ]) !!}
            </h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.students.promotion.selector')
        </div>
    </div>

    @if($selected)
    <div class="card">
        <div class="card-header header-elements-inline">
            @php
                $from_class_name = $my_classes->where('id', $fc)->first()->name.' '.$sections->where('id', $fs)->first()->name;
                $to_class_name = $my_classes->where('id', $tc)->first()->name.' '.$sections->where('id', $ts)->first()->name;
            @endphp
            
            <h5 class="card-title font-weight-bold">
                {!! __('msg.promote_students_from_to', [
                    'from_class' => '<span class="text-teal">'.$from_class_name.'</span>',
                    'to_class' => '<span class="text-purple">'.$to_class_name.'</span>'
                ]) !!}
            </h5>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.students.promotion.promote')
        </div>
    </div>
    @endif


    {{--Student Promotion End--}}

@endsection
