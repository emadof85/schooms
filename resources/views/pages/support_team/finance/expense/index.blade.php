@extends('layouts.master')

@section('page_title', __('msg.expense_management'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.expense_management') }}</h6>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addExpenseModal">
            {{ __('msg.add_expense') }}
        </button>
    </div>

    <div class="card-body">
        <p>Expense management content will be loaded here.</p>
    </div>
</div>

@endsection