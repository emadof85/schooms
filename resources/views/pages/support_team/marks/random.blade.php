<form class="ajax-update" action="{{ route('marks.update', [$exam_id, $my_class_id, $section_id, $subject_id]) }}" method="post">
    @csrf @method('put')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('msg.sn') }}</th>
            <th>{{ __('msg.name_49ee') }}</th>
            <th>{{ __('msg.adm_no_42a0') }}</th>
            @if($class_type->code == 'J')
                <th>{{ __('msg.1st_ca_10') }}</th>
                <th>{{ __('msg.mt_ca_20') }}</th>
                <th>{{ __('msg.2nd_ca_10') }}</th>
                <th>{{ __('msg.exam_60') }}</th>
            @endif
            @if($class_type->code == 'S')
                <th>{{ __('msg.1st_ca_15') }}</th>
                <th>{{ __('msg.2nd_ca_15') }}</th>
                <th>{{ __('msg.exam_70') }}</th>
            @endif
            @if($class_type->code == 'P')
                <th>{{ __('msg.1st_ca') }} </th>
                <th>{{ __('msg.2nd_ca') }} </th>
                <th>{{ __('msg.mt_ca') }} </th>
                <th>{{ __('msg.exam') }}</th>
            @endif
            @if($class_type->code == 'N')
                <th>{{ __('msg.test') }} </th>
                <th>{{ __('msg.exam') }}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($marks->sortBy('user.name') as $mk)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mk->user->name }} </td>
                <td>{{ $mk->user->student_record->adm_no }}</td>

                @if($class_type->code == 'J')
                    <td><input min="1" max="10" class="w-50 text-center" name="t1_{{ $mk->id }}" value="{{ rand(1, 10) }}" type="number"></td>
                    <td><input min="1" max="20" class="w-50 text-center" name="t2_{{ $mk->id }}" value="{{ rand(1, 20) }}" type="number"></td>
                    <td><input min="1" max="10" class="w-50 text-center" name="t3_{{ $mk->id }}" value="{{ rand(1, 10) }}" type="number"></td>
                    <td><input min="1" max="60" class="w-50 text-center" name="exm_{{ $mk->id }}" value="{{ rand(1, 60) }}" type="number"></td>
                @endif

                @if($class_type->code == 'S')
                    <td><input min="1" max="15" class="w-50 text-center" name="t1_{{ $mk->id }}" value="{{ rand(1, 15) }}" type="number"></td>
                    <td><input min="1" max="15" class="w-50 text-center" name="t2_{{ $mk->id }}" value="{{ rand(1, 15) }}" type="number"></td>
                    <td><input min="1" max="70" class="w-50 text-center" name="exm_{{ $mk->id }}" value="{{ rand(1, 70) }}" type="number"></td>
                @endif

                @if($class_type->code == 'P')
                    <td><input min="1" class="w-50 text-center" name="t1_{{ $mk->id }}" value="{{ rand(1, 10) }}" type="number"></td>
                    <td><input min="1" class="w-50 text-center" name="t2_{{ $mk->id }}" value="{{ rand(1, 20) }}" type="number"></td>
                    <td><input min="1" class="w-50 text-center" name="t3_{{ $mk->id }}" value="{{ rand(1, 10) }}" type="number"></td>
                    <td><input min="1" class="w-50 text-center" name="exm_{{ $mk->id }}" value="{{ rand(1, 60) }}" type="number"></td>
                @endif

                @if($class_type->code == 'N')
                    <td><input min="1" max="40" class="w-50 text-center" name="t1_{{ $mk->id }}" value="{{ $rand(1, 40) }}" type="number"></td>
                    <td><input min="1" max="60" class="w-50 text-center" name="exm_{{ $mk->id }}" value="{{ rand(1, 60) }}" type="number"></td>
                @endif


            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-center mt-2">
        <button type="submit" class="btn btn-primary">Update Marks <i class="icon-paperplane ml-2"></i></button>
    </div>
</form>
