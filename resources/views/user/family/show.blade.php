@extends('layouts.app')

@section('content')

@php
    if(!$family || !$family->id){
        echo '<div class="text-center py-20 text-red-500 font-semibold">Family not found</div>';
        return;
    }

    $metrics = $metrics ?? [];
    $trend = $trend ?? ['months'=>[],'income'=>[],'expense'=>[]];
    $categories = $categories ?? [];

    $totalIncome = $metrics['total_income'] ?? 0;
    $totalExpense = $metrics['total_expense'] ?? 0;
    $balance = $metrics['balance'] ?? 0;
    $savingRate = $metrics['saving_rate'] ?? 0;

    $incomeGrowth = $metrics['income_growth'] ?? 0;
    $expenseGrowth = $metrics['expense_growth'] ?? 0;

    $health = $balance >= 0 ? 'Healthy' : 'At Risk';
@endphp

<div class="max-w-7xl mx-auto px-6 py-16">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-14">

        <div>
            <h1 class="text-4xl font-bold text-slate-800 tracking-tight">
                {{ $family->name }}
            </h1>

            <div class="flex items-center gap-3 mt-3">
                <span class="px-4 py-1 rounded-full text-xs font-semibold
                    {{ $balance >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                    {{ $health }}
                </span>

                <span class="text-sm text-slate-500">
                    Enterprise Finance Control Center
                </span>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">

            <a href="{{ route('user.incomes.create',['family_id'=>$family->id]) }}"
               class="btn btn-green">
                + Income
            </a>

            <a href="{{ route('user.expenses.create',['family_id'=>$family->id]) }}"
               class="btn btn-red">
                − Expense
            </a>

        </div>
    </div>


    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">

        <x-kpi-card
            title="Total Income"
            value="₹{{ number_format($totalIncome,2) }}"
            color="green"
            growth="{{ round($incomeGrowth) }}"
            positive="{{ $incomeGrowth >= 0 }}" />

        <x-kpi-card
            title="Total Expense"
            value="₹{{ number_format($totalExpense,2) }}"
            color="red"
            growth="{{ round($expenseGrowth) }}"
            positive="{{ $expenseGrowth < 0 }}" />

        <x-kpi-card
            title="Net Balance"
            value="₹{{ number_format($balance,2) }}"
            color="{{ $balance >= 0 ? 'green' : 'red' }}" />

        <x-kpi-card
            title="Saving Rate"
            value="{{ round($savingRate,2) }}%"
            color="cyan" />

    </div>


    {{-- CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">

        <div class="card h-[380px]">
            <h3 class="card-title">Financial Trend</h3>
            <canvas id="trendChart"></canvas>
        </div>

        <div class="card h-[380px]">
            <h3 class="card-title">Expense Distribution</h3>
            <canvas id="categoryChart"></canvas>
        </div>

    </div>


    {{-- INVITE SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- EMAIL INVITE --}}
        <div class="card">

            <h3 class="card-title mb-6">Invite Member</h3>

            <form method="POST"
                  action="{{ route('user.families.invite',$family->id) }}"
                  class="flex flex-col sm:flex-row gap-4"
                  id="inviteForm">

                @csrf

                <input type="email"
                       name="email"
                       required
                       placeholder="Enter member email"
                       class="input">

                <button class="btn btn-blue" id="inviteBtn">
                    <span class="btn-label">Send</span>
                    <span class="btn-loading hidden">Sending...</span>
                </button>

            </form>

        </div>

        {{-- LINK INVITE --}}
        <div class="card">

            <h3 class="card-title mb-6">Secure Invite Link</h3>

            <div class="flex gap-3">

                <input id="inviteLink"
                       readonly
                       value="{{ $inviteLink ?? '' }}"
                       class="input text-sm">

                <button type="button"
                        onclick="copyInviteLink()"
                        class="btn btn-outline">
                    Copy
                </button>

            </div>

            <div id="copyToast"
                 class="hidden mt-4 text-xs text-emerald-600 font-medium">
                Link copied successfully ✓
            </div>

        </div>

    </div>

</div>


{{-- STYLES --}}
<style>
.card{
    background:linear-gradient(135deg,#ffffff,#f8fafc);
    padding:2rem;
    border-radius:1.2rem;
    box-shadow:0 25px 70px rgba(0,0,0,.06);
    transition:.3s;
}
.card:hover{transform:translateY(-5px)}

.card-title{
    font-weight:600;
    font-size:1.05rem;
    margin-bottom:1rem;
    color:#334155;
}

.btn{
    padding:.75rem 1.5rem;
    border-radius:.75rem;
    font-weight:600;
    transition:.2s;
}

.btn-green{background:#10b981;color:#fff}
.btn-red{background:#ef4444;color:#fff}
.btn-blue{background:#2563eb;color:#fff}
.btn-outline{background:#e2e8f0;color:#334155}

.btn:hover{transform:translateY(-2px);opacity:.95}

.input{
    flex:1;
    padding:.75rem;
    border-radius:.7rem;
    border:1px solid #e2e8f0;
}
</style>


{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded',function(){

    const trendLabels = @json($trend['months']);
    const trendIncome = @json($trend['income']);
    const trendExpense = @json($trend['expense']);

    const ctxTrend = document.getElementById('trendChart');
    if(ctxTrend){
        new Chart(ctxTrend,{
            type:'line',
            data:{
                labels:trendLabels,
                datasets:[
                    {
                        label:'Income',
                        data:trendIncome,
                        borderColor:'#10b981',
                        tension:.4,
                        fill:true,
                        backgroundColor:'rgba(16,185,129,0.08)'
                    },
                    {
                        label:'Expense',
                        data:trendExpense,
                        borderColor:'#ef4444',
                        tension:.4,
                        fill:true,
                        backgroundColor:'rgba(239,68,68,0.08)'
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{legend:{position:'bottom'}}
            }
        });
    }

    const ctxCategory = document.getElementById('categoryChart');
    if(ctxCategory){
        new Chart(ctxCategory,{
            type:'doughnut',
            data:{
                labels:@json(array_keys($categories)),
                datasets:[{
                    data:@json(array_values($categories)),
                    backgroundColor:[
                        '#10b981','#3b82f6','#f59e0b',
                        '#ef4444','#8b5cf6','#06b6d4','#f97316'
                    ]
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{legend:{position:'bottom'}}
            }
        });
    }

});

function copyInviteLink(){
    const link = document.getElementById('inviteLink').value;
    if(!link) return;

    navigator.clipboard.writeText(link);
    document.getElementById('copyToast').classList.remove('hidden');
}
</script>

@endsection