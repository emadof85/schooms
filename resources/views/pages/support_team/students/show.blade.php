@extends('layouts.master')
@section('page_title', 'Student Profile - '.$sr->user->name)
@section('content')
<div class="row">
    <div class="col-md-3 text-center">
        <div class="card">
            <div class="card-body">
                <img style="width: 90%; height:90%" src="{{ $sr->user->photo }}" alt="{{ __('msg.photo_5ae0') }}" class="rounded-circle">
                <br>
                <h3 class="mt-3">{{ $sr->user->name }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">{{ $sr->user->name }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
                    <div class="tab-pane fade show active" id="basic-info">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.name_49ee') }}</td>
                                <td>{{ $sr->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.adm_no_42a0') }}</td>
                                <td>{{ $sr->adm_no }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.class_9bd8') }}</td>
                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>
                            </tr>
                            @if($sr->my_parent_id)
                                <tr>
                                    <td class="font-weight-bold">{{ __('msg.parent') }}</td>
                                    <td>
                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.year_admitted') }}</td>
                                <td>{{ $sr->year_admitted }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.gender') }}</td>
                                <td>{{ $sr->user->gender }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.address') }}</td>
                                <td>{{ $sr->user->address }}</td>
                            </tr>
                            @if($sr->user->email)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.email') }}</td>
                                <td>{{$sr->user->email }}</td>
                            </tr>
                            @endif
                            @if($sr->user->phone)
                                <tr>
                                    <td class="font-weight-bold">{{ __('msg.phone') }}</td>
                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.birthday') }}</td>
                                <td>{{$sr->user->dob }}</td>
                            </tr>
                            @if($sr->user->bg_id)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.blood_group') }}</td>
                                <td>{{$sr->user->blood_group->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->nal_id)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.nationality') }}</td>
                                <td>{{$sr->user->nationality->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->state_id)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.state') }}</td>
                                <td>{{$sr->user->state->name }}</td>
                            </tr>
                            @endif
                            @if($sr->user->lga_id)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.lga') }}</td>
                                <td>{{$sr->user->lga->name }}</td>
                            </tr>
                            @endif
                            @if($sr->dorm_id)
                                <tr>
                                    <td class="font-weight-bold">{{ __('msg.dormitory') }}</td>
                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.withdrawn') }}</td>
                                <td><x-yes-no :value="$sr->wd" /></td>
                            </tr>
                            @if($sr->wd_date)
                            <tr>
                                <td class="font-weight-bold">{{ __('msg.withdrawn_date') }}</td>
                                <td>{{ $sr->wd_date }}</td>
                            </tr>
                            @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


    {{--Student Profile Ends--}}

@endsection
