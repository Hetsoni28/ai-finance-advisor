@extends('layouts.app')

@section('content')

@php
$analysis = $analysis ?? [];

$income      = (float)($analysis['totalIncome'] ?? 0);
$expense     = (float)($analysis['totalExpense'] ?? 0);
$savings     = (float)($analysis['savings'] ?? 0);
$rate        = (float)($analysis['savingRate'] ?? 0);
$score       = (int)($analysis['score'] ?? 0);
$risk        = $analysis['riskLevel'] ?? 'Stable';
$runway      = (int)($analysis['runway'] ?? 0);

$labels      = $analysis['labels'] ?? [];
$incomeData  = $analysis['incomeSeries'] ?? [];
$expenseData = $analysis['expenseSeries'] ?? [];
$netWorthData= $analysis['netWorthSeries'] ?? [];

$categoryLabels = $analysis['categoryLabels'] ?? [];
$categorySeries = $analysis['categorySeries'] ?? [];

/* Risk Color Logic (UI only) */
$riskColor = match(strtolower($risk)){
    'high','critical' => 'from-rose-600 to-red-600',
    'moderate'        => 'from-yellow-500 to-orange-500',
    default           => 'from-indigo-600 to-purple-600'
};
@endphp

<div class="max-w-[1600px] mx-auto px-6 py-12 space-y-12">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight">
                FinanceAI Enterprise Dashboard
            </h1>
            <p class="text-slate-500 mt-2">
                Advanced Financial Intelligence • Real-Time Metrics
            </p>
        </div>

        <div class="text-sm text-slate-500">
            Updated: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- KPI GRID --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- INCOME --}}
        <div class="kpi-card border-emerald-200">
            <p>Total Income</p>
            <h2 class="text-3xl font-bold text-emerald-600">
                ₹{{ number_format($income,2) }}
            </h2>
        </div>

        {{-- EXPENSE --}}
        <div class="kpi-card border-rose-200">
            <p>Total Expense</p>
            <h2 class="text-3xl font-bold text-rose-600">
                ₹{{ number_format($expense,2) }}
            </h2>
        </div>

        {{-- SAVINGS --}}
        <div class="kpi-card">
            <p>Net Savings</p>
            <h2 class="text-3xl font-bold">
                ₹{{ number_format($savings,2) }}
            </h2>
            <span class="saving-badge {{ $rate >= 20 ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ number_format($rate,1) }}% Rate
            </span>
        </div>

        {{-- AI SCORE --}}
        <div class="bg-gradient-to-br {{ $riskColor }} text-white rounded-2xl p-6 shadow-xl relative overflow-hidden">

            <p class="text-sm opacity-80">AI Stability Score</p>

            <h2 id="scoreCounter" 
                class="text-4xl font-extrabold mt-2">
                0
            </h2>

            <p class="text-sm mt-2 opacity-80">
                Risk: {{ $risk }}
            </p>

            <p class="text-xs mt-2 opacity-70">
                Runway: {{ $runway }} months
            </p>

            <div class="absolute right-4 bottom-4 opacity-10 text-8xl font-black">
                AI
            </div>
        </div>

    </div>


    {{-- CHARTS --}}
    @if(count($labels) > 0)

    <div class="grid lg:grid-cols-2 gap-6">

        <div class="chart-card">
            <h3>Cashflow Intelligence</h3>
            <div class="h-[320px]">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3>Net Worth Growth</h3>
            <div class="h-[320px]">
                <canvas id="netWorthChart"></canvas>
            </div>
        </div>

    </div>

    @else
        <div class="bg-white rounded-2xl p-10 text-center shadow">
            <h3 class="text-lg font-semibold text-slate-600">
                No Financial Data Yet
            </h3>
            <p class="text-sm text-slate-400 mt-2">
                Add income & expenses to generate insights.
            </p>
        </div>
    @endif


    {{-- CATEGORY --}}
    @if(count($categoryLabels) > 0)
    <div class="chart-card">
        <h3>Expense Category Breakdown</h3>
        <div class="h-[320px]">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
    @endif

</div>

{{-- STYLES --}}
<style>
.kpi-card{
    background:white;
    border-width:1px;
    border-radius:1rem;
    padding:1.5rem;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
    transition:.3s;
}
.kpi-card:hover{
    transform:translateY(-4px);
    box-shadow:0 12px 30px rgba(0,0,0,.08);
}
.kpi-card p{
    font-size:.875rem;
    color:#64748b;
}
.saving-badge{
    display:inline-block;
    margin-top:.5rem;
    font-size:.75rem;
    padding:.25rem .6rem;
    border-radius:999px;
}
.chart-card{
    background:white;
    border:1px solid #e2e8f0;
    border-radius:1rem;
    padding:2rem;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
}
.chart-card h3{
    font-weight:600;
    margin-bottom:1.5rem;
}
</style>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded',function(){

    /* Animated Score Counter */
    let score = {{ $score }};
    let counter = document.getElementById('scoreCounter');
    let current = 0;
    let interval = setInterval(()=>{
        current += Math.ceil(score/40);
        if(current >= score){
            current = score;
            clearInterval(interval);
        }
        counter.innerText = current;
    },30);


    const labels = @json($labels);
    const incomeData = @json($incomeData);
    const expenseData = @json($expenseData);
    const netWorthData = @json($netWorthData);
    const categoryLabels = @json($categoryLabels);
    const categorySeries = @json($categorySeries);

    function currency(val){
        return '₹'+new Intl.NumberFormat().format(val);
    }

    const gradient = (ctx, color1, color2)=>{
        const g = ctx.createLinearGradient(0,0,0,300);
        g.addColorStop(0,color1);
        g.addColorStop(1,color2);
        return g;
    };

    if(document.getElementById('financeChart')){
        const ctx = document.getElementById('financeChart').getContext('2d');
        new Chart(ctx,{
            type:'line',
            data:{
                labels:labels,
                datasets:[
                    {
                        label:'Income',
                        data:incomeData,
                        borderColor:'#10b981',
                        backgroundColor:gradient(ctx,'rgba(16,185,129,.3)','rgba(16,185,129,0)'),
                        fill:true,
                        tension:.4
                    },
                    {
                        label:'Expense',
                        data:expenseData,
                        borderColor:'#ef4444',
                        backgroundColor:gradient(ctx,'rgba(239,68,68,.3)','rgba(239,68,68,0)'),
                        fill:true,
                        tension:.4
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                interaction:{mode:'index',intersect:false},
                scales:{
                    y:{beginAtZero:true,ticks:{callback:v=>currency(v)}}
                }
            }
        });
    }

    if(document.getElementById('netWorthChart')){
        const ctx = document.getElementById('netWorthChart').getContext('2d');
        new Chart(ctx,{
            type:'line',
            data:{
                labels:labels,
                datasets:[{
                    label:'Net Worth',
                    data:netWorthData,
                    borderColor:'#6366f1',
                    backgroundColor:gradient(ctx,'rgba(99,102,241,.3)','rgba(99,102,241,0)'),
                    fill:true,
                    tension:.4
                }]
            },
            options:{responsive:true,maintainAspectRatio:false}
        });
    }

    if(document.getElementById('categoryChart')){
        new Chart(document.getElementById('categoryChart'),{
            type:'doughnut',
            data:{
                labels:categoryLabels,
                datasets:[{
                    data:categorySeries,
                    backgroundColor:[
                        '#6366f1','#10b981','#f59e0b',
                        '#ef4444','#8b5cf6','#0ea5e9'
                    ]
                }]
            },
            options:{responsive:true,maintainAspectRatio:false}
        });
    }

});
</script>
@endpush