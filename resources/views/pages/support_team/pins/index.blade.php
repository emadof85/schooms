@extends('layouts.master')
@section('page_title', 'Exam Pins')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.exam_pins') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#valid-pins" class="nav-link active" data-toggle="tab">{{ __('msg.valid_pins') }}</a></li>
                <li class="nav-item"><a href="#used-pins" class="nav-link" data-toggle="tab"> {{ __('msg.used_pins') }}</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane fade show active" id="valid-pins">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>There are <strong>{{ $pin_count }}</strong> {{ __('msg.valid_pins_that_have_not_been_used') }}</span>
                            </div>
                        </div>
                    </div>

                    @foreach($valid_pins->chunk(4) as $chunk)
                        <div class="row">
                            @foreach($chunk as $vp)
                                <div class="col-md-3">{{ $vp->code }}</div>
                            @endforeach
                        </div>
                    @endforeach

                </div>

                {{--Used Pins--}}
                <div class="tab-pane fade" id="used-pins">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <div class="text-center">  <span>A total of <strong>{{ $used_pins->count() }}</strong> pin(s) have been used and may no longer be valid </span>

                                    <a id="used-pins" onclick="confirmDelete(this.id)" href="#" class="btn btn-danger btn-sm ml-2"><i class="icon-trash mr-1"></i> {{ __('msg.delete_all_used_pins') }}</a>
                                    <form method="post" id="item-delete-used-pins" action="{{ route('pins.destroy', 'used-pins') }}" class="hidden">@csrf @method('delete')</form>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table datatable-button-html5-columns">
                                <thead>
                                <tr>
                                    <th>{{ __('msg.sn') }}</th>
                                    <th>{{ __('msg.pin') }}</th>
                                    <th>{{ __('msg.used_by') }}</th>
                                    <th>{{ __('msg.user_type') }}</th>
                                    <th>{{ __('msg.used_for_student') }}</th>
                                    <th>{{ __('msg.date_used') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($used_pins as $up)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $up->code }}</td>
                                        <td><a href="{{ $up->user->user_type == 'student' ? route('students.show', Qs::hash(Qs::getSRByUserID($up->user->id)->id)) : route('users.show', Qs::hash($up->user->id)) }}">{{ $up->user->name }}</a></td>
                                        <td>{{ $up->user->user_type }}</td>
                                        <td><a href="{{ route('students.show', Qs::hash(Qs::getSRByUserID($up->student->id)->id))  }}">{{ $up->student->name }}</a></td>
                                        <td>{{ $up->updated_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{--Pins List Ends--}}

@endsection
