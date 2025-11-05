@extends('layouts.master')

@section('page_title')
    {{ __('msg.sms_communication') }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        @livewire('communication.sms-communication')
    </div>
</div>
@endsection