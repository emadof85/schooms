<div>
    {{--KEYS TO RATING--}}
    <div style="float: left">
        <br>
        <strong style="text-decoration: underline;">{{ __('msg.key') }}</strong> <br>
        <span>{{ __('msg.5_excellent') }}</span> <br>
        <span>{{ __('msg.4_very_good') }}</span> <br>
        <span>{{ __('msg.3_good') }}</span> <br>
        <span>{{ __('msg.2_fair') }}</span> <br>
        <span>{{ __('msg.1_poor') }}</span> <br>
    </div>

    <table align="left" style="width:40%; border-collapse:collapse; border: 1px solid #000; margin:10px 20px;" border="1">
        <thead>
        <tr>
            <td><strong>{{ __('msg.affective_traits') }}</strong></td>
            <td><strong>{{ __('msg.rating') }}</strong></td>
        </tr>
        </thead>
        <tbody>
        @foreach ($skills->where('skill_type', 'AF') as $af)
            <tr>
                <td>{{ $af->name }}</td>
                <td>{{ $exr->af ? explode(',', $exr->af)[$loop->index] : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table align="left" style="width:35%; border-collapse:collapse;border: 1px solid #000;  margin: 10px 20px;" border="1">
        <thead>
        <tr>
            <td><strong>{{ __('msg.psychomotor') }}</strong></td>
            <td><strong>{{ __('msg.rating') }}</strong></td>
        </tr>
        </thead>
        <tbody>
        @foreach ($skills->where('skill_type', 'PS') as $ps)
            <tr>
                <td>{{ $ps->name }}</td>
                <td>{{ $exr->ps ? explode(',', $exr->ps)[$loop->index] : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
