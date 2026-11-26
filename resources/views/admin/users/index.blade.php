@extends('layouts.app')

@section('title', 'Users · Admin · FinanceAI')

@section('content')
@php
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'blocked' => 0, 'admins' => 0];
    $me = auth()->id();
    $filtered = request()->filled('search') || request()->filled('role') || request()->filled('status');

    // Real sign-ups per week for the last 8 weeks (oldest first)
    // One query for all eight weeks, bucketed in PHP.
    $growthLabels = []; $growthData = [];
    $weeks = collect(range(7, 0))->map(fn ($w) => now()->subWeeks($w)->startOfWeek());
    $signups = \App\Models\User::where('created_at', '>=', $weeks->first())->pluck('created_at');
    foreach ($weeks as $from) {
        $to = $from->copy()->endOfWeek();
        $growthLabels[] = $from->format('d M');
        $growthData[] = $signups->filter(fn ($d) => $d && $d->between($from, $to))->count();
    }
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header title="Users" description="Everyone with a FinanceAI account. Suspend or remove accounts here.">
        <x-slot name="actions">
            <button type="button" onclick="exportUsersCsv()" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                <i class="fa-solid fa-download text-xs text-zinc-400" aria-hidden="true"></i> Export this page
            </button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <div class="grid gap-3 lg:grid-cols-5">
        <div class="grid grid-cols-2 gap-3 lg:col-span-3">
            <x-admin.stat label="Total users" :value="inr_number($stats['total'])" icon="fa-users" tone="indigo" />
            <x-admin.stat label="Active" :value="inr_number($stats['active'])" icon="fa-user-check" tone="emerald"
                :hint="$stats['total'] ? round($stats['active'] / $stats['total'] * 100) . '% of users' : null" />
            <x-admin.stat label="Suspended" :value="inr_number($stats['blocked'])" icon="fa-user-lock" tone="rose" />
            <x-admin.stat label="Admins" :value="inr_number($stats['admins'])" icon="fa-user-shield" tone="amber" />
        </div>
        <x-admin.card class="lg:col-span-2" title="Sign-ups" description="New accounts per week · last 8 weeks">
            <div class="relative h-[150px]">
                <canvas id="growthChart" role="img" aria-label="New users per week for the last 8 weeks"></canvas>
                @if(array_sum($growthData) === 0)
                    <x-admin.empty class="absolute inset-0 bg-white !py-6" icon="fa-user-plus" title="No sign-ups in the last 8 weeks" />
                @endif
            </div>
        </x-admin.card>
    </div>

    <x-admin.card flush>
        {{-- Status tabs (real counts) --}}
        @php
            $tabDefs = ['' => ['All', $stats['total']], 'active' => ['Active', $stats['active']], 'blocked' => ['Suspended', $stats['blocked']]];
            $curStatus = request('status', '');
        @endphp
        <nav class="flex gap-1 overflow-x-auto border-b border-zinc-100 px-3 pt-2" aria-label="Filter by status">
            @foreach($tabDefs as $key => [$label, $count])
                <a href="{{ route('admin.users.index', array_filter(['status' => $key ?: null, 'role' => request('role'), 'search' => request('search')])) }}"
                   @if($curStatus === $key) aria-current="page" @endif
                   class="-mb-px inline-flex items-center gap-2 border-b-2 px-3 pb-2.5 pt-1.5 text-sm font-medium {{ $curStatus === $key ? 'border-zinc-900 text-zinc-900' : 'border-transparent text-zinc-500 hover:text-zinc-800' }}">
                    {{ $label }}
                    <span class="rounded-full px-1.5 text-[11px] tabular-nums {{ $curStatus === $key ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600' }}">{{ inr_number($count) }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Filters --}}        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-2 border-b border-zinc-100 p-3 sm:flex-row sm:items-center">
            <label class="relative flex-1">
                <span class="sr-only">Search users</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, email or ID"
                       class="h-9 w-full rounded-lg border border-zinc-200 bg-white pl-8 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>
            <select name="role" aria-label="Role" class="h-9 rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
                <option value="">All roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Users</option>
            </select>
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <button class="h-9 rounded-lg bg-zinc-900 px-4 text-sm font-medium text-white hover:bg-zinc-800">Apply</button>
            @if($filtered)
                <a href="{{ route('admin.users.index') }}" class="h-9 rounded-lg px-3 text-sm font-medium leading-9 text-zinc-500 hover:text-zinc-900">Clear</a>
            @endif
        </form>

        @if($users->isEmpty())
            <x-admin.empty icon="fa-user-slash" :title="$filtered ? 'No users match these filters' : 'No users yet'"
                :text="$filtered ? 'Try a different search or clear the filters.' : null" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="usersTable">
                    <thead>
                        <tr class="border-b border-zinc-100 text-left text-xs text-zinc-500">
                            <th class="px-4 py-2.5 font-medium sm:px-5">User</th>
                            <th class="px-4 py-2.5 font-medium">Role</th>
                            <th class="px-4 py-2.5 font-medium">Status</th>
                            <th class="px-4 py-2.5 font-medium">Plan</th>
                            <th class="px-4 py-2.5 text-right font-medium">Records</th>
                            <th class="px-4 py-2.5 font-medium">Joined</th>
                            <th class="px-4 py-2.5 text-right font-medium sm:px-5"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($users as $u)
                            @php $isMe = $u->id === $me; @endphp
                            <tr class="hover:bg-zinc-50/60"
                                data-csv="{{ json_encode([$u->id, $u->name, $u->email, $u->isAdmin() ? "Admin" : "User", $u->is_blocked ? "Suspended" : "Active", $u->current_plan_name, $u->incomes_count + $u->expenses_count, optional($u->created_at)->format("Y-m-d")]) }}">
                                <td class="px-4 py-3 sm:px-5">
                                    <div class="flex items-center gap-3">
                                        <x-admin.avatar :name="$u->name" :muted="(bool) $u->is_blocked" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-zinc-900">{{ $u->name }} @if($isMe)<span class="ml-1 text-xs font-normal text-zinc-400">(you)</span>@endif</p>
                                            <p class="truncate text-xs text-zinc-500">{{ $u->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">@if($u->isAdmin())<x-admin.badge tone="indigo">Admin</x-admin.badge>@else<x-admin.badge>User</x-admin.badge>@endif</td>
                                <td class="px-4 py-3">@if($u->is_blocked)<x-admin.badge tone="rose" dot>Suspended</x-admin.badge>@else<x-admin.badge tone="emerald" dot>Active</x-admin.badge>@endif</td>
                                <td class="px-4 py-3 text-zinc-700">{{ $u->current_plan_name }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-zinc-700" title="{{ $u->incomes_count }} income · {{ $u->expenses_count }} expense">{{ inr_number($u->incomes_count + $u->expenses_count) }}</td>
                                <td class="px-4 py-3 text-zinc-500" title="{{ $u->created_at }}">{{ $u->created_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3 sm:px-5">
                                    @if($isMe)
                                        <span class="block text-right text-xs text-zinc-400">—</span>
                                    @else
                                        <div class="flex items-center justify-end gap-1">
                                            <form method="POST" action="{{ route('admin.users.block', $u) }}"
                                                  data-confirm-title="{{ $u->is_blocked ? 'Restore access?' : 'Suspend this user?' }}" data-confirm="{{ $u->is_blocked ? $u->email . ' will be able to sign in again.' : $u->email . ' will not be able to sign in until you restore access.' }}" data-confirm-ok="{{ $u->is_blocked ? 'Restore access' : 'Suspend user' }}" data-confirm-tone="{{ $u->is_blocked ? 'primary' : 'danger' }}">
                                                @csrf @method('PATCH')
                                                <button class="h-8 rounded-md px-2.5 text-xs font-medium {{ $u->is_blocked ? 'text-emerald-700 hover:bg-emerald-50' : 'text-zinc-600 hover:bg-zinc-100' }}">
                                                    {{ $u->is_blocked ? 'Restore' : 'Suspend' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                  data-confirm-title="Delete this user permanently?" data-confirm="{{ $u->email }} and all their records will be deleted. This can't be undone." data-confirm-ok="Delete user">
                                                @csrf @method('DELETE')
                                                <button class="h-8 w-8 rounded-md text-zinc-400 hover:bg-rose-50 hover:text-rose-600" title="Delete user" aria-label="Delete {{ $u->email }}">
                                                    <i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-2 border-t border-zinc-100 px-4 py-3 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ inr_number($users->total()) }}</span>
                @if($users->hasPages())<div>{{ $users->links('pagination::simple-tailwind') }}</div>@endif
            </div>
        @endif
    </x-admin.card>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('growthChart');
    if (el && typeof Chart !== 'undefined') {
        new Chart(el, {
            type: 'bar',
            data: { labels: @json($growthLabels), datasets: [{ data: @json($growthData), backgroundColor: '#6366f1', borderRadius: 3, maxBarThickness: 18 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: i => 'Week of ' + i[0].label, label: c => c.parsed.y + ' new user' + (c.parsed.y === 1 ? '' : 's') } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#a1a1aa', font: { size: 10 } } },
                    y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { precision: 0, color: '#a1a1aa', font: { size: 10 } } },
                },
            },
        });
    }
});

// Export the rows shown on this page (real data from the table)
function exportUsersCsv() {
    const rows = [...document.querySelectorAll('#usersTable tbody tr[data-csv]')].map(tr => JSON.parse(tr.dataset.csv));
    if (!rows.length) return;
    const esc = v => '"' + String(v ?? '').replace(/"/g, '""') + '"';
    const csv = [['ID', 'Name', 'Email', 'Role', 'Status', 'Plan', 'Records', 'Joined'], ...rows].map(r => r.map(esc).join(',')).join('\n');
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
    a.download = 'financeai-users-' + new Date().toISOString().slice(0, 10) + '.csv';
    a.click();
    URL.revokeObjectURL(a.href);
}
</script>
@endpush
