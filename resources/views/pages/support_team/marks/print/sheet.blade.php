{{--<!--NAME , CLASS AND OTHER INFO -->--}}
<table style="width:100%; border-collapse:collapse; ">
    <tbody>
    <tr>
        <td><strong>{{ __('msg.name') }}</strong> {{ strtoupper($sr->user->name) }}</td>
        <td><strong>{{ __('msg.adm_no') }}</strong> {{ $sr->adm_no }}</td>
        <td><strong>{{ __('msg.house') }}</strong> {{ strtoupper($sr->house) }}</td>
        <td><strong>{{ __('msg.class') }}</strong> {{ strtoupper($my_class->name) }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('msg.report_sheet_for') }}</strong> {!! strtoupper(Mk::getSuffix($ex->term)) !!} TERM </td>
        <td><strong>{{ __('msg.academic_year') }}</strong> {{ $ex->year }}</td>
        <td><strong>{{ __('msg.age') }}</strong> {{ $sr->age ?: ($sr->user->dob ? date_diff(date_create($sr->user->dob), date_create('now'))->y : '-') }}</td>
    </tr>

    </tbody>
</table>


{{--Exam Table--}}
<table style="width:100%; border-collapse:collapse; border: 1px solid #000; margin: 10px auto;" border="1">
    <thead>
    <tr>
        <th rowspan="2">{{ __('msg.subjects') }}</th>
        <th colspan="3">{{ __('msg.continuous_assessment') }}</th>
        <th rowspan="2">{{ __('msg.exam') }}<br>(60)</th>
        <th rowspan="2">FINAL MARKS <br> (100%)</th>
        <th rowspan="2">{{ __('msg.grade') }}</th>
        <th rowspan="2">SUBJECT <br> {{ __('msg.position') }}</th>


      {{--  @if($ex->term == 3) --}}{{-- 3rd Term --}}{{--
        <th rowspan="2">FINAL MARKS <br>(100%) 3<sup>{{ __('msg.rd') }}</sup> {{ __('msg.term') }}</th>
        <th rowspan="2">1<sup>{{ __('msg.st') }}</sup> <br> {{ __('msg.term') }}</th>
        <th rowspan="2">2<sup>{{ __('msg.nd') }}</sup> <br> {{ __('msg.term') }}</th>
        <th rowspan="2">CUM (300%) <br> 1<sup>{{ __('msg.st') }}</sup> + 2<sup>{{ __('msg.nd') }}</sup> + 3<sup>{{ __('msg.rd') }}</sup></th>
        <th rowspan="2">{{ __('msg.cum_ave') }}</th>
        <th rowspan="2">{{ __('msg.grade') }}</th>
        @endif--}}

        <th rowspan="2">{{ __('msg.remarks') }}</th>
    </tr>
    <tr>
        <th>{{ __('msg.ca120') }}</th>
        <th>{{ __('msg.ca220') }}</th>
        <th>{{ __('msg.total40') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($subjects as $sub)
        <tr>
            <td style="font-weight: bold">{{ $sub->name }}</td>
            @foreach($marks->where('subject_id', $sub->id)->where('exam_id', $ex->id) as $mk)
                <td>{{ $mk->t1 ?: '-' }}</td>
                <td>{{ $mk->t2 ?: '-' }}</td>
                <td>{{ $mk->tca ?: '-' }}</td>
                <td>{{ $mk->exm ?: '-' }}</td>

                <td>{{ $mk->$tex ?: '-'}}</td>
                <td>{{ $mk->grade ? $mk->grade->name : '-' }}</td>
                <td>{!! ($mk->grade) ? Mk::getSuffix($mk->sub_pos) : '-' !!}</td>
                <td>{{ $mk->grade ? $mk->grade->remark : '-' }}</td>

                {{--@if($ex->term == 3)
                    <td>{{ $mk->tex3 ?: '-' }}</td>
                    <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 1, $mk->my_class_id, $year) }}</td>
                    <td>{{ Mk::getSubTotalTerm($student_id, $sub->id, 2, $mk->my_class_id, $year) }}</td>
                    <td>{{ $mk->cum ?: '-' }}</td>
                    <td>{{ $mk->cum_ave ?: '-' }}</td>
                    <td>{{ $mk->grade ? $mk->grade->name : '-' }}</td>
                    <td>{{ $mk->grade ? $mk->grade->remark : '-' }}</td>
                @endif--}}

            @endforeach
        </tr>
    @endforeach
    <tr>
        <td colspan="3"><strong>{{ __('msg.total_scores_obtained') }} </strong> {{ $exr->total }}</td>
        <td colspan="3"><strong>{{ __('msg.final_average') }} </strong> {{ $exr->ave }}</td>
        <td colspan="3"><strong>{{ __('msg.class_average') }} </strong> {{ $exr->class_ave }}</td>
    </tr>
    </tbody>
</table>
