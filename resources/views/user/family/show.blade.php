@extends('layouts.app')

@section('title', $family->name . ' · Family · FinanceAI')

@section('content')
@php
    $inr = fn ($v) => '₹' . inr_number(abs((float) $v), 0);
    $signed = fn ($v) => ($v < 0 ? '−' : '') . '₹' . inr_number(abs((float) $v), 0);
    $me = auth()->user();
    $canManage = $me->can('update', $family);
    $myRole = strtolower(optional($members->firstWhere('id', $me->id))->pivot->role ?? 'member');
    $roleTone = ['owner' => 'indigo', 'admin' => 'sky', 'member' => 'zinc'];
    $m = $metrics;
    $hasTrend = array_sum((array) $trend['income']) + array_sum((array) $trend['expense']) > 0;
    $catTotal = array_sum((array) $categories);
    $palette = ['#6366f1', '#10b981', '#f59e0b', '#0ea5e9', '#f43f5e', '#a855f7', '#71717a'];
    $pending = $invites->filter(fn ($i) => !$i->accepted_at && (!$i->expires_at || \Carbon\Carbon::parse($i->expires_at)->isFuture()));
    $ledger = collect($recentIncomes)->map(fn ($r) => (object) ['in' => true, 'title' => $r->source, 'cat' => $r->category, 'amount' => $r->amount, 'date' => \Carbon\Carbon::parse($r->income_date), 'by' => $r->user_id])
        ->concat(collect($recentExpenses)->map(fn ($r) => (object) ['in' => false, 'title' => $r->title, 'cat' => $r->category, 'amount' => $r->amount, 'date' => \Carbon\Carbon::parse($r->expense_date), 'by' => $r->user_id]))
        ->sortByDesc(fn ($r) => $r->date->timestamp)->take(8)->values();
    $memberNames = $members->pluck('name', 'id');
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header :title="$family->name" eyebrow="Family" icon="fa-people-roof" :stamp="false"
        :description="$family->description ?: 'Shared income and spending for everyone in this family.'">
        <x-slot name="actions">
            @if($canManage)
                <a href="{{ route('user.families.access', $family) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <i class="fa-solid fa-envelope-open-text text-xs text-zinc-400" aria-hidden="true"></i> Invites @if($pending->count())<span class="rounded-full bg-zinc-900 px-1.5 text-[11px] text-white">{{ $pending->count() }}</span>@endif
                </a>
            @endif
            <a href="{{ route('user.expenses.create') }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> Add shared expense
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    @if(!empty($systemError))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">We couldn’t load this family’s totals just now. Refresh to try again.</div>
    @endif

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat label="Shared income" :value="$inr($m['total_income'])" icon="fa-arrow-trend-up" tone="emerald" :spark="(array) $trend['income']" />
        <x-admin.stat label="Shared expenses" :value="$inr($m['total_expense'])" icon="fa-arrow-trend-down" tone="rose" :spark="(array) $trend['expense']" />
        <x-admin.stat label="Balance" :value="$signed($m['balance'])" icon="fa-scale-balanced" tone="indigo" />
        <x-admin.stat label="Kept this month" :value="$m['saving_rate'] . '%'" icon="fa-piggy-bank" tone="sky" hint="of this month’s shared income" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" title="Last 6 months" description="Shared income and expenses">
            <div class="relative h-60">
                <canvas id="familyChart" role="img" aria-label="Shared income and expenses per month"></canvas>
                @unless($hasTrend)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-column" title="No shared records yet" text="When you add income or an expense, choose “A family” and pick {{ $family->name }}." />
                @endunless
            </div>
        </x-admin.card>
        <x-admin.card title="Shared spending by category">
            @if(empty($categories))
                <p class="text-sm text-zinc-500">No shared expenses yet.</p>
            @else
                <ul class="space-y-3">
                    @foreach($categories as $cat => $val)
                        @php $share = $catTotal > 0 ? round($val / $catTotal * 100) : 0; @endphp
                        <li>
                            <div class="flex justify-between text-sm"><span class="flex items-center gap-2 text-zinc-700"><span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $palette[$loop->index % 7] }}"></span>{{ $cat ?: 'Uncategorized' }}</span><span class="tabular-nums text-zinc-900">{{ $inr($val) }}</span></div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full" style="width: {{ $share }}%; background: {{ $palette[$loop->index % 7] }}"></div></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" title="Recent shared transactions" flush>
            @if($ledger->isEmpty())
                <p class="px-5 py-6 text-sm text-zinc-500">Nothing shared yet.</p>
            @else
                <ul class="divide-y divide-zinc-100">
                    @foreach($ledger as $t)
                        <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $t->in ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}"><i class="fa-solid {{ $t->in ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs" aria-hidden="true"></i></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900">{{ $t->title }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ $t->cat }} · {{ $t->date->format('d M Y') }} · {{ $memberNames[$t->by] ?? 'Former member' }}</p>
                            </div>
                            <span class="text-sm font-semibold tabular-nums {{ $t->in ? 'text-emerald-600' : 'text-zinc-900' }}">{{ $t->in ? '+' : '−' }}{{ $inr($t->amount) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>

        <div class="space-y-6">
            <x-admin.card :title="'Members · ' . $members->count()" flush>
                <ul class="divide-y divide-zinc-100">
                    @foreach($members as $member)
                        @php $r = strtolower($member->pivot->role ?? 'member'); @endphp
                        <li class="flex items-center gap-3 px-4 py-3 sm:px-5">
                            <x-admin.avatar :name="$member->name" size="sm" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900">{{ $member->name }} @if($member->id === $me->id)<span class="text-xs font-normal text-zinc-400">(you)</span>@endif</p>
                                <p class="truncate text-xs text-zinc-500">Joined {{ \Carbon\Carbon::parse($member->pivot->created_at)->format('d M Y') }}</p>
                            </div>
                            <x-admin.badge :tone="$roleTone[$r] ?? 'zinc'">{{ ucfirst($r) }}</x-admin.badge>
                            @if($canManage && $member->id !== $me->id && $r !== 'owner')
                                <form method="POST" action="{{ route('user.families.removeMember', [$family, $member]) }}" data-confirm-title="Remove this member?" data-confirm="{{ $member->name }} will lose access to {{ $family->name }} and its shared budget." data-confirm-ok="Remove member">
                                    @csrf @method('DELETE')
                                    <button class="flex h-7 w-7 items-center justify-center rounded-md text-zinc-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Remove {{ $member->name }}"><i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i></button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>

            @if($canManage)
                <x-admin.card title="Invite someone">
                    <form method="POST" action="{{ route('user.families.invite', $family) }}" class="space-y-3" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        <label for="invite-email" class="sr-only">Email address</label>
                        <input id="invite-email" type="email" name="email" required value="{{ old('email') }}" placeholder="name@example.com"
                               class="h-10 w-full rounded-lg border border-zinc-200 px-3 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        @error('email')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        <button :disabled="submitting" class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-lg bg-zinc-900 text-sm font-medium text-white hover:bg-zinc-800 disabled:opacity-60">
                            <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i> Send invite
                        </button>
                        <p class="text-xs text-zinc-500">They’ll get an email with a one-click link to join.</p>
                    </form>
                </x-admin.card>
            @else
                <p class="rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-500">You’re a {{ $myRole }} here. Ask an owner or admin to invite people.</p>
            @endif

            <x-admin.card title="Activity" flush>
                @if($activities->isEmpty())
                    <p class="px-5 py-5 text-sm text-zinc-500">No activity yet.</p>
                @else
                    <ul class="divide-y divide-zinc-100">
                        @foreach($activities->take(6) as $a)
                            <li class="px-4 py-3 sm:px-5">
                                <p class="text-sm text-zinc-800">{{ $a->description }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500">{{ $a->causer->name ?? 'System' }} · {{ $a->created_at?->diffForHumans() }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        </div>
    </div>
</div>
@endsection

@if($hasTrend)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('familyChart');
    if (!el || typeof Chart === 'undefined') return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: @json(array_values((array) $trend['months'])),
            datasets: [
                { label: 'Income', data: @json(array_values((array) $trend['income'])), backgroundColor: '#10b981', borderRadius: 4, maxBarThickness: 24 },
                { label: 'Expenses', data: @json(array_values((array) $trend['expense'])), backgroundColor: '#f43f5e', borderRadius: 4, maxBarThickness: 24 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.dataset.label + ': ₹' + Number(c.parsed.y).toLocaleString('en-IN') } } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { callback: v => v >= 1000 ? '₹' + (v / 1000) + 'k' : '₹' + v } } },
        },
    });
});
</script>
@endpush
@endif
