@extends('layouts.master')
@section('page_title', __('msg.students_graduated'))
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.students_graduated') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">{{ __('msg.all_graduated_students') }}</a></li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('msg.select_class') }}</a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach($my_classes as $c)
                    <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                    @endforeach
                </div>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-students">
                <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>{{ __('msg.sn') }}</th>
                        <th>{{ __('msg.photo') }}</th>
                        <th>{{ __('msg.name_49ee') }}</th>
                        <th>{{ __('msg.adm_no_e965') }}</th>
                        <th>{{ __('msg.section') }}</th>
                        <th>{{ __('msg.grad_year') }}</th>
                        <th>{{ __('msg.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                        <td>{{ $s->user->name }}</td>
                        <td>{{ $s->adm_no }}</td>
                        <td>{{ $s->my_class->name.' '.$s->section->name }}</td>
                        <td>{{ $s->grad_date }}</td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-left">
                                        <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> {{ __('msg.view_profile') }}</a>
                                        @if(Qs::userIsTeamSA())
                                        <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                        <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> {{ __('msg.reset_password_3b5b') }}</a>

                                        {{--Not Graduated--}}
                                        <a id="{{ Qs::hash($s->id) }}" href="#" onclick="$('form#ng-'+this.id).submit();" class="dropdown-item"><i class="icon-stairs-down"></i> {{ __('msg.not_graduated') }}</a>
                                            <form method="post" id="ng-{{ Qs::hash($s->id) }}" action="{{ route('st.not_graduated', Qs::hash($s->id)) }}" class="hidden">@csrf @method('put')</form>
                                        @endif

                                        <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> {{ __('msg.marksheet') }}</a>

                                        {{--Delete--}}
                                        @if(Qs::userIsSuperAdmin())
                                        <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                        <form method="post" id="item-delete-{{ Qs::hash($s->user->id) }}" action="{{ route('students.destroy', Qs::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($my_classes as $mc)
            <div class="tab-pane fade" id="c{{$mc->id}}">                                      <table class="table datatable-button-html5-columns">
                    <thead>
                    <tr>
                        <th>{{ __('msg.sn') }}</th>
                        <th>{{ __('msg.photo') }}</th>
                        <th>{{ __('msg.name_49ee') }}</th>
                        <th>{{ __('msg.adm_no_e965') }}</th>
                        <th>{{ __('msg.section') }}</th>
                        <th>{{ __('msg.grad_year') }}</th>
                        <th>{{ __('msg.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students->where('my_class_id', $mc->id) as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->adm_no }}</td>
                            <td>{{ $s->my_class->name.' '.$s->section->name }}</td>
                            <td>{{ $s->grad_date }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> {{ __('msg.view_profile') }}</a>
                                            @if(Qs::userIsTeamSA())
                                                <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> {{ __('msg.reset_password_3b5b') }}</a>

                                                {{--Not Graduated--}}
                                                <a id="{{ Qs::hash($s->id) }}" href="#" onclick="$('form#ng-'+this.id).submit();" class="dropdown-item"><i class="icon-stairs-down"></i> {{ __('msg.not_graduated') }}</a>
                                                <form method="post" id="ng-{{ Qs::hash($s->id) }}" action="{{ route('st.not_graduated', Qs::hash($s->id)) }}" class="hidden">@csrf @method('put')</form>
                                            @endif

                                            <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> {{ __('msg.marksheet') }}</a>

                                            {{--Delete--}}
                                            @if(Qs::userIsSuperAdmin())
                                                <a id="{{ Qs::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                <form method="post" id="item-delete-{{ Qs::hash($s->user->id) }}" action="{{ route('students.destroy', Qs::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

        </div>
    </div>
</div>

{{--Student List Ends--}}

@endsection
