@extends('layouts.master')
@section('page_title', __('msg.edit_student'))
@section('content')

        <div class="card">
            <div class="card-header bg-white header-elements-inline">
                <h6 id="ajax-title" class="card-title">
                {!! __('msg.fill_to_edit_stud', [
                    'student' => $sr->user->name
                ]) !!}
                
                 </h6>

                {!! Qs::getPanelOptions() !!}
            </div>

            <form method="post" enctype="multipart/form-data" class="wizard-form steps-validation ajax-update" data-reload="#ajax-title" action="{{ route('students.update', Qs::hash($sr->id)) }}" data-fouc>
                @csrf @method('PUT')
                <h6>{{ __('msg.personal_data_6370') }}</h6>
                <fieldset>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('msg.full_name')}}: <span class="text-danger">*</span></label>
                                <input value="{{ $sr->user->name }}" required type="text" name="name" placeholder="{{ __('msg.full_name') }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{__('msg.address')}}: <span class="text-danger">*</span></label>
                                <input value="{{ $sr->user->address }}" class="form-control" placeholder="{{ __('msg.address') }}" name="address" type="text" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.email_address_2df9') }} <span class="text-danger">*</span></label>
                                <input value="{{ $sr->user->email  }}" type="email" name="email" class="form-control" placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="gender">{{__('msg.gender')}}: <span class="text-danger">*</span></label>
                                <select class="select form-control" id="gender" name="gender" required data-fouc data-placeholder="{{ __('msg.choose') }}">
                                    <option value=""></option>
                                    <option {{ ($sr->user->gender  == 'Male' ? 'selected' : '') }} value="Male">{{ __('msg.male') }}</option>
                                    <option {{ ($sr->user->gender  == 'Female' ? 'selected' : '') }} value="Female">{{ __('msg.female') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.phone_673a') }}</label>
                                <input value="{{ $sr->user->phone  }}" type="text" name="phone" class="form-control" placeholder="" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.telephone_2011') }}</label>
                                <input value="{{ $sr->user->phone2  }}" type="text" name="phone2" class="form-control" placeholder="" >
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.date_of_birth') }}</label>
                                <input name="dob" value="{{ $sr->user->dob  }}" type="text" class="form-control date-pick" placeholder="{{ __('msg.select_date') }}">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nal_id">{{__('msg.nationality')}}: <span class="text-danger">*</span></label>
                                <select data-placeholder="{{ __('msg.choose_ab25') }}" required name="nal_id" id="nal_id" class="select-search form-control">
                                    <option value=""></option>
                                    @foreach($nationals as $na)
                                        <option {{  ($sr->user->nal_id  == $na->id ? 'selected' : '') }} value="{{ $na->id }}">{{ $na->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="state_id">{{__('msg.state')}}: <span class="text-danger">*</span></label>
                            <select onchange="getLGA(this.value)" required data-placeholder="{{ __('msg.choose') }}" class="select-search form-control" name="state_id" id="state_id">
                                <option value=""></option>
                                @foreach($states as $st)
                                    <option {{ ($sr->user->state_id  == $st->id ? 'selected' : '') }} value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="lga_id">{{__('msg.lga')}}: <span class="text-danger">*</span></label>
                            <select required data-placeholder="{{ __('msg.select_state_first') }}" class="select-search form-control" name="lga_id" id="lga_id">
                                @if($sr->user->lga_id)
                                    <option selected value="{{ $sr->user->lga_id }}">{{ $sr->user->lga->name}}</option>
                                @endif
                            </select>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bg_id">{{ __('msg.blood_group_54e8') }} </label>
                                <select class="select form-control" id="bg_id" name="bg_id" data-fouc data-placeholder="{{ __('msg.choose') }}">
                                    <option value=""></option>
                                    @foreach(App\Models\BloodGroup::all() as $bg)
                                        <option {{ ($sr->user->bg_id  == $bg->id ? 'selected' : '') }} value="{{ $bg->id }}">{{ $bg->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">{{ __('msg.upload_passport_photo') }}</label>
                                <input value="{{ old('photo') }}" accept="image/*" type="file" name="photo" class="form-input-styled" data-fouc>
                                <span class="form-text text-muted">Accepted Images: jpeg, png. Max file size 2Mb</span>
                            </div>
                        </div>
                    </div>

                </fieldset>

                <h6>{{ __('msg.student_data') }}</h6>
                <fieldset>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="my_class_id">{{ __('msg.class_6788') }} </label>
                                <select onchange="getClassSections(this.value)" name="my_class_id" required id="my_class_id" class="form-control select-search" data-placeholder="{{ __('msg.select_class') }}">
                                    <option value=""></option>
                                    @foreach($my_classes as $c)
                                        <option {{ $sr->my_class_id == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="section_id">{{ __('msg.section_cba6') }} </label>
                                <select name="section_id" required id="section_id" class="form-control select" data-placeholder="{{ __('msg.select_section') }}">
                                    <option value="{{ $sr->section_id }}">{{ $sr->section->name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="my_parent_id">{{ __('msg.parent_f188') }} </label>
                                <select data-placeholder="{{ __('msg.choose_ab25') }}"  name="my_parent_id" id="my_parent_id" class="select-search form-control">
                                    <option  value=""></option>
                                    @foreach($parents as $p)
                                        <option {{ (Qs::hash($sr->parent_id) == Qs::hash($p->id)) ? 'selected' : '' }} value="{{ Qs::hash($p->id) }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year_admitted">{{ __('msg.year_admitted_af6b') }} </label>
                                <select name="year_admitted" data-placeholder="{{ __('msg.choose_ab25') }}" id="year_admitted" class="select-search form-control">
                                    <option value=""></option>
                                    @for($y=date('Y', strtotime('- 10 years')); $y<=date('Y'); $y++)
                                        <option {{ ($sr->year_admitted == $y) ? 'selected' : '' }} value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="dorm_id">{{ __('msg.dormitory_d6af') }} </label>
                            <select data-placeholder="{{ __('msg.choose_ab25') }}"  name="dorm_id" id="dorm_id" class="select-search form-control">
                                <option value=""></option>
                                @foreach($dorms as $d)
                                    <option {{ ($sr->dorm_id == $d->id) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('msg.dormitory_room_no_3e59') }}</label>
                                <input type="text" name="dorm_room_no" placeholder="{{ __('msg.dormitory_room_no') }}" class="form-control" value="{{ $sr->dorm_room_no }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="wd" value="1" {{ $sr->wd ? 'checked' : '' }}>
                                    {{ __('msg.withdrawn') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('msg.withdrawn_date') }}</label>
                                <input type="date" name="wd_date" class="form-control" value="{{ $sr->wd_date }}">
                            </div>
                        </div>
                    </div>
                </fieldset>

            </form>
        </div>
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