@extends('layouts.app')

@section('content')

@php
    $totalUsers      = (int) ($totalUsers ?? 0);
    $totalIncome     = (float) ($totalIncome ?? 0);
    $totalExpenses   = (float) ($totalExpenses ?? 0);

    $months          = $months ?? [];
    $monthlyIncome   = $monthlyIncome ?? [];
    $monthlyExpenses = $monthlyExpenses ?? [];
    $activities      = $activities ?? collect();

    $netRevenue = $totalIncome - $totalExpenses;

    $healthIndex = $totalIncome > 0
        ? round((($netRevenue / $totalIncome) * 100),1)
        : 0;

    $healthIndex = min(max($healthIndex, -100), 100);

    $isProfit = $netRevenue >= 0;
@endphp


<div class="min-h-screen bg-gradient-to-br 
    from-slate-50 via-white to-indigo-50
    dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

<div class="max-w-7xl mx-auto px-6 py-14 space-y-16">

{{-- ================= HEADER ================= --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6">

    <div>
        <h1 class="text-5xl font-black text-slate-900 dark:text-white">
            Admin Dashboard
        </h1>

        <p class="text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-3 text-sm">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            Real-time Financial Telemetry
        </p>
    </div>

    <div class="px-4 py-1.5 rounded-full text-xs font-bold
                bg-indigo-100 dark:bg-indigo-500/20
                text-indigo-700 dark:text-indigo-400">
        LIVE SYSTEM
    </div>
</div>


{{-- ================= KPI GRID ================= --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">

    {{-- Users --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-lg hover:-translate-y-1 transition">
        <p class="text-xs uppercase text-slate-400 font-bold tracking-wider">
            Total Users
        </p>
        <h3 class="text-4xl font-black text-slate-900 dark:text-white mt-4 counter"
            data-target="{{ $totalUsers }}">
            0
        </h3>
    </div>

    {{-- Income --}}
    <div class="bg-gradient-to-br from-emerald-50 to-white 
                dark:from-emerald-500/10 dark:to-slate-900
                rounded-3xl p-8 shadow-lg hover:-translate-y-1 transition">
        <p class="text-xs uppercase text-emerald-600 font-bold tracking-wider">
            Platform Income
        </p>
        <h3 class="text-4xl font-black text-emerald-600 mt-4 counter-currency"
            data-target="{{ $totalIncome }}">
            ₹0
        </h3>
    </div>

    {{-- Expenses --}}
    <div class="bg-gradient-to-br from-rose-50 to-white 
                dark:from-rose-500/10 dark:to-slate-900
                rounded-3xl p-8 shadow-lg hover:-translate-y-1 transition">
        <p class="text-xs uppercase text-rose-600 font-bold tracking-wider">
            Platform Expenses
        </p>
        <h3 class="text-4xl font-black text-rose-600 mt-4 counter-currency"
            data-target="{{ $totalExpenses }}">
            ₹0
        </h3>
    </div>

    {{-- Net Revenue --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-lg hover:-translate-y-1 transition">

        <p class="text-xs uppercase text-slate-400 font-bold tracking-wider">
            Net Revenue
        </p>

        <h3 class="text-4xl font-black mt-4 
                   {{ $isProfit ? 'text-indigo-600' : 'text-rose-600' }} counter-currency"
            data-target="{{ $netRevenue }}">
            ₹0
        </h3>

        <div class="mt-4 flex items-center gap-3 text-xs">
            <span class="px-2 py-1 rounded-full font-semibold
                {{ $isProfit ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                {{ $isProfit ? 'Profit' : 'Loss' }}
            </span>

            <span class="{{ $healthIndex >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                Health Index: {{ $healthIndex }}%
            </span>
        </div>

        {{-- Health Progress --}}
        <div class="mt-3 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
            <div class="h-2 transition-all duration-700
                {{ $healthIndex >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"
                style="width: {{ min(abs($healthIndex),100) }}%">
            </div>
        </div>

    </div>

</div>


{{-- ================= CHART ================= --}}
<div class="bg-white dark:bg-slate-900 rounded-3xl p-10 shadow-xl">

    <div class="flex justify-between mb-8">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            Monthly Income vs Expense
        </h2>
    </div>

    @if(count($months))
        <div class="h-[420px]">
            <canvas id="financeChart"></canvas>
        </div>
    @else
        <div class="text-center py-16 text-slate-500">
            No financial data available yet.
        </div>
    @endif

</div>


{{-- ================= ACTIVITY ================= --}}
<div class="bg-white dark:bg-slate-900 rounded-3xl p-10 shadow-xl">

    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-8">
        Recent Activity
    </h2>

    @if($activities->count())

        <ul class="space-y-5 text-sm">

            @foreach($activities as $activity)
            <li class="flex justify-between items-start border-b border-slate-200 dark:border-slate-700 pb-4 hover:bg-slate-50 dark:hover:bg-slate-800 px-2 rounded-xl transition">

                <div>
                    <p class="font-medium text-slate-800 dark:text-white">
                        {{ $activity->description ?? 'System event' }}
                    </p>
                </div>

                <span class="text-xs text-slate-400 whitespace-nowrap">
                    {{ optional($activity->created_at)->diffForHumans() }}
                </span>

            </li>
            @endforeach

        </ul>

    @else
        <div class="text-center text-slate-500 py-12">
            No recent activity
        </div>
    @endif

</div>

</div>
</div>


{{-- ================= SCRIPTS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function animateNumber(el, isCurrency = false) {
        let target = parseFloat(el.dataset.target) || 0;
        let start = 0;
        let duration = 1200;
        let startTime = null;

        function animate(time){
            if(!startTime) startTime = time;
            let progress = Math.min((time - startTime)/duration,1);
            let value = start + (target - start) * progress;

            if(isCurrency){
                el.innerText = '₹' + value.toLocaleString(undefined,{minimumFractionDigits:2});
            }else{
                el.innerText = Math.floor(value).toLocaleString();
            }

            if(progress < 1){
                requestAnimationFrame(animate);
            }
        }

        requestAnimationFrame(animate);
    }

    document.querySelectorAll('.counter')
        .forEach(el=>animateNumber(el,false));

    document.querySelectorAll('.counter-currency')
        .forEach(el=>animateNumber(el,true));


    const canvas = document.getElementById('financeChart');

    if(canvas && typeof Chart !== 'undefined'){

        new Chart(canvas,{
            type:'line',
            data:{
                labels:@json($months),
                datasets:[
                    {
                        label:'Income',
                        data:@json($monthlyIncome),
                        borderColor:'#10b981',
                        backgroundColor:'rgba(16,185,129,.12)',
                        fill:true,
                        tension:.4
                    },
                    {
                        label:'Expenses',
                        data:@json($monthlyExpenses),
                        borderColor:'#ef4444',
                        backgroundColor:'rgba(239,68,68,.12)',
                        fill:true,
                        tension:.4
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                interaction:{mode:'index',intersect:false},
                plugins:{
                    legend:{position:'bottom'}
                },
                scales:{
                    y:{
                        ticks:{
                            callback:value=>'₹'+value
                        }
                    }
                }
            }
        });

    }

});
</script>

@endsection