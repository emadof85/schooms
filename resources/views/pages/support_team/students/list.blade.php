@extends('layouts.master')
@section('page_title', __('msg.student_information_1749') . ' - '.$my_class->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.students_list') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="mb-3">
                <div class="form-inline">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="include_withdrawn" name="include_withdrawn" value="1" {{ isset($include_withdrawn) && $include_withdrawn ? 'checked' : '' }}>
                        <label class="form-check-label ml-2" for="include_withdrawn">{{ __('msg.include_withdrawn') }}</label>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">{{ __('msg.all_class_students', ['name' => $my_class->name]) }}</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('msg.sections') }}</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($sections as $s)
                            <a href="#s{{ $s->id }}" class="dropdown-item" data-toggle="tab">{{ $my_class->name.' '.$s->name }}</a>
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
                            <th>{{ __('msg.email') }}</th>
                            <th>{{ __('msg.withdrawn') }}</th>
                            <th>{{ __('msg.withdrawn_date') }}</th>
                            <th>{{ __('msg.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $s)
                            <tr class="{{ $s->wd ? 'withdrawn-row' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user ? $s->user->photo : asset('user.png') }}" alt="photo"></td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $my_class->name.' '.$s->section->name }}</td>
                                <td>{{ $s->user ? $s->user->email : 'N/A' }}</td>
                                <td><x-yes-no :value="$s->wd" /></td>
                                <td>{{ $s->wd_date }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('students.show', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> {{ __('msg.view_profile') }}</a>
                                                @if(Qs::userIsTeamSA() && $s->user)
                                                    <a href="{{ route('students.edit', Qs::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                    <a href="{{ route('st.reset_pass', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> {{ __('msg.reset_password_3b5b') }}</a>
                                                @endif
                                                @if($s->user)
                                                    <a target="_blank" href="{{ route('marks.year_selector', Qs::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> {{ __('msg.marksheet') }}</a>
                                                @endif

                                                {{--Delete--}}
                                                @if(Qs::userIsSuperAdmin() && $s->user)
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

                @foreach($sections as $se)
                    <div class="tab-pane fade" id="s{{$se->id}}">                         <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.photo') }}</th>
                                <th>{{ __('msg.name_49ee') }}</th>
                                <th>{{ __('msg.adm_no_e965') }}</th>
                                <th>{{ __('msg.email') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students->where('section_id', $se->id) as $sr)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $sr->user ? $sr->user->photo : asset('user.png') }}" alt="photo"></td>
                                    <td>{{ $sr->user ? $sr->user->name : 'N/A' }}</td>
                                    <td>{{ $sr->adm_no }}</td>
                                    <td>{{ $sr->user ? $sr->user->email : 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="{{ route('students.show', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-eye"></i> {{ __('msg.view_info') }}</a>
                                                        @if(Qs::userIsTeamSA() && $sr->user)
                                                            <a href="{{ route('students.edit', Qs::hash($sr->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                            <a href="{{ route('st.reset_pass', Qs::hash($sr->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> {{ __('msg.reset_password_3b5b') }}</a>
                                                        @endif
                                                        @if($sr->user)
                                                            <a href="#" class="dropdown-item"><i class="icon-check"></i> {{ __('msg.marksheet') }}</a>
                                                        @endif
   
                                                        {{--Delete--}}
                                                        @if(Qs::userIsSuperAdmin() && $sr->user)
                                                            <a id="{{ Qs::hash($sr->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                            <form method="post" id="item-delete-{{ Qs::hash($sr->user->id) }}" action="{{ route('students.destroy', Qs::hash($sr->user->id)) }}" class="hidden">@csrf @method('delete')</form>
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

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cb = document.getElementById('include_withdrawn');
                const rows = document.querySelectorAll('tr.withdrawn-row');

                function updateRows() {
                    rows.forEach(r => {
                        if (cb.checked) {
                            r.style.display = '';
                        } else {
                            r.style.display = 'none';
                        }
                    });
                }

                // Initialize (hide withdrawn by default unless checkbox is checked server-side)
                updateRows();

                // Toggle on change
                cb.addEventListener('change', updateRows);
            });
        </script>
    @endsection
