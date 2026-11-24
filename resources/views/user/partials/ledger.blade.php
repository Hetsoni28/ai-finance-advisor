{{--
    Shared ledger page for Income and Expenses.
    Expects: $cfg = [type, label, items (paginator), model, dateCol, titleCol, categories, color,
                     routes => [create, edit, destroy, index, pdf], stats => [...]]
    All figures are queried from the user's own personal records.
--}}
@php
    $inr = fn ($v) => '₹' . number_format(abs((float) $v), 0);
    $isIncome = $cfg['type'] === 'income';
    $model = $cfg['model'];
    $dateCol = $cfg['dateCol'];
    $titleCol = $cfg['titleCol'];
    $base = fn () => $model::where('user_id', auth()->id())->where('is_personal', true)->whereNull('family_id');

    // Last 6 months (real)
    $start = now()->startOfMonth()->subMonths(5);
    $byMonth = $base()->where($dateCol, '>=', $start)
        ->selectRaw("DATE_FORMAT($dateCol, '%Y-%m') as ym, SUM(amount) as total")->groupBy('ym')->pluck('total', 'ym');
    $chartLabels = $chartValues = [];
    for ($i = 0; $i < 6; $i++) { $m = $start->copy()->addMonths($i); $chartLabels[] = $m->format('M'); $chartValues[] = (float) ($byMonth[$m->format('Y-m')] ?? 0); }
    $thisMonth = end($chartValues); $lastMonth = $chartValues[4];
    $delta = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : null;

    // All-time (personal) totals and category split
    $allTotal = (float) $base()->sum('amount');
    $allCount = (int) $base()->count();
    $byCategory = $base()->selectRaw("COALESCE(NULLIF(category, ''), 'Uncategorized') as category, SUM(amount) as total, COUNT(*) as n")
        ->groupBy('category')->orderByDesc('total')->get();
    $catTotal = (float) $byCategory->sum('total');
    $palette = ['#6366f1', '#10b981', '#f59e0b', '#0ea5e9', '#f43f5e', '#a855f7', '#71717a'];

    $filtered = request()->filled('search') || request()->filled('category') || request()->filled('from') || request()->filled('to');
    $accent = $isIncome ? 'emerald' : 'rose';
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header :title="$cfg['label']" eyebrow="My money" icon="fa-wallet" :stamp="false" :description="$cfg['description']">
        <x-slot name="actions">
            @if($allCount > 0)
                <a href="{{ $cfg['routes']['pdf'] }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <i class="fa-solid fa-file-pdf text-xs text-zinc-400" aria-hidden="true"></i> Download PDF
                </a>
            @endif
            <a href="{{ $cfg['routes']['create'] }}" class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> {{ $cfg['addLabel'] }}
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat :label="'This month'" :value="$inr($thisMonth)" :icon="$isIncome ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'" :tone="$accent"
            :spark="$chartValues" :delta="$delta" :goodWhen="$isIncome ? 'up' : 'down'"
            :hint="$delta === null ? now()->format('F Y') : null" />
        <x-admin.stat label="All time" :value="$inr($allTotal)" icon="fa-layer-group" tone="zinc" :hint="number_format($allCount) . ' ' . \Illuminate\Support\Str::plural('record', $allCount)" />
        <x-admin.stat label="Average entry" :value="$allCount ? $inr($allTotal / $allCount) : '—'" icon="fa-divide" tone="sky" />
        <x-admin.stat label="Top category" :value="$byCategory->first()->category ?? '—'" icon="fa-crown" tone="amber"
            :hint="$byCategory->isNotEmpty() && $catTotal > 0 ? round($byCategory->first()->total / $catTotal * 100) . '% of total' : null" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2" title="Last 6 months" :description="$isIncome ? 'Income by month' : 'Spending by month'">
            <div class="relative h-56">
                <canvas id="ledgerChart" role="img" aria-label="{{ $cfg['label'] }} per month for the last 6 months"></canvas>
                @if(array_sum($chartValues) == 0)
                    <x-admin.empty class="absolute inset-0 bg-white" icon="fa-chart-column" title="Nothing in the last 6 months" />
                @endif
            </div>
        </x-admin.card>

        <x-admin.card title="By category" description="All time">
            @if($byCategory->isEmpty())
                <x-admin.empty icon="fa-chart-pie" title="No records yet" />
            @else
                <ul class="space-y-3">
                    @foreach($byCategory as $i => $c)
                        @php $share = $catTotal > 0 ? round($c->total / $catTotal * 100) : 0; @endphp
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['category' => $c->category === 'Uncategorized' ? null : $c->category, 'page' => null]) }}" class="block rounded-md hover:bg-zinc-50">
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <span class="flex items-center gap-2 truncate text-zinc-700"><span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $palette[$i % 7] }}"></span>{{ $c->category }} <span class="text-xs text-zinc-400">{{ $c->n }}</span></span>
                                    <span class="shrink-0 tabular-nums text-zinc-900">{{ $inr($c->total) }}</span>
                                </div>
                                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full" style="width: {{ $share }}%; background: {{ $palette[$i % 7] }}"></div></div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <x-admin.card flush>
        <form method="GET" action="{{ $cfg['routes']['index'] }}" class="flex flex-col gap-2 border-b border-zinc-100 p-3 lg:flex-row lg:items-center">
            <label class="relative flex-1">
                <span class="sr-only">Search</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400" aria-hidden="true"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $isIncome ? 'Search by source' : 'Search by title' }}"
                       class="h-9 w-full rounded-lg border border-zinc-200 pl-8 pr-3 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </label>
            <select name="category" aria-label="Category" class="h-9 rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
                <option value="">All categories</option>
                @foreach($cfg['categories'] as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 text-xs text-zinc-500">From
                <input type="date" name="from" value="{{ request('from') }}" class="h-9 rounded-lg border border-zinc-200 px-2 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
            </label>
            @if(!$isIncome)
                <label class="flex items-center gap-2 text-xs text-zinc-500">To
                    <input type="date" name="to" value="{{ request('to') }}" class="h-9 rounded-lg border border-zinc-200 px-2 text-sm text-zinc-700 focus:border-indigo-400 focus:outline-none">
                </label>
            @endif
            <button class="h-9 rounded-lg bg-zinc-900 px-4 text-sm font-medium text-white hover:bg-zinc-800">Apply</button>
            @if($filtered)<a href="{{ $cfg['routes']['index'] }}" class="h-9 px-2 text-sm font-medium leading-9 text-zinc-500 hover:text-zinc-900">Clear</a>@endif
        </form>

        @if($cfg['items']->isEmpty())
            @if($filtered)
                <x-admin.empty icon="fa-filter" title="Nothing matches these filters" text="Try a different search or clear the filters." />
            @else
                <x-admin.empty :icon="$isIncome ? 'fa-sack-dollar' : 'fa-receipt'" :title="$cfg['emptyTitle']" :text="$cfg['emptyText']">
                    <a href="{{ $cfg['routes']['create'] }}" class="mt-4 inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white hover:bg-zinc-800"><i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> {{ $cfg['addLabel'] }}</a>
                </x-admin.empty>
            @endif
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 text-left text-xs text-zinc-500">
                            <th class="px-4 py-2.5 font-medium sm:px-5">{{ $isIncome ? 'Source' : 'Title' }}</th>
                            <th class="px-4 py-2.5 font-medium">Category</th>
                            <th class="px-4 py-2.5 font-medium">Date</th>
                            <th class="px-4 py-2.5 text-right font-medium">Amount</th>
                            <th class="px-4 py-2.5 sm:px-5"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($cfg['items'] as $row)
                            <tr class="group hover:bg-zinc-50/60">
                                <td class="px-4 py-3 sm:px-5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-{{ $accent }}-50 text-{{ $accent }}-600"><i class="fa-solid {{ $isIncome ? 'fa-arrow-down' : 'fa-arrow-up' }} text-[11px]" aria-hidden="true"></i></span>
                                        <span class="font-medium text-zinc-900">{{ $row->{$titleCol} ?: ($isIncome ? 'Income' : 'Expense') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><x-admin.badge>{{ $row->category ?: 'Uncategorized' }}</x-admin.badge></td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-500">{{ \Carbon\Carbon::parse($row->{$dateCol})->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-semibold tabular-nums {{ $isIncome ? 'text-emerald-600' : 'text-zinc-900' }}">{{ $isIncome ? '+' : '−' }}{{ $inr($row->amount) }}</td>
                                <td class="px-4 py-3 sm:px-5">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route($cfg['routes']['edit'], $row) }}" class="flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700" title="Edit" aria-label="Edit {{ $row->{$titleCol} }}"><i class="fa-solid fa-pen text-xs" aria-hidden="true"></i></a>
                                        <form method="POST" action="{{ route($cfg['routes']['destroy'], $row) }}" onsubmit="return confirm('Delete this {{ $isIncome ? 'income' : 'expense' }} ({{ $inr($row->amount) }})? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button class="flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 hover:bg-rose-50 hover:text-rose-600" title="Delete" aria-label="Delete {{ $row->{$titleCol} }}"><i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-2 border-t border-zinc-100 px-4 py-3 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <span>Showing {{ $cfg['items']->firstItem() }}–{{ $cfg['items']->lastItem() }} of {{ number_format($cfg['items']->total()) }}@if($filtered) · filtered total {{ $inr($cfg['filteredTotal']) }}@endif</span>
                @if($cfg['items']->hasPages())<div>{{ $cfg['items']->links('pagination::simple-tailwind') }}</div>@endif
            </div>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('ledgerChart');
    if (!el || typeof Chart === 'undefined') return;
    const color = @json($isIncome ? '#10b981' : '#f43f5e');
    new Chart(el, {
        type: 'bar',
        data: { labels: @json($chartLabels), datasets: [{ data: @json($chartValues), backgroundColor: color, borderRadius: 4, maxBarThickness: 34 }] },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ₹' + Number(c.parsed.y).toLocaleString('en-IN') } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 11 } } },
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f4f4f5' }, ticks: { color: '#a1a1aa', font: { size: 11 }, callback: v => v >= 1000 ? '₹' + (v / 1000) + 'k' : '₹' + v } },
            },
        },
    });
});
</script>
@endpush
