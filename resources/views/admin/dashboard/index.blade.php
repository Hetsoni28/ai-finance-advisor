@extends('layouts.app')

@section('title', 'Overview · Admin · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 0);
    $signed = fn ($v) => ($v < 0 ? '−' : '') . '₹' . number_format(abs((float) $v), 0);
    // % change; null when there is no previous value to compare against (avoids a fake "+100%")
    $pct = fn ($now, $prev) => $prev > 0 ? round(($now - $prev) / $prev * 100) : null;

    $net = $totalIncome - $totalExpenses;
    $rate = $totalIncome > 0 ? round($net / $totalIncome * 100, 1) : null;
    $monthKept = $thisMonthIncome - $thisMonthExpenses;
    $monthlyNet = array_map(fn ($i, $e) => $i - $e, $monthlyIncome, $monthlyExpenses);
    $netMax = max(1, max(array_map('abs', $monthlyNet)));
    $activeUsers = $totalUsers - $blockedUsers;
    $hasTrend = array_sum($monthlyIncome) + array_sum($monthlyExpenses) > 0;
    $catTotal = (float) $topCategories->sum('total');
    $planColors = ['#a1a1aa', '#6366f1', '#f59e0b', '#10b981', '#0ea5e9'];

    // Group recent activity by day
    $activityByDay = $activities->groupBy(fn ($a) => $a->created_at?->isToday() ? 'Today' : ($a->created_at?->isYesterday() ? 'Yesterday' : $a->created_at?->format('d M Y')));
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Overview" description="How FinanceAI is being used right now, from live database records.">
        <x-slot name="actions">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-chart-column text-xs text-zinc-400" aria-hidden="true"></i> Reports
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-users text-xs" aria-hidden="true"></i> Manage users
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    {{-- Summary band --}}
    <section class="relative overflow-hidden rounded-2xl bg-zinc-950 text-white ring-1 ring-zinc-900" aria-label="Platform summary">
        <div class="relative grid gap-8 p-6 sm:p-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-4">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">Net position · all users</p>
                <p class="mt-2 text-4xl font-semibold tracking-tight tabular-nums sm:text-5xl {{ $net < 0 ? 'text-rose-300' : '' }}">{{ $signed($net) }}</p>
                <p class="mt-2 text-sm text-zinc-400">
                    @if($rate !== null)
                        <span class="font-medium {{ $rate >= 20 ? 'text-emerald-300' : ($rate >= 0 ? 'text-amber-300' : 'text-rose-300') }}">{{ $rate }}%</span> of all recorded income kept
                    @else
                        No income recorded yet
                    @endif
                </p>
            </div>

            <dl class="grid grid-cols-3 gap-4 border-t border-white/10 pt-6 lg:col-span-4 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0">
                <div>
                    <dt class="text-xs text-zinc-400">In · {{ now()->format('M') }}</dt>
                    <dd class="mt-1 text-lg font-semibold tabular-nums text-emerald-300">{{ $inr($thisMonthIncome) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-400">Out · {{ now()->format('M') }}</dt>
                    <dd class="mt-1 text-lg font-semibold tabular-nums text-rose-300">{{ $inr($thisMonthExpenses) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-zinc-400">Kept</dt>
                    <dd class="mt-1 text-lg font-semibold tabular-nums">{{ $signed($monthKept) }}</dd>
                </div>
            </dl>

            <div class="lg:col-span-4 lg:pl-4">
                <p class="text-xs text-zinc-400">Net per month</p>
                <div class="mt-3 flex h-20 items-center gap-2" role="img" aria-label="Net income minus expenses for each of the last 6 months">
                    @foreach($monthlyNet as $i => $v)
                        @php $h = max(2, round(abs($v) / $netMax * 36)); @endphp
                        <div class="flex h-full flex-1 flex-col items-center justify-center" title="{{ $months[$i] }}: {{ $signed($v) }}">
                            <div class="flex h-9 w-full items-end justify-center">@if($v > 0)<span class="w-full max-w-[22px] rounded-t-sm bg-emerald-400/90" style="height: {{ $h }}px"></span>@endif</div>
                            <div class="h-px w-full bg-white/15"></div>
                            <div class="flex h-9 w-full items-start justify-center">@if($v < 0)<span class="w-full max-w-[22px] rounded-b-sm bg-rose-400/90" style="height: {{ $h }}px"></span>@endif</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-1 flex gap-2 text-[10px] text-zinc-500">
                    @foreach($months as $m)<span class="flex-1 text-center">{{ \Illuminate\Support\Str::of($m)->before(' ') }}</span>@endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Users" :value="number_format($totalUsers)" icon="fa-users" tone="indigo" :href="route('admin.users.index')"
            :spark="$monthlySignups" :delta="$pct($newUsersThisMonth, $usersPrev30)" deltaLabel="sign-ups vs prior 30 days"
            :hint="$pct($newUsersThisMonth, $usersPrev30) === null ? number_format($newUsersThisMonth) . ' new in 30 days' : null" />
        <x-admin.stat label="Paid subscribers" :value="number_format($paidSubscribers)" icon="fa-credit-card" tone="emerald"
            :hint="$totalUsers > 0 ? round($paidSubscribers / $totalUsers * 100) . '% of all users' : 'No users yet'" />
        <x-admin.stat label="Income this month" :value="$inr($thisMonthIncome)" icon="fa-arrow-trend-up" tone="emerald"
            :spark="$monthlyIncome" :delta="$pct($thisMonthIncome, $lastMonthIncome)"
            :hint="$pct($thisMonthIncome, $lastMonthIncome) === null ? $inr($totalIncome) . ' all time' : null" />
        <x-admin.stat label="Expenses this month" :value="$inr($thisMonthExpenses)" icon="fa-arrow-trend-down" tone="rose"
            :spark="$monthlyExpenses" :delta="$pct($thisMonthExpenses, $lastMonthExpenses)" goodWhen="down"
            :hint="$pct($thisMonthExpenses, $lastMonthExpenses) === null ? $inr($totalExpenses) . ' all time' : null" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Trend --}}
        <x-admin.card class="lg:col-span-2" title="Income vs expenses" description="All users · last 6 months, by transaction date">
            <x-slot name="action">
                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Income</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Expenses</span>
                    <span class="flex items-center gap-1.5"><span class="h-0.5 w-3 rounded bg-zinc-900"></span>Net</span>
                </div>
            </x-slot>
            <div class="relative h-72">
                <canvas id="trendChart" aria-label="Monthly income, expenses and net for the last 6 months" role="img"></canvas>
                @unless($hasTrend)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-line" title="No transactions in the last 6 months" text="The chart fills in as users record income and expenses." />
                @endunless
            </div>
        </x-admin.card>

        {{-- Plans --}}
        <x-admin.card title="Plans" description="Users on each plan right now">
            @if($planBreakdown->isEmpty())
                <x-admin.empty icon="fa-tags" title="No plans configured" />
            @else
                <div class="relative mx-auto h-40 w-40">
                    <canvas id="planChart" role="img" aria-label="Share of users on each plan"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-semibold tabular-nums text-zinc-900">{{ number_format($totalUsers) }}</span>
                        <span class="text-[11px] text-zinc-500">users</span>
                    </div>
                </div>
                <ul class="mt-5 space-y-2.5">
                    @foreach($planBreakdown as $i => $plan)
                        <li class="flex items-center gap-2.5 text-sm">
                            <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $planColors[$i % count($planColors)] }}"></span>
                            <span class="flex-1 text-zinc-700">{{ $plan['name'] }} <span class="text-xs text-zinc-400">{{ $plan['price'] > 0 ? $inr($plan['price']) . '/mo' : 'Free' }}</span></span>
                            <span class="font-medium tabular-nums text-zinc-900">{{ number_format($plan['count']) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-zinc-100 pt-4 text-sm">
                    <div><p class="text-xs text-zinc-500">Active</p><p class="font-semibold tabular-nums text-zinc-900">{{ number_format($activeUsers) }}</p></div>
                    <div><p class="text-xs text-zinc-500">Suspended</p><p class="font-semibold tabular-nums {{ $blockedUsers ? 'text-rose-600' : 'text-zinc-900' }}">{{ number_format($blockedUsers) }}</p></div>
                </div>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Newest users --}}
        <x-admin.card class="lg:col-span-2" title="Newest users" flush>
            <x-slot name="action"><a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">View all →</a></x-slot>
            @if($recentUsers->isEmpty())
                <x-admin.empty icon="fa-user-plus" title="No users yet" />
            @else
                <ul class="divide-y divide-zinc-100">
                    @foreach($recentUsers as $u)
                        <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                            <x-admin.avatar :name="$u->name" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900">{{ $u->name }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $u->email }}</p>
                            </div>
                            <div class="hidden items-center gap-2 sm:flex">
                                @if($u->isAdmin())<x-admin.badge tone="indigo">Admin</x-admin.badge>@endif
                                @if($u->is_blocked)<x-admin.badge tone="rose" dot>Suspended</x-admin.badge>@else<x-admin.badge tone="emerald" dot>Active</x-admin.badge>@endif
                            </div>
                            <time class="w-24 shrink-0 text-right text-xs text-zinc-500" title="{{ $u->created_at }}">{{ $u->created_at?->diffForHumans(null, true) }} ago</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>

        {{-- Top categories --}}
        <x-admin.card title="Where money goes" description="Top expense categories, all users">
            @if($topCategories->isEmpty())
                <x-admin.empty icon="fa-chart-pie" title="No expenses yet" />
            @else
                <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-zinc-100">
                    @foreach($topCategories as $i => $cat)
                        <span style="width: {{ $catTotal > 0 ? $cat->total / $catTotal * 100 : 0 }}%; background: {{ ['#f43f5e', '#f59e0b', '#6366f1', '#0ea5e9', '#10b981'][$i % 5] }}"></span>
                    @endforeach
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach($topCategories as $i => $cat)
                        <li class="flex items-center gap-2.5 text-sm">
                            <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ ['#f43f5e', '#f59e0b', '#6366f1', '#0ea5e9', '#10b981'][$i % 5] }}"></span>
                            <span class="flex-1 truncate text-zinc-700">{{ $cat->category }}</span>
                            <span class="tabular-nums text-zinc-900">{{ $inr($cat->total) }}</span>
                            <span class="w-10 text-right text-xs tabular-nums text-zinc-400">{{ $catTotal > 0 ? round($cat->total / $catTotal * 100) : 0 }}%</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Activity timeline --}}
        <x-admin.card class="lg:col-span-2" title="Recent activity" flush>
            <x-slot name="action"><a href="{{ route('admin.activities.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Full log →</a></x-slot>
            @if($activities->isEmpty())
                <x-admin.empty icon="fa-list-check" title="No activity recorded yet" />
            @else
                <div class="px-4 py-2 sm:px-5">
                    @foreach($activityByDay as $day => $items)
                        <p class="pb-1 pt-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-400">{{ $day }}</p>
                        <ol class="relative ml-1.5 border-l border-zinc-200">
                            @foreach($items as $a)
                                @php
                                    $d = strtolower($a->description);
                                    $dot = \Illuminate\Support\Str::contains($d, ['delete', 'suspend', 'block', 'fail']) ? 'bg-rose-500' : (\Illuminate\Support\Str::contains($d, ['modif', 'update', 'edit', 'change']) ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <li class="relative py-2.5 pl-5">
                                    <span class="absolute -left-[5px] top-[15px] h-2.5 w-2.5 rounded-full ring-4 ring-white {{ $dot }}" aria-hidden="true"></span>
                                    <p class="text-sm text-zinc-800">{{ $a->description }}</p>
                                    <p class="mt-0.5 text-xs text-zinc-500">{{ $a->user->name ?? 'System' }} · {{ $a->created_at?->format('h:i A') }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endforeach
                </div>
            @endif
        </x-admin.card>

        {{-- Inbox --}}
        <x-admin.card title="Messages" description="From the website contact form">
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-semibold tabular-nums text-zinc-900">{{ number_format($unreadMessages) }}</p>
                <p class="text-sm text-zinc-500">unread</p>
            </div>
            <p class="mt-2 text-sm text-zinc-500">
                {{ $unreadMessages ? 'People are waiting for a reply.' : 'You’re all caught up.' }}
            </p>
            <a href="{{ route('admin.contacts.index', $unreadMessages ? ['filter' => 'unread'] : []) }}" class="mt-5 inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                Open inbox <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
            <dl class="mt-5 grid grid-cols-2 gap-3 border-t border-zinc-100 pt-4 text-sm">
                <div><dt class="text-xs text-zinc-500">Family hubs</dt><dd class="font-semibold tabular-nums text-zinc-900">{{ number_format($familyCount) }}</dd></div>
                <div><dt class="text-xs text-zinc-500" title="Income plus expenses recorded by all users">Money tracked</dt><dd class="font-semibold tabular-nums text-zinc-900">{{ $inr($totalIncome + $totalExpenses) }}</dd></div>
            </dl>
        </x-admin.card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') return;
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    const inr = v => (v < 0 ? '−' : '') + '₹' + Math.abs(Number(v)).toLocaleString('en-IN');

    const trend = document.getElementById('trendChart');
    if (trend) {
        const ctx = trend.getContext('2d');
        const grad = (c) => { const g = ctx.createLinearGradient(0, 0, 0, 280); g.addColorStop(0, c + '33'); g.addColorStop(1, c + '00'); return g; };
        new Chart(trend, {
            data: {
                labels: @json($months),
                datasets: [
                    { type: 'line', label: 'Income', data: @json($monthlyIncome), borderColor: '#10b981', backgroundColor: grad('#10b981'), fill: true, tension: .35, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#fff' },
                    { type: 'line', label: 'Expenses', data: @json($monthlyExpenses), borderColor: '#f43f5e', backgroundColor: grad('#f43f5e'), fill: true, tension: .35, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#fff' },
                    { type: 'line', label: 'Net', data: @json($monthlyNet), borderColor: '#18181b', borderDash: [4, 4], borderWidth: 1.5, pointRadius: 0, fill: false, tension: .35 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false }, tooltip: { padding: 10, callbacks: { label: c => ' ' + c.dataset.label + ': ' + inr(c.parsed.y) } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 11 } } },
                    y: { border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { color: '#a1a1aa', font: { size: 11 }, callback: v => Math.abs(v) >= 1000 ? (v < 0 ? '−' : '') + '₹' + Math.abs(v / 1000) + 'k' : inr(v) } },
                },
            },
        });
    }

    const plan = document.getElementById('planChart');
    if (plan) {
        new Chart(plan, {
            type: 'doughnut',
            data: {
                labels: @json($planBreakdown->pluck('name')),
                datasets: [{ data: @json($planBreakdown->pluck('count')), backgroundColor: @json(array_slice($planColors, 0, $planBreakdown->count())), borderWidth: 2, borderColor: '#fff', hoverOffset: 4 }],
            },
            options: { cutout: '74%', maintainAspectRatio: false, plugins: { legend: { display: false } } },
        });
    }
});
</script>
@endpush
