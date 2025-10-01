@extends('layouts.master')
@section('page_title', __('msg.student_marksheet'))
@section('content')

    <div class="card">
        <div class="card-header text-center">
            @php
                $student_info = $sr->user->name.' ('.$my_class->name.' '.$my_class->section->first()->name.')';
            @endphp
            <h4 class="card-title font-weight-bold">{{ __('msg.student_marksheet_for', ['student_info' => $student_info]) }}</h4>
        </div>
    </div>

    @foreach($exams as $ex)
        @foreach($exam_records->where('exam_id', $ex->id) as $exr)

                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h6 class="font-weight-bold">{{ $ex->name.' - '.$ex->year }}</h6>
                        {!! Qs::getPanelOptions() !!}
                    </div>

                    <div class="card-body collapse">

                        {{--Sheet Table--}}
                        @include('pages.support_team.marks.show.sheet')

                        {{--Print Button--}}
                        <div class="text-center mt-3">
                            <a target="_blank" href="{{ route('marks.print', [Qs::hash($student_id), $ex->id, $year]) }}" class="btn btn-secondary btn-lg">{{ __('msg.print_marksheet') }} <i class="icon-printer ml-2"></i></a>
                        </div>

                    </div>

                </div>

            {{--    EXAM COMMENTS   --}}
            @include('pages.support_team.marks.show.comments')

            {{-- SKILL RATING --}}
            @include('pages.support_team.marks.show.skills')

        @endforeach
    @endforeach

@endsection
