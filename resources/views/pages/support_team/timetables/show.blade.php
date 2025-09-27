@extends('layouts.master')
@section('page_title', 'View TimeTable')
@section('content')

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.name_4e14') }} </strong> {{ $ttr->name }}</h6></div>
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.class_6788') }} </strong> {{ $my_class->name }}</h6></div>
                <div class="col-md-4"><h6 class="card-title"><strong>{{ __('msg.year_06f8') }} </strong> {{ ($ttr->exam_id) ? 'Exam TimeTable' : 'Class TimeTable' }} {{ '('.$ttr->year.')' }}</h6></div>
            </div>
        </div>
            <div class="card-body">
                <table class="table table-responsive table-striped">
                    <thead>
                    <tr>
                        <th rowspan="2">Time <i class="icon-arrow-right7 ml-2"></i> <br> {{ __('msg.date') }}<i class="icon-arrow-down7 ml-2"></i>
                        </th>
                        @foreach($time_slots as $tms)
                            <th rowspan="2">{{ $tms->time_from }} <br>
                            {{ $tms->time_to }}
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $day)
                            <tr>
                                @if($ttr->exam_id)
                                <td><strong>{{ date('l', strtotime($day)) }} <br> {{ date('d/m/Y', strtotime($day)) }} </strong></td>
                                @else
                                <td><strong>{{ $day }}</strong></td>
                                @endif
                                @foreach($d_time->where('day', $day) as $dt)
                                <td>{{ $dt['subject'] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{--Print Button--}}
                <div class="text-center mt-4">
                    <a target="_blank" href="{{ route('ttr.print', $ttr->id) }}" class="btn btn-danger btn-lg"><i class="icon-printer mr-2"></i> {{ __('msg.print_timetable') }}</a>
                </div>
            </div>
        </div>

@endsection