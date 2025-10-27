@extends('layouts.master')
@section('page_title', __('msg.edit_employee'))
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('msg.edit_employee') }}</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-store" method="post" action="{{ route('employees.update', $employee->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.user') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="user_id" required class="form-control select" data-placeholder="{{ __('msg.select_user') }}">
                                    <option value="">{{ __('msg.select_user') }}</option>
                                    @foreach(\App\User::whereNotIn('user_type', ['student', 'parent'])->get() as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.type') }} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="type" required class="form-control select" data-placeholder="{{ __('msg.select_type') }}">
                                    <option value="">{{ __('msg.select_type') }}</option>
                                    <option value="driver" {{ old('type', $employee->type) == 'driver' ? 'selected' : '' }}>{{ __('msg.driver') }}</option>
                                    <option value="staff" {{ old('type', $employee->type) == 'staff' ? 'selected' : '' }}>{{ __('msg.staff') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.license_number') }}</label>
                            <div class="col-lg-9">
                                <input name="license_number" value="{{ old('license_number', $employee->license_number) }}" type="text" class="form-control" placeholder="{{ __('msg.license_number') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.license_expiry') }}</label>
                            <div class="col-lg-9">
                                <input name="license_expiry" value="{{ old('license_expiry', $employee->license_expiry ? $employee->license_expiry->format('Y-m-d') : '') }}" type="date" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="active" class="col-lg-3 col-form-label font-weight-semibold">{{ __('msg.active') }}</label>
                            <div class="col-lg-9">
                                <select name="active" class="form-control select">
                                    <option value="1" {{ old('active', $employee->active) ? 'selected' : '' }}>{{ __('msg.yes') }}</option>
                                    <option value="0" {{ !old('active', $employee->active) ? 'selected' : '' }}>{{ __('msg.no') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button id="ajax-btn" type="submit" class="btn btn-primary">{{__('msg.update')}} <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
