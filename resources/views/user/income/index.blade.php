@extends('layouts.app')

@section('content')

@php
$total = (float)($stats['total'] ?? 0);
$currentMonth = (float)($stats['currentMonth'] ?? 0);
$average = (float)($stats['average'] ?? 0);

/* Use income_date if exists */
$monthly = $incomes
    ->groupBy(fn($i) => $i->income_date?->format('M Y') ?? $i->created_at?->format('M Y'))
    ->map(fn($group) => $group->sum('amount'))
    ->take(6);

$chartLabels = $monthly->keys()->values();
$chartValues = $monthly->values();
@endphp

<div class="max-w-[1500px] mx-auto px-6 py-12 space-y-12">

{{-- HEADER --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
    <div>
        <h1 class="text-4xl font-extrabold tracking-tight">
            Income Intelligence
        </h1>
        <p class="text-slate-500 mt-2">
            Monitor, optimize and track your personal income performance.
        </p>
    </div>

    <a href="{{ route('user.incomes.create') }}"
       class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg transition hover:scale-105">
        + Add Income
    </a>
</div>


{{-- KPI CARDS --}}
<div class="grid md:grid-cols-3 gap-6">

    <div class="kpi-card">
        <p>Total Income</p>
        <h2 class="kpi-value text-emerald-600" data-target="{{ $total }}">
            ₹0
        </h2>
    </div>

    <div class="kpi-card">
        <p>This Month</p>
        <h2 class="kpi-value" data-target="{{ $currentMonth }}">
            ₹0
        </h2>
    </div>

    <div class="kpi-card gradient-card">
        <p>Average Entry</p>
        <h2 class="kpi-value text-white" data-target="{{ $average }}">
            ₹0
        </h2>
    </div>

</div>


{{-- TREND CHART --}}
<div class="chart-card">
    <h3>Income Trend (Last Months)</h3>
    <div class="h-[300px]">
        <canvas id="incomeChart"></canvas>
    </div>
</div>


{{-- SEARCH --}}
<div class="flex justify-between items-center">
    <input id="searchInput"
           type="text"
           placeholder="Search income source..."
           class="search-input">
</div>


{{-- TABLE --}}
<div class="table-card">

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-slate-50 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left cursor-pointer" onclick="sortTable(0)">
                        Source ↑↓
                    </th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-right cursor-pointer" onclick="sortTable(2)">
                        Amount ↑↓
                    </th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>

            <tbody id="incomeTable">

            @forelse($incomes as $income)

                <tr class="table-row">
                    <td class="px-6 py-4 income-source">
                        {{ $income->source }}
                    </td>

                    <td class="px-6 py-4 text-slate-500">
                        {{ $income->income_date?->format('d M Y') ?? $income->created_at?->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4 text-right font-semibold text-emerald-600 income-amount">
                        {{ number_format((float)$income->amount, 2) }}
                    </td>

                    <td class="px-6 py-4 text-right space-x-3">
                        <a href="{{ route('user.incomes.edit', $income->id) }}"
                           class="text-indigo-600 hover:underline">Edit</a>

                        <form action="{{ route('user.incomes.destroy', $income->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Delete this income?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>

            @empty

                <tr id="emptyRow">
                    <td colspan="4" class="px-6 py-20 text-center text-slate-400">
                        No income records found.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>
    </div>
</div>

<div>
    {{ $incomes->links() }}
</div>

</div>



{{-- STYLES --}}
<style>
.kpi-card{
    background:white;
    padding:1.5rem;
    border-radius:1rem;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
    transition:.3s;
}
.kpi-card:hover{
    transform:translateY(-4px);
}
.kpi-value{
    font-size:2rem;
    font-weight:700;
}
.gradient-card{
    background:linear-gradient(135deg,#6366f1,#8b5cf6);
    color:white;
}
.chart-card,.table-card{
    background:white;
    padding:2rem;
    border-radius:1rem;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
}
.table-row:hover{
    background:#f8fafc;
}
.search-input{
    width:300px;
    padding:.75rem 1rem;
    border-radius:.75rem;
    border:1px solid #e2e8f0;
}
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
        if(current>=target){
            current=target;
            clearInterval(interval);
        }
        el.innerText='₹'+new Intl.NumberFormat('en-IN').format(current.toFixed(2));
    },25);
});

/* Chart Safety */
const canvas=document.getElementById('incomeChart');
if(canvas){
    new Chart(canvas,{
        type:'line',
        data:{
            labels:@json($chartLabels),
            datasets:[{
                data:@json($chartValues),
                borderColor:'#10b981',
                backgroundColor:'rgba(16,185,129,0.15)',
                fill:true,
                tension:.4
            }]
        },
        options:{responsive:true,maintainAspectRatio:false}
    });
}

});


/* Search */
document.getElementById('searchInput')?.addEventListener('input',function(){
    const val=this.value.toLowerCase();
    let visible=false;
    document.querySelectorAll('#incomeTable tr').forEach(row=>{
        const source=row.querySelector('.income-source');
        if(!source)return;
        const match=source.innerText.toLowerCase().includes(val);
        row.style.display=match?'':'none';
        if(match) visible=true;
    });

    if(!visible){
        if(!document.getElementById('searchEmpty')){
            const tr=document.createElement('tr');
            tr.id='searchEmpty';
            tr.innerHTML='<td colspan="4" class="px-6 py-10 text-center text-slate-400">No matching income found</td>';
            document.getElementById('incomeTable').appendChild(tr);
        }
    }else{
        document.getElementById('searchEmpty')?.remove();
    }
});


/* Sorting */
function sortTable(column){
    const table=document.getElementById('incomeTable');
    const rows=[...table.querySelectorAll('tr')].filter(r=>r.querySelector('td'));
    rows.sort((a,b)=>{
        let A=a.children[column].innerText;
        let B=b.children[column].innerText;
        if(column===2){
            A=parseFloat(A.replace(/,/g,''))||0;
            B=parseFloat(B.replace(/,/g,''))||0;
        }
        return A>B?1:-1;
    });
    rows.forEach(r=>table.appendChild(r));
}
</script>

@endsection