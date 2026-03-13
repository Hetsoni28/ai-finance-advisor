@extends('layouts.app')

@section('content')

@php
$total = (float)($total ?? 0);
$topCategory = $topCategory ?? null;
$latestDate = $latest ?? null;

/* Safe monthly grouping */
$categoryData = $expenses->groupBy('category')
    ->map(fn($g) => $g->sum('amount'));

$monthlyData = $expenses->groupBy(function($e){
        return $e->expense_date
            ? $e->expense_date->format('M Y')
            : optional($e->created_at)->format('M Y');
    })->map(fn($g) => $g->sum('amount'));

$monthlyValues = $monthlyData->values();
$lastMonth = $monthlyValues->last() ?? 0;
$prevMonth = $monthlyValues->slice(-2,1)->first() ?? 0;
$growth = $prevMonth > 0 ? (($lastMonth-$prevMonth)/$prevMonth)*100 : 0;
@endphp

<div class="max-w-[1600px] mx-auto px-6 py-16 space-y-14">

{{-- HEADER --}}
<div class="flex flex-col xl:flex-row justify-between gap-6">
    <div>
        <h1 class="text-4xl font-extrabold">
            Expense Intelligence
        </h1>

        <p class="text-sm text-slate-500 mt-2">
            Personal expenses • Live analytics engine
        </p>

        <p class="text-xs text-slate-400 mt-1">
            Last updated {{ $latestDate ? $latestDate->diffForHumans() : '—' }}
        </p>
    </div>

    <div class="flex gap-3 items-center">
        @if(Route::has('user.expenses.export.pdf'))
            <a href="{{ route('user.expenses.export.pdf') }}"
               class="btn-dark">
                Export PDF
            </a>
        @endif

        <a href="{{ route('user.expenses.create') }}"
           class="btn-primary">
            + Add Expense
        </a>
    </div>
</div>


{{-- KPI --}}
<div class="grid md:grid-cols-3 gap-8">

    <div class="kpi-card">
        <p>Total Spending</p>
        <h2 class="kpi-value text-rose-600"
            data-target="{{ $total }}">
            ₹0
        </h2>

        <span class="text-xs mt-3 block
            {{ $growth >=0 ? 'text-rose-600' : 'text-emerald-600' }}">
            {{ number_format($growth,1) }}% vs last month
        </span>
    </div>

    <div class="kpi-card">
        <p>Top Category</p>
        <h2 class="text-xl font-semibold mt-3">
            {{ $topCategory ?? '—' }}
        </h2>
    </div>

    <div class="kpi-gradient">
        <p class="opacity-80 text-xs uppercase">AI Insight</p>
        <p class="text-sm mt-3">
            {{ $topCategory
                ? "Spending heavily in {$topCategory}. Optimize budget allocation."
                : "No dominant pattern detected yet." }}
        </p>
    </div>

</div>


{{-- CHARTS --}}
<div class="grid lg:grid-cols-2 gap-10">

    <div class="chart-card">
        <h3>Category Breakdown</h3>
        <div class="h-[320px]">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3>Monthly Expense Trend</h3>
        <div class="h-[320px]">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

</div>


{{-- FILTER --}}
<form method="GET"
      action="{{ route('user.expenses.index') }}"
      id="filterForm"
      class="filter-card">

    <div class="grid md:grid-cols-5 gap-4">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search title…"
               class="filter-input">

        <select name="category" class="filter-input">
            <option value="">All Categories</option>
            @foreach(['Food','Travel','Bills','Shopping'] as $cat)
                <option value="{{ $cat }}"
                    {{ request('category')===$cat?'selected':'' }}>
                    {{ $cat }}
                </option>
            @endforeach
        </select>

        <input type="date"
               name="from"
               value="{{ request('from') }}"
               class="filter-input">

        <input type="date"
               name="to"
               value="{{ request('to') }}"
               class="filter-input">

        <div class="flex gap-2">
            <button class="btn-rose">
                Apply
            </button>

            <a href="{{ route('user.expenses.index') }}"
               class="btn-light">
                Reset
            </a>
        </div>
    </div>
</form>


{{-- TABLE --}}
<div class="table-card">

<table class="w-full text-sm">

<thead class="bg-slate-100 text-xs uppercase">
<tr>
<th class="p-4 text-left">Title</th>
<th class="p-4 text-center">Category</th>
<th class="p-4 text-center">Date</th>
<th class="p-4 text-right">Amount</th>
<th class="p-4 text-right">Actions</th>
</tr>
</thead>

<tbody class="divide-y">

@forelse($expenses as $expense)

<tr class="hover:bg-slate-50 transition">
<td class="p-4 font-semibold">{{ $expense->title }}</td>
<td class="p-4 text-center">{{ $expense->category }}</td>
<td class="p-4 text-center text-slate-500">
{{ optional($expense->expense_date)->format('d M Y') }}
</td>
<td class="p-4 text-right font-bold text-rose-600">
-₹{{ number_format($expense->amount,2) }}
</td>
<td class="p-4 text-right space-x-3">
<a href="{{ route('user.expenses.edit',$expense->id) }}"
class="text-blue-600 hover:underline">Edit</a>
<form method="POST"
action="{{ route('user.expenses.destroy',$expense->id) }}"
class="inline"
onsubmit="return confirm('Delete this expense?')">
@csrf
@method('DELETE')
<button class="text-rose-600 hover:underline">Delete</button>
</form>
</td>
</tr>

@empty
<tr>
<td colspan="5" class="p-16 text-center text-slate-400">
<div class="flex flex-col items-center gap-3">
<div class="text-6xl">💸</div>
<p>No expenses yet.</p>
</div>
</td>
</tr>
@endforelse

</tbody>
</table>

<div class="p-6 border-t">
{{ $expenses->appends(request()->query())->links() }}
</div>

</div>

</div>


{{-- STYLES --}}
<style>
.kpi-card{background:white;padding:2rem;border-radius:1rem;box-shadow:0 8px 30px rgba(0,0,0,.05)}
.kpi-value{font-size:2rem;font-weight:700}
.kpi-gradient{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;padding:2rem;border-radius:1rem}
.chart-card{background:white;padding:2rem;border-radius:1rem;box-shadow:0 8px 30px rgba(0,0,0,.05)}
.filter-card{background:white;padding:1.5rem;border-radius:1rem;box-shadow:0 4px 20px rgba(0,0,0,.05)}
.filter-input{padding:.7rem 1rem;border:1px solid #e2e8f0;border-radius:.7rem}
.btn-primary{background:linear-gradient(135deg,#ef4444,#ec4899);color:white;padding:.7rem 1.2rem;border-radius:.8rem}
.btn-dark{background:#0f172a;color:white;padding:.6rem 1rem;border-radius:.8rem}
.btn-rose{background:#ef4444;color:white;padding:.5rem 1rem;border-radius:.7rem}
.btn-light{background:#e2e8f0;padding:.5rem 1rem;border-radius:.7rem}
.table-card{background:white;border-radius:1rem;box-shadow:0 8px 30px rgba(0,0,0,.05);overflow:hidden}
</style>


{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){

/* Animated KPI */
document.querySelectorAll('.kpi-value').forEach(el=>{
let target=parseFloat(el.dataset.target||0);
let current=0;
let step=target/40;
let interval=setInterval(()=>{
current+=step;
if(current>=target){current=target;clearInterval(interval);}
el.innerText='₹'+new Intl.NumberFormat('en-IN').format(current.toFixed(2));
},20);
});

/* Currency formatter */
function currency(v){
return '₹'+new Intl.NumberFormat('en-IN').format(v);
}

const catCanvas=document.getElementById('categoryChart');
if(catCanvas){
new Chart(catCanvas,{
type:'doughnut',
data:{
labels:@json($categoryData->keys()),
datasets:[{data:@json($categoryData->values())}]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{
tooltip:{callbacks:{label:(ctx)=>ctx.label+': '+currency(ctx.raw)}}
}
}
});
}

const monthCanvas=document.getElementById('monthlyChart');
if(monthCanvas){
new Chart(monthCanvas,{
type:'line',
data:{
labels:@json($monthlyData->keys()),
datasets:[{
label:'Expenses',
data:@json($monthlyData->values()),
borderColor:'#ef4444',
backgroundColor:'rgba(239,68,68,0.15)',
fill:true,
tension:.4
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{
tooltip:{callbacks:{label:(ctx)=>currency(ctx.raw)}}
},
scales:{
y:{ticks:{callback:(v)=>currency(v)}}
}
}
});
}

});
</script>

@endsection