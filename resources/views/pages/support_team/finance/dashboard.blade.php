@extends('layouts.master')

@section('page_title', __('msg.finance_dashboard'))

@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">{{ __('msg.finance_dashboard') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="form-row mb-3">
            <div class="col-md-3">
                <select id="period_select" class="form-control">
                    <option value="month">{{ __('msg.this_month') }}</option>
                    <option value="year">{{ __('msg.this_year') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="load_dashboard" class="btn btn-primary">{{ __('msg.load') }}</button>
            </div>
        </div>

        <div class="row" id="dashboard_cards">
            <div class="col-md-12 text-center">
                <p>{{ __('msg.loading') }}...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('load_dashboard').addEventListener('click', loadDashboardData);

function loadDashboardData() {
    const period = document.getElementById('period_select').value;
    
    fetch(`/finance/dashboard/data?period=${period}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            updateDashboardCards(res.data);
        } else {
            alert(res.message || 'Error loading dashboard data');
        }
    })
    .catch(err => {
        alert('Error loading dashboard data');
        console.error(err);
    });
}

function updateDashboardCards(data) {
    const cardsContainer = document.getElementById('dashboard_cards');
    cardsContainer.innerHTML = `
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>{{ __('msg.total_income') }}</h6>
                    <h3>${formatCurrency(data.total_income || 0)}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>{{ __('msg.total_expense') }}</h6>
                    <h3>${formatCurrency(data.total_expense || 0)}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>{{ __('msg.salary_expense') }}</h6>
                    <h3>${formatCurrency(data.total_salaries || 0)}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card ${(data.net_balance || 0) >= 0 ? 'bg-primary' : 'bg-warning'} text-white">
                <div class="card-body">
                    <h6>{{ __('msg.net_balance') }}</h6>
                    <h3>${formatCurrency(data.net_balance || 0)}</h3>
                </div>
            </div>
        </div>
    `;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Load dashboard on page load
document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endsection