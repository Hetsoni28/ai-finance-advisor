@extends('layouts.app')

@section('content')

@php
    $analysis    = $analysis ?? [];
    $activities  = $activities ?? collect();
    $severities  = $severities ?? [];

    $totalLogs     = (int) ($analysis['totalLogs'] ?? 0);
    $deleteCount   = (int) ($analysis['deleteCount'] ?? 0);
    $updateCount   = (int) ($analysis['updateCount'] ?? 0);
    $criticalCount = (int) ($analysis['criticalCount'] ?? 0);

    $threatScore = min(max((int) ($analysis['score'] ?? 0), 0), 100);

    $defaultLevel = [
        'label' => 'SECURE',
        'color' => '#16a34a',
        'text'  => 'text-emerald-500'
    ];

    $levelData = array_merge($defaultLevel, $analysis['level'] ?? []);

    $safeColor = preg_match('/^#[a-f0-9]{6}$/i', $levelData['color'])
        ? $levelData['color']
        : '#16a34a';

    $anomalyRatio = $totalLogs > 0
        ? round(($deleteCount / $totalLogs) * 100)
        : 0;
@endphp


<div class="min-h-screen bg-gradient-to-br 
    from-slate-50 via-white to-indigo-50
    dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition">

<div class="max-w-7xl mx-auto px-6 py-14 space-y-16">

{{-- ================= HEADER ================= --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">

    <div>
        <h1 class="text-4xl font-black text-slate-900 dark:text-white">
            Security Intelligence Console
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">
            Real-time audit monitoring & anomaly detection
        </p>
    </div>

    <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>

        <span class="px-4 py-1.5 rounded-full text-xs font-bold
                     bg-indigo-100 dark:bg-indigo-500/20
                     text-indigo-700 dark:text-indigo-400">
            LIVE MONITORING
        </span>
    </div>

</div>


{{-- ================= ALERT BANNER ================= --}}
@if($criticalCount > 0)
<div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 
            text-rose-600 px-6 py-4 rounded-2xl shadow-lg">
    ⚠ {{ $criticalCount }} critical security alert(s) detected.
</div>
@endif


{{-- ================= METRICS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">

@foreach([
    ['label'=>'Total Logs','value'=>$totalLogs,'color'=>'text-slate-900 dark:text-white'],
    ['label'=>'Delete Events','value'=>$deleteCount,'color'=>'text-rose-500'],
    ['label'=>'Update Events','value'=>$updateCount,'color'=>'text-amber-500'],
    ['label'=>'Critical Alerts','value'=>$criticalCount,'color'=>'text-purple-500'],
] as $metric)

<div class="bg-white dark:bg-slate-900 border dark:border-slate-800 
            rounded-3xl p-6 shadow-lg hover:-translate-y-1 transition">

    <p class="text-xs uppercase text-slate-400 font-bold">
        {{ $metric['label'] }}
    </p>

    <div class="text-4xl font-black mt-3 {{ $metric['color'] }} counter"
         data-target="{{ $metric['value'] }}">
        0
    </div>

</div>

@endforeach


{{-- Threat Gauge --}}
<div class="bg-white dark:bg-slate-900 border dark:border-slate-800
            rounded-3xl p-6 shadow-lg flex flex-col items-center">

    <div class="relative w-44 h-44">

        <svg class="w-full h-full -rotate-90">
            <circle cx="88" cy="88" r="70"
                    stroke="#e5e7eb" stroke-width="14" fill="none" />
            <circle cx="88" cy="88" r="70"
                    stroke="{{ $safeColor }}"
                    stroke-width="14"
                    fill="none"
                    stroke-dasharray="440"
                    stroke-dashoffset="{{ 440 - (440 * $threatScore / 100) }}"
                    stroke-linecap="round" />
        </svg>

        <div class="absolute inset-0 flex items-center justify-center
                    text-3xl font-black">
            {{ $threatScore }}
        </div>

    </div>

    <p class="mt-4 text-sm font-bold {{ $levelData['text'] }}">
        {{ e($levelData['label']) }} LEVEL
    </p>

</div>

</div>


{{-- ================= DELETION RISK BAR ================= --}}
<div class="bg-white dark:bg-slate-900 border dark:border-slate-800
            rounded-3xl p-8 shadow-lg">

<div class="flex justify-between mb-4">
    <span class="font-semibold text-slate-700 dark:text-slate-300">
        Deletion Ratio
    </span>
    <span class="font-bold text-rose-500">
        {{ $anomalyRatio }}%
    </span>
</div>

<div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
    <div class="h-2 bg-rose-500 transition-all duration-700"
         style="width: {{ min(max($anomalyRatio,0),100) }}%">
    </div>
</div>

</div>


{{-- ================= ACTIVITY TABLE ================= --}}
<div class="bg-white dark:bg-slate-900 border dark:border-slate-800
            rounded-3xl shadow-lg overflow-hidden">

<div class="overflow-x-auto">

<table class="w-full text-sm min-w-[900px]">

<thead class="bg-slate-100 dark:bg-slate-800 sticky top-0 z-10
              uppercase text-xs tracking-wide text-slate-500">
<tr>
    <th class="px-6 py-4 text-left">User</th>
    <th class="px-6 py-4 text-left">Action</th>
    <th class="px-6 py-4 text-center">Severity</th>
    <th class="px-6 py-4 text-right">Timestamp</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-200 dark:divide-slate-800">

@forelse($activities as $activity)

@php
    $severity = $severities[$activity->id] ?? [
        'label' => 'Info',
        'class' => 'bg-slate-200 text-slate-700'
    ];
@endphp

<tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition
           {{ strtolower($severity['label']) === 'critical' ? 'bg-rose-50 dark:bg-rose-500/10' : '' }}">

<td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
    {{ e(optional($activity->user)->name ?? 'System') }}
</td>

<td class="px-6 py-4 text-slate-600 dark:text-slate-400">
    {{ e($activity->description ?? 'N/A') }}
</td>

<td class="px-6 py-4 text-center">
    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $severity['class'] }}">
        {{ e($severity['label']) }}
    </span>
</td>

<td class="px-6 py-4 text-right text-slate-600 dark:text-slate-400 whitespace-nowrap">
    {{ optional($activity->created_at)->format('d M Y • h:i A') }}
</td>

</tr>

@empty

<tr>
<td colspan="4" class="px-6 py-20 text-center text-slate-500">
    No security logs recorded.
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>


{{-- PAGINATION --}}
@if($activities instanceof \Illuminate\Contracts\Pagination\Paginator ||
    $activities instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
<div>
    {{ $activities->withQueryString()->links('pagination::tailwind') }}
</div>
@endif


</div>
</div>


{{-- Counter Animation --}}
<script>
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.counter').forEach(c=>{
        const target=parseInt(c.dataset.target||0);
        let count=0;
        const step=Math.max(target/50,1);
        function update(){
            count+=step;
            if(count<target){
                c.innerText=Math.floor(count).toLocaleString();
                requestAnimationFrame(update);
            }else{
                c.innerText=target.toLocaleString();
            }
        }
        update();
    });
});
</script>

@endsection