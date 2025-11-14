@extends('layouts.master')
@section('page_title', __('msg.manage_users'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.manage_users') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#new-user" class="nav-link active" data-toggle="tab">{{ __('msg.create_new_user') }}</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('msg.manage_users') }}</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($user_types as $ut)
                            <a href="#ut-{{ Qs::hash($ut->id) }}" class="dropdown-item" data-toggle="tab">{{ $ut->name }}s</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="new-user">
                    <form method="post" enctype="multipart/form-data" class="wizard-form steps-validation ajax-store" action="{{ route('users.store') }}" data-fouc>
                        @csrf
                    <h6>{{ __('msg.personal_data') }}</h6>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="user_type"> {{ __('msg.select_user') }}: <span class="text-danger">*</span></label>
                                        <select required data-placeholder="{{ __('msg.select_user') }}" class="form-control select" name="user_type" id="user_type">
                                @foreach($user_types as $ut)
                                    <option value="{{ Qs::hash($ut->id) }}">{{ $ut->name }}</option>
                                @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('msg.full_name') }}: <span class="text-danger">*</span></label>
                                        <input value="{{ old('name') }}" required type="text" name="name" placeholder="{{ __('msg.full_name') }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('msg.address') }}: <span class="text-danger">*</span></label>
                                        <input value="{{ old('address') }}" class="form-control" placeholder="{{ __('msg.address') }}" name="address" type="text" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('msg.email_address_2df9') }} </label>
                                        <input value="{{ old('email') }}" type="email" name="email" class="form-control" placeholder="your@email.com">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('msg.username_cac8') }} </label>
                                        <input value="{{ old('username') }}" type="text" name="username" class="form-control" placeholder="{{ __('msg.username') }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('msg.phone_673a') }}</label>
                                        <input value="{{ old('phone') }}" type="text" name="phone" class="form-control" placeholder="+2341234567" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('msg.telephone_2011') }}</label>
                                        <input value="{{ old('phone2') }}" type="text" name="phone2" class="form-control" placeholder="+2341234567" >
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('msg.date_of_employment') }}</label>
                                        <input autocomplete="off" name="emp_date" value="{{ old('emp_date') }}" type="text" class="form-control date-pick" placeholder="{{ __('msg.select_date') }}">

                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">{{ __('msg.password') }} </label>
                                        <input id="password" type="password" name="password" class="form-control"  >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">{{__('msg.gender')}}: <span class="text-danger">*</span></label>
                                        <select class="select form-control" id="gender" name="gender" required data-fouc data-placeholder="{{ __('msg.choose') }}">
                                            <option value=""></option>
                                            <option {{ (old('gender') == 'Male') ? 'selected' : '' }} value="Male">{{ __('msg.male') }}</option>
                                            <option {{ (old('gender') == 'Female') ? 'selected' : '' }} value="Female">{{ __('msg.female') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nal_id">{{__('msg.nationality')}}: <span class="text-danger">*</span></label>
                                        <select data-placeholder="{{ __('msg.choose_ab25') }}" required name="nal_id" id="nal_id" class="select-search form-control">
                                            <option value=""></option>
                                            @foreach($nationals as $nal)
                                                <option {{ (old('nal_id') == $nal->id ? 'selected' : '') }} value="{{ $nal->id }}">{{ $nal->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{--State--}}
                                <div class="col-md-6">
                                    <label for="state">{{__('msg.state')}}: <span class="text-danger">*</span></label>
                                    <input value="{{ old('state') }}" required type="text" name="state" placeholder="{{ __('msg.state') }}" class="form-control">
                                </div>
                                {{--BLOOD GROUP--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bg_id">{{ __('msg.blood_group_54e8') }} </label>
                                        <select class="select form-control" id="bg_id" name="bg_id" data-fouc data-placeholder="{{ __('msg.choose') }}">
                                            <option value=""></option>
                                            @foreach($blood_groups as $bg)
                                                <option {{ (old('bg_id') == $bg->id ? 'selected' : '') }} value="{{ $bg->id }}">{{ $bg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                {{--PASSPORT--}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="d-block">{{ __('msg.upload_passport_photo') }}</label>
                                        <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                                        <span class="form-text text-muted">Accepted Images: jpeg, png. Max file size 2Mb</span>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>

                @foreach($user_types as $ut)
                    <div class="tab-pane fade" id="ut-{{Qs::hash($ut->id)}}">                         <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>{{ __('msg.sn') }}</th>
                                <th>{{ __('msg.photo') }}</th>
                                <th>{{ __('msg.name_49ee') }}</th>
                                <th>{{ __('msg.username') }}</th>
                                <th>{{ __('msg.phone') }}</th>
                                <th>{{ __('msg.email') }}</th>
                                <th>{{ __('msg.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users->where('user_type', $ut->title) as $u)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $u->photo }}" alt="photo"></td>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->username }}</td>
                                    <td>{{ $u->phone }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{--View Profile--}}
                                                    <a href="{{ route('users.show', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-eye"></i> {{ __('msg.view_profile') }}</a>
                                                    {{--Edit--}}
                                                    <a href="{{ route('users.edit', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> {{ __('msg.edit') }}</a>
                                                @if(Qs::userIsSuperAdmin())

                                                        <a href="{{ route('users.reset_pass', Qs::hash($u->id)) }}" class="dropdown-item"><i class="icon-lock"></i> {{ __('msg.reset_password_3b5b') }}</a>
                                                        {{--Delete--}}
                                                        <a id="{{ Qs::hash($u->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> {{ __('msg.delete_f2a6') }}</a>
                                                        <form method="post" id="item-delete-{{ Qs::hash($u->id) }}" action="{{ route('users.destroy', Qs::hash($u->id)) }}" class="hidden">@csrf @method('delete')</form>
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
    		// --- Translations for jQuery Steps ---
            @php
                $wizardTranslations = [
                    'previous' => __('msg.previous'),
                    'next' => __('msg.next'),
                    'finish' => __('msg.finish'),
                ];
            @endphp

            const wizardLabels = {!! json_encode($wizardTranslations, JSON_UNESCAPED_UNICODE) !!};
            
            //
            // Wizard with validation
            //
    
            // Show form
            var form = $('.steps-validation').show();
            
            // Initialize wizard
            $('.steps-validation').steps({
                headerTag: 'h6',
                bodyTag: 'fieldset',
                titleTemplate: '<span class="number">#index#</span> #title#',
                labels: {
                    previous: '<i class="icon-arrow-{{app()->getLocale() == 'ar' ? 'right14': 'left13'}} mr-2" /> '+ wizardLabels.previous,
                    next: wizardLabels.next +' <i class="icon-arrow-{{app()->getLocale() == 'ar' ? 'left13': 'right14'}} ml-2" />',
                    finish: wizardLabels.finish +' <i class="icon-arrow-{{app()->getLocale() == 'ar' ? 'left13': 'right14'}} ml-2" />'
                },
                transitionEffect: 'fade',
                autoFocus: true,
                onStepChanging: function (event, currentIndex, newIndex) {
    
                    // Always allow previous action even if the current form is not valid!
                    if (currentIndex > newIndex) {
                        return true;
                    }
    
                    // Needed in some cases if the user went back (clean up)
                    if (currentIndex < newIndex) {
    
                        // To remove error styles
                        form.find('.body:eq(' + newIndex + ') label.error').remove();
                        form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
                    }
    
                    form.validate().settings.ignore = ':disabled,:hidden';
                    return form.valid();
                },
                onFinishing: function (event, currentIndex) {
                    form.validate().settings.ignore = ':disabled';
                    return form.valid();
                },
                onFinished: function (event, currentIndex) {
                    $(this).submit();
                }
            });
    
    
            // Initialize validation
            $('.steps-validation').validate({
                ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
                errorClass: 'validation-invalid-label',
                highlight: function(element, errorClass) {
                    $(element).removeClass(errorClass);
                },
                unhighlight: function(element, errorClass) {
                    $(element).removeClass(errorClass);
                },
    
                // Different components require proper error label placement
                errorPlacement: function(error, element) {
    
                    // Unstyled checkboxes, radios
                    if (element.parents().hasClass('form-check')) {
                        error.appendTo( element.parents('.form-check').parent() );
                    }
    
                    // Input with icons and Select2
                    else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                        error.appendTo( element.parent() );
                    }
    
                    // Input group, styled file input
                    else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                        error.appendTo( element.parent().parent() );
                    }
    
                    // Other elements
                    else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    email: {
                        email: true
                    }
                }
            });

        </script>
        
    @endsection