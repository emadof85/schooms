<table class="table table-bordered table-responsive text-center">
    <thead>
    <tr>
        <th rowspan="2">{{ __('msg.sn') }}</th>
        <th rowspan="2">{{ __('msg.subjects') }}</th>
        <th rowspan="2">CA1<br>(20)</th>
        <th rowspan="2">CA2<br>(20)</th>
        <th rowspan="2">EXAMS<br>(60)</th>
        <th rowspan="2">TOTAL<br>(100)</th>

        {{--@if($ex->term == 3) --}}{{-- 3rd Term --}}{{--
        <th rowspan="2">TOTAL <br>(100%) 3<sup>{{ __('msg.rd') }}</sup> {{ __('msg.term') }}</th>
        <th rowspan="2">1<sup>{{ __('msg.st') }}</sup> <br> {{ __('msg.term') }}</th>
        <th rowspan="2">2<sup>{{ __('msg.nd') }}</sup> <br> {{ __('msg.term') }}</th>
        <th rowspan="2">CUM (300%) <br> 1<sup>{{ __('msg.st') }}</sup> + 2<sup>{{ __('msg.nd') }}</sup> + 3<sup>{{ __('msg.rd') }}</sup></th>
        <th rowspan="2">{{ __('msg.cum_ave') }}</th>
        @endif--}}

        <th rowspan="2">{{ __('msg.grade') }}</th>
        <th rowspan="2">SUBJECT <br> {{ __('msg.position') }}</th>
        <th rowspan="2">{{ __('msg.remarks') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach($subjects as $sub)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $sub->name }}</td>
            @foreach($marks->where('subject_id', $sub->id)->where('exam_id', $ex->id) as $mk)
                <td>{{ ($mk->t1) ?: '-' }}</td>
                <td>{{ ($mk->t2) ?: '-' }}</td>
                <td>{{ ($mk->exm) ?: '-' }}</td>
                <td>
                    @if($ex->term === 1) {{ ($mk->tex1) }}
                    @elseif ($ex->term === 2) {{ ($mk->tex2) }}
                    @elseif ($ex->term === 3) {{ ($mk->tex3) }}
                    @else {{ '-' }}
                    @endif
                </td>

                {{--3rd Term--}}
                {{-- @if($ex->term == 3)
                     <td>{{ $mk->tex3 ?: '-' }}</td>
                     <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 1, $mk->my_class_id, $year) }}</td>
                     <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 2, $mk->my_class_id, $year) }}</td>
                     <td>{{ $mk->cum ?: '-' }}</td>
                     <td>{{ $mk->cum_ave ?: '-' }}</td>
                 @endif--}}

                {{--Grade, Subject Position & Remarks--}}
                <td>{{ ($mk->grade) ? $mk->grade->name : '-' }}</td>
                <td>{!! ($mk->grade) ? Mk::getSuffix($mk->sub_pos) : '-' !!}</td>
                <td>{{ ($mk->grade) ? $mk->grade->remark : '-' }}</td>
            @endforeach
        </tr>
    @endforeach
    <tr>
        <td colspan="4"><strong>{{ __('msg.total_scores_obtained') }} </strong> {{ $exr->total }}</td>
        <td colspan="3"><strong>{{ __('msg.final_average') }} </strong> {{ $exr->ave }}</td>
        <td colspan="2"><strong>{{ __('msg.class_average') }} </strong> {{ $exr->class_ave }}</td>
    </tr>
    </tbody>
</table>
