@extends('layouts.master')

@section('page_title')
    {{ __('msg.email_communication') }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        @livewire('communication.email-communication')
    </div>
</div>
@endsection