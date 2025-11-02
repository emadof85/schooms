@extends('layouts.master')
@section('page_title', __('msg.dashboard') )
@section('content')

    {{--Livewire Dashboard Component--}}
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('msg.dashboard') }}</h5>
         {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <livewire:dashboard />
        </div>
    </div>

@endsection