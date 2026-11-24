@extends('layouts.app')

@section('title', 'Reports · Admin · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 0);
    $net = $totalIncome - $totalExpenses;
    $rate = $totalIncome > 0 ? round($net / $totalIncome * 100, 1) : null;
    $catTotal = array_sum($categorySeries);
    $hasMonthly = array_sum($monthlyIncome) + array_sum($monthlyExpenses) > 0;
    $months = collect($labels)->map(fn ($l, $i) => ['label' => $l, 'in' => $monthlyIncome[$i] ?? 0, 'out' => $monthlyExpenses[$i] ?? 0]);
    $spenderTotal = (float) $topSpenders->sum('total');
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Reports" description="Platform-wide money movement across all users. Updated every minute.">
        <x-slot name="actions">
            <a href="{{ route('admin.reports.pdf') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800">
                <i class="fa-solid fa-file-pdf text-xs" aria-hidden="true"></i> Download PDF
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Total income" :value="$inr($totalIncome)" icon="fa-arrow-trend-up" tone="emerald" hint="All users, all time" />
        <x-admin.stat label="Total expenses" :value="$inr($totalExpenses)" icon="fa-arrow-trend-down" tone="rose" hint="All users, all time" />
        <x-admin.stat label="Net" :value="($net < 0 ? '−' : '') . $inr(abs($net))" icon="fa-scale-balanced" tone="indigo"
            :hint="$rate !== null ? $rate . '% of income kept' : 'No income recorded yet'" />
        <x-admin.stat label="Users" :value="number_format($totalUsers)" icon="fa-users" tone="sky"
            :hint="number_format($activeUsers) . ' joined in the last 30 days'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" title="Monthly totals" description="Last 6 months, by transaction date">
            <div class="relative h-64">
                <canvas id="monthlyChart" role="img" aria-label="Income and expenses per month for the last 6 months"></canvas>
                @unless($hasMonthly)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-line" title="No transactions in the last 6 months" />
                @endunless
            </div>
        </x-admin.card>

        <x-admin.card title="Expenses by category" description="All users, all time">
            @if(empty($categoryLabels))
                <x-admin.empty icon="fa-chart-pie" title="No categorised expenses yet" />
            @else
                <ul class="space-y-3.5">
                    @foreach($categoryLabels as $i => $cat)
                        @php $val = $categorySeries[$i] ?? 0; $share = $catTotal > 0 ? round($val / $catTotal * 100) : 0; @endphp
                        <li>
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="truncate font-medium text-zinc-800">{{ $cat }}</span>
                                <span class="shrink-0 tabular-nums text-zinc-900">{{ $inr($val) }} <span class="text-xs text-zinc-400">{{ $share }}%</span></span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-indigo-500" style="width: {{ $share }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin.card title="Month by month" flush>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 text-xs text-zinc-500">
                            <th class="px-4 py-2.5 text-left font-medium sm:px-5">Month</th>
                            <th class="px-4 py-2.5 text-right font-medium">Income</th>
                            <th class="px-4 py-2.5 text-right font-medium">Expenses</th>
                            <th class="px-4 py-2.5 text-right font-medium sm:px-5">Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($months->reverse() as $m)
                            @php $mNet = $m['in'] - $m['out']; @endphp
                            <tr class="hover:bg-zinc-50/60">
                                <td class="px-4 py-2.5 font-medium text-zinc-800 sm:px-5">{{ $m['label'] }}</td>
                                <td class="px-4 py-2.5 text-right tabular-nums {{ $m['in'] ? 'text-zinc-800' : 'text-zinc-400' }}">{{ $inr($m['in']) }}</td>
                                <td class="px-4 py-2.5 text-right tabular-nums {{ $m['out'] ? 'text-zinc-800' : 'text-zinc-400' }}">{{ $inr($m['out']) }}</td>
                                <td class="px-4 py-2.5 text-right font-medium tabular-nums sm:px-5 {{ $mNet < 0 ? 'text-rose-600' : ($mNet > 0 ? 'text-emerald-600' : 'text-zinc-400') }}">{{ $mNet < 0 ? '−' : '' }}{{ $inr(abs($mNet)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>

        <x-admin.card title="Highest spenders" description="Users with the most expenses recorded" flush>
            @if($topSpenders->isEmpty())
                <x-admin.empty icon="fa-receipt" title="No expenses recorded yet" />
            @else
                <ol class="divide-y divide-zinc-100">
                    @foreach($topSpenders as $i => $s)
                        @php $share = $spenderTotal > 0 ? round($s->total / $spenderTotal * 100) : 0; @endphp
                        <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                            <span class="w-5 text-xs tabular-nums text-zinc-400">{{ $i + 1 }}</span>
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xs font-semibold text-zinc-600">{{ strtoupper(mb_substr($s->user->name ?? '?', 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900">{{ $s->user->name ?? 'Deleted user' }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $s->user->email ?? '—' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium tabular-nums text-zinc-900">{{ $inr($s->total) }}</p>
                                <p class="text-xs text-zinc-500">{{ $share }}% of top 5</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-admin.card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('monthlyChart');
    if (!el || typeof Chart === 'undefined') return;
    const inr = v => '₹' + Number(v).toLocaleString('en-IN');
    new Chart(el, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                { label: 'Income', data: @json($monthlyIncome), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.08)', fill: true, tension: .3, pointRadius: 3, borderWidth: 2 },
                { label: 'Expenses', data: @json($monthlyExpenses), borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.06)', fill: true, tension: .3, pointRadius: 3, borderWidth: 2 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', align: 'end', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, color: '#71717a', font: { size: 11 } } },
                tooltip: { callbacks: { label: c => c.dataset.label + ': ' + inr(c.parsed.y) } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 11 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { color: '#a1a1aa', font: { size: 11 }, callback: v => v >= 1000 ? '₹' + (v / 1000) + 'k' : '₹' + v } },
            },
        },
    });
});
</script>
@endpush
