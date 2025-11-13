@extends('layouts.master')
@section('page_title', 'Manage System Settings')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-semibold">{{ __('msg.update_system_settungs') }} </h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form enctype="multipart/form-data" method="post" action="{{ route('settings.update') }}">
                @csrf @method('PUT')

                {{-- Language Tabs --}}
                <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                    @foreach($supported_languages as $lang_code => $lang_name)
                        <li class="nav-item">
                            <a href="#{{ $lang_code }}-tab" class="nav-link {{ $lang_code === $default_locale ? 'active' : '' }}" data-toggle="tab">{{ $lang_name }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($supported_languages as $lang_code => $lang_name)
                        {{-- {{ ucfirst($lang_name) }} Tab --}}
                        <div class="tab-pane fade {{ $lang_code === $default_locale ? 'show active' : '' }}" id="{{ $lang_code }}-tab">
                            <div class="row mt-3">
                                <div class="col-md-6 border-right-2 border-right-blue-400">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.name_of_school') }} {!! $lang_code === $default_locale ? '<span class="text-danger">*</span>' : '' !!}</label>
                                        <div class="col-lg-9">
                                            <input name="system_name_{{ $lang_code }}" value="{{ $s['system_name_' . $lang_code] ?? ($lang_code === $default_locale ? ($s['system_name'] ?? '') : '') }}" {{ $lang_code === $default_locale ? 'required' : '' }} type="text" class="form-control" placeholder="{{ __('msg.name_of_school') }}" {{ $lang_code === 'ar' ? 'dir="rtl"' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.school_acronym') }}</label>
                                        <div class="col-lg-9">
                                            <input name="system_title_{{ $lang_code }}" value="{{ $s['system_title_' . $lang_code] ?? ($lang_code === $default_locale ? ($s['system_title'] ?? '') : '') }}" type="text" class="form-control" placeholder="{{ __('msg.school_acronym') }}" {{ $lang_code === 'ar' ? 'dir="rtl"' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.school_address') }} {!! $lang_code === $default_locale ? '<span class="text-danger">*</span>' : '' !!}</label>
                                        <div class="col-lg-9">
                                            <input {{ $lang_code === $default_locale ? 'required' : '' }} name="address_{{ $lang_code }}" value="{{ $s['address_' . $lang_code] ?? ($lang_code === $default_locale ? ($s['address'] ?? '') : '') }}" type="text" class="form-control" placeholder="{{ __('msg.school_address') }}" {{ $lang_code === 'ar' ? 'dir="rtl"' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            <div class="row">
                <div class="col-md-6 border-right-2 border-right-blue-400">
                        <div class="form-group row">
                            <label for="default_language" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.default_language') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required name="default_language" id="default_language" class="form-control">
                                    @foreach($supported_languages as $lang_code => $lang_name)
                                        <option value="{{ $lang_code }}" {{ ($s['default_language'] ?? $default_locale) === $lang_code ? 'selected' : '' }}>{{ $lang_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="current_session" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.current_session') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select data-placeholder="{{ __('msg.choose_ab25') }}" required name="current_session" id="current_session" class="select-search form-control">
                                    <option value=""></option>
                                    @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                        <option {{ ($s['current_session'] == (($y-=1).'-'.($y+=1))) ? 'selected' : '' }}>{{ ($y-=1).'-'.($y+=1) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.phone') }}</label>
                            <div class="col-lg-9">
                                <input name="phone" value="{{ $s['phone'] }}" type="text" class="form-control" placeholder="{{ __('msg.phone') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.school_email') }}</label>
                            <div class="col-lg-9">
                                <input name="system_email" value="{{ $s['system_email'] }}" type="email" class="form-control" placeholder="{{ __('msg.school_email') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.this_term_ends') }}</label>
                            <div class="col-lg-6">
                                <input name="term_ends" value="{{ $s['term_ends'] }}" type="text" class="form-control date-pick" placeholder="{{ __('msg.date_term_ends') }}">
                            </div>
                            <div class="col-lg-3 mt-2">
                                <span class="font-weight-bold font-italic">{{ __('msg.m_d_y_or_mdy') }} </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.next_term_begins_e0d5') }}</label>
                            <div class="col-lg-6">
                                <input name="term_begins" value="{{ $s['term_begins'] }}" type="text" class="form-control date-pick" placeholder="{{ __('msg.date_term_ends') }}">
                            </div>
                            <div class="col-lg-3 mt-2">
                                <span class="font-weight-bold font-italic">{{ __('msg.m_d_y_or_mdy') }} </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="lock_exam" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.lock_exam') }}</label>
                            <div class="col-lg-3">
                                <select class="form-control select" name="lock_exam" id="lock_exam">
                                    <option {{ $s['lock_exam'] ? 'selected' : '' }} value="1">{{ __('msg.yes') }}</option>
                                    <option {{ $s['lock_exam'] ?: 'selected' }} value="0">{{ __('msg.no') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                    <span class="font-weight-bold font-italic text-info-800">{{ __('msg.lock_exam') }}</span>
                            </div>
                        </div>
                </div>
                <div class="col-md-6">
                    {{--Fees--}}
               <fieldset>
                   <legend><strong>{{ __('msg.next_term_fees_ef4a') }}</strong></legend>
                   @foreach($class_types as $ct)
                   <div class="form-group row">
                       <label class="col-lg-3 col-form-label font-weight-semibold">{{ $ct->name }}</label>
                       <div class="col-lg-9">
                           <input class="form-control" value="{{ $s['next_term_fees_'.strtolower($ct->code)] }}" name="next_term_fees_{{ strtolower($ct->code) }}" placeholder="{{ $ct->name }}" type="text">
                       </div>
                   </div>
                       @endforeach
               </fieldset>
                    <hr class="divider">

                    {{--Logo--}}
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.change_logo') }}</label>
                        <div class="col-lg-9">
                            <div class="mb-3">
                                <img style="width: 100px" height="100px" src="{{ $s['logo'] }}" alt="">
                            </div>
                            <input name="logo" accept="image/*" type="file" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc>
                        </div>
                    </div>
                </div>
            </div>

                <hr class="divider">

                <div class="text-right">
                    <button type="submit" class="btn btn-danger">Submit form <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>

    {{--Settings Edit Ends--}}

@endsection
