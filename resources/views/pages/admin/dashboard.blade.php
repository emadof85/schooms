@extends('layouts.master')
@section('page_title', __('msg.dashboard'))

@section('content')
    <h2>WELCOME {{ Auth::user()->name }}. {{__('msg.your_dashboard') }}</h2>
    @endsection