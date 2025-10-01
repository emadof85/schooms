@extends('layouts.master')
@section('page_title', __('msg.manage_exams'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_exams') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-exams" class="nav-link active" data-toggle="tab">{{ __('msg.manage_exam') }}</a></li>
                <li class="nav-item"><a href="#new-exam" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> {{ __('msg.add_exam') }}</a></li>
            </ul>

            <div class="tab-content">
                    <div class="tab-pane fade show active" id="all-exams">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.name_49ee') }}</th>
                                <th>{{ __('msg.term_cf5f') }}</th>
                                <th>{{ __('msg.session') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($exams as $ex)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ex->name }}</td>
                                    <td>{{ 'Term '.$ex->term }}</td>
                                    <td>{{ $ex->year }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    @if(Qs::userIsTeamSA())
                                                    {{--Edit--}}
                                                    <a href="{{ route('exams.edit', $ex->id) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                   @endif
                                                    @if(Qs::userIsSuperAdmin())
                                                    {{--Delete--}}
                                                    <a id="{{ $ex->id }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                    <form method="post" id="item-delete-{{ $ex->id }}" action="{{ route('exams.destroy', $ex->id) }}" class="hidden">@csrf @method('delete')</form>
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

                <div class="tab-pane fade" id="new-exam">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                                <span>
                                    {!! __('msg.creating_exam_for_session', [
                                        'session' => '<strong>'.Qs::getSetting('current_session').'</strong>'
                                    ]) !!}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="{{ route('exams.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.name_49ee') }} <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text" class="form-control" placeholder="{{ __('msg.name_of_exam') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="term" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.term_cf5f') }}</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="{{ __('msg.select_teacher') }}" class="form-control select-search" name="term" id="term">
                                            <option {{ old('term') == 1 ? 'selected' : '' }} value="1">{{ __('msg.first_term') }}</option>
                                            <option {{ old('term') == 2 ? 'selected' : '' }} value="2">{{ __('msg.second_term') }}</option>
                                            <option {{ old('term') == 3 ? 'selected' : '' }} value="3">{{ __('msg.third_term') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">{{__('msg.submit_form') }} <i class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Class List Ends--}}

@endsection
