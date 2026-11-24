@extends('layouts.app')

@section('title', 'Overview · Admin · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . number_format((float) $v, 0);
    $net = $totalIncome - $totalExpenses;
    $activeUsers = $totalUsers - $blockedUsers;
    $hasTrend = array_sum($monthlyIncome) + array_sum($monthlyExpenses) > 0;
    $catTotal = (float) $topCategories->sum('total');
    $planTotal = max(1, $planBreakdown->sum('count'));
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Overview" description="Everything happening across FinanceAI, from live database records.">
        <x-slot name="actions">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                <i class="fa-solid fa-chart-column text-xs text-zinc-400" aria-hidden="true"></i> Reports
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800">
                <i class="fa-solid fa-users text-xs" aria-hidden="true"></i> Manage users
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    {{-- KPIs --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Users" :value="number_format($totalUsers)" icon="fa-users" tone="indigo"
            :hint="number_format($newUsersThisMonth) . ' joined in the last 30 days'"
            :href="route('admin.users.index')" />
        <x-admin.stat label="Paid subscribers" :value="number_format($paidSubscribers)" icon="fa-credit-card" tone="emerald"
            :hint="$totalUsers > 0 ? round($paidSubscribers / $totalUsers * 100) . '% of all users' : 'No users yet'" />
        <x-admin.stat label="Income recorded" :value="$inr($totalIncome)" icon="fa-arrow-trend-up" tone="emerald"
            :hint="$inr($thisMonthIncome) . ' this month'" />
        <x-admin.stat label="Expenses recorded" :value="$inr($totalExpenses)" icon="fa-arrow-trend-down" tone="rose"
            :hint="$inr($thisMonthExpenses) . ' this month'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Trend --}}
        <x-admin.card class="lg:col-span-2" title="Income vs expenses" description="All users · last 6 months, by transaction date">
            <x-slot name="action">
                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Income</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Expenses</span>
                </div>
            </x-slot>
            <div class="relative h-72">
                <canvas id="trendChart" aria-label="Monthly income and expenses for the last 6 months" role="img"></canvas>
                @unless($hasTrend)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-line" title="No transactions in the last 6 months" text="The chart fills in as users record income and expenses." />
                @endunless
            </div>
            <dl class="mt-4 grid grid-cols-3 divide-x divide-zinc-100 border-t border-zinc-100 pt-4 text-center">
                <div><dt class="text-xs text-zinc-500">Net (all time)</dt><dd class="mt-0.5 text-sm font-semibold tabular-nums {{ $net >= 0 ? 'text-zinc-900' : 'text-rose-600' }}">{{ $net < 0 ? '−' : '' }}{{ $inr(abs($net)) }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Savings rate</dt><dd class="mt-0.5 text-sm font-semibold tabular-nums text-zinc-900">{{ $totalIncome > 0 ? round($net / $totalIncome * 100, 1) . '%' : '—' }}</dd></div>
                <div><dt class="text-xs text-zinc-500">Family hubs</dt><dd class="mt-0.5 text-sm font-semibold tabular-nums text-zinc-900">{{ number_format($familyCount) }}</dd></div>
            </dl>
        </x-admin.card>

        {{-- Plans --}}
        <x-admin.card title="Plans" description="Users on each plan right now">
            @if($planBreakdown->isEmpty())
                <x-admin.empty icon="fa-tags" title="No plans configured" />
            @else
                <ul class="space-y-4">
                    @foreach($planBreakdown as $plan)
                        @php $share = round($plan['count'] / $planTotal * 100); @endphp
                        <li>
                            <div class="flex items-baseline justify-between text-sm">
                                <span class="font-medium text-zinc-800">{{ $plan['name'] }}
                                    <span class="ml-1 text-xs font-normal text-zinc-400">{{ $plan['price'] > 0 ? $inr($plan['price']) . '/mo' : 'Free' }}</span>
                                </span>
                                <span class="tabular-nums text-zinc-900">{{ number_format($plan['count']) }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full {{ $plan['price'] > 0 ? 'bg-indigo-500' : 'bg-zinc-400' }}" style="width: {{ $share }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-zinc-100 pt-4">
                    <div><p class="text-xs text-zinc-500">Active accounts</p><p class="text-sm font-semibold tabular-nums text-zinc-900">{{ number_format($activeUsers) }}</p></div>
                    <div><p class="text-xs text-zinc-500">Suspended</p><p class="text-sm font-semibold tabular-nums {{ $blockedUsers ? 'text-rose-600' : 'text-zinc-900' }}">{{ number_format($blockedUsers) }}</p></div>
                </div>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent sign-ups --}}
        <x-admin.card class="lg:col-span-2" title="Newest users" flush>
            <x-slot name="action"><a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">View all</a></x-slot>
            @if($recentUsers->isEmpty())
                <x-admin.empty icon="fa-user-plus" title="No users yet" />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 text-left text-xs text-zinc-500">
                                <th class="px-4 py-2.5 font-medium sm:px-5">User</th>
                                <th class="px-4 py-2.5 font-medium">Role</th>
                                <th class="px-4 py-2.5 font-medium">Status</th>
                                <th class="px-4 py-2.5 text-right font-medium sm:px-5">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach($recentUsers as $u)
                                <tr class="hover:bg-zinc-50/60">
                                    <td class="px-4 py-3 sm:px-5">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xs font-semibold text-zinc-600">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="truncate font-medium text-zinc-900">{{ $u->name }}</p>
                                                <p class="truncate text-xs text-zinc-500">{{ $u->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">@if($u->isAdmin())<x-admin.badge tone="indigo">Admin</x-admin.badge>@else<x-admin.badge>User</x-admin.badge>@endif</td>
                                    <td class="px-4 py-3">@if($u->is_blocked)<x-admin.badge tone="rose" dot>Suspended</x-admin.badge>@else<x-admin.badge tone="emerald" dot>Active</x-admin.badge>@endif</td>
                                    <td class="px-4 py-3 text-right text-zinc-500 sm:px-5" title="{{ $u->created_at }}">{{ $u->created_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-admin.card>

        {{-- Top categories --}}
        <x-admin.card title="Where money goes" description="Top expense categories, all users">
            @if($topCategories->isEmpty())
                <x-admin.empty icon="fa-chart-pie" title="No expenses yet" />
            @else
                <ul class="space-y-3.5">
                    @foreach($topCategories as $cat)
                        @php $share = $catTotal > 0 ? round($cat->total / $catTotal * 100) : 0; @endphp
                        <li>
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="truncate font-medium text-zinc-800">{{ $cat->category }}</span>
                                <span class="shrink-0 tabular-nums text-zinc-900">{{ $inr($cat->total) }} <span class="text-xs text-zinc-400">{{ $share }}%</span></span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-rose-400" style="width: {{ $share }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Activity --}}
        <x-admin.card class="lg:col-span-2" title="Recent activity" flush>
            <x-slot name="action"><a href="{{ route('admin.activities.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Activity log</a></x-slot>
            @if($activities->isEmpty())
                <x-admin.empty icon="fa-list-check" title="No activity recorded yet" />
            @else
                <ul class="divide-y divide-zinc-100">
                    @foreach($activities as $a)
                        <li class="flex items-start gap-3 px-4 py-3 sm:px-5">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ \Illuminate\Support\Str::contains(strtolower($a->description), ['delete', 'suspend', 'block', 'fail']) ? 'bg-rose-500' : 'bg-zinc-300' }}" aria-hidden="true"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-zinc-800">{{ $a->description }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500">{{ $a->user->name ?? 'System' }} · {{ $a->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>

        {{-- Inbox --}}
        <x-admin.card title="Messages" description="From the website contact form">
            <p class="text-3xl font-semibold tabular-nums text-zinc-900">{{ number_format($unreadMessages) }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ $unreadMessages === 1 ? 'unread message' : 'unread messages' }}</p>
            <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}" class="mt-5 inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                Open inbox <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </x-admin.card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('trendChart');
    if (!el || typeof Chart === 'undefined') return;
    const inr = v => '₹' + Number(v).toLocaleString('en-IN');
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [
                { label: 'Income', data: @json($monthlyIncome), backgroundColor: '#10b981', borderRadius: 4, maxBarThickness: 28 },
                { label: 'Expenses', data: @json($monthlyExpenses), backgroundColor: '#f43f5e', borderRadius: 4, maxBarThickness: 28 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.dataset.label + ': ' + inr(c.parsed.y) } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 11 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { color: '#a1a1aa', font: { size: 11 }, callback: v => v >= 1000 ? '₹' + (v / 1000) + 'k' : '₹' + v } },
            },
        },
    });
});
</script>
@endpush
