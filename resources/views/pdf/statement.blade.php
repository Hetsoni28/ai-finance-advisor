@php
    $inr = require resource_path('views/pdf/partials/inr.php');
    $isExpense = $kind === 'expense';
    $label = $isExpense ? 'Expense' : 'Income';
    $maxCat = max(1, (float) ($byCategory->max('total') ?? 0));
    $maxMonth = max(1, (float) ($byMonth->max('total') ?? 0));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $label }} statement · FinanceAI</title>
    @include('pdf.partials.style')
</head>
<body>

<div class="footer">
    FinanceAI · {{ $label }} statement for {{ $user->name }} · {{ $statementNo }}
    <span class="right">Page <span class="pg"></span></span>
</div>

<table class="brand">
    <tr>
        <td><div class="logo">Finance<span>AI</span></div><div class="muted" style="font-size:8.5px">AI Personal Finance Advisor</div></td>
        <td>
            <div class="doc-title">{{ $label }} Statement</div>
            <div class="doc-sub">Personal {{ strtolower($label) }} records</div>
        </td>
    </tr>
</table>

<table class="meta">
    <tr>
        <td class="k">Account holder</td><td class="v">{{ $user->name }}</td>
        <td class="k">Statement no.</td><td class="v">{{ $statementNo }}</td>
    </tr>
    <tr>
        <td class="k">Email</td><td class="v">{{ $user->email }}</td>
        <td class="k">Generated on</td><td class="v">{{ $generatedAt }}</td>
    </tr>
    <tr>
        <td class="k">Period covered</td><td class="v">{{ $period }}</td>
        <td class="k">Entries</td><td class="v">{{ number_format($summary['count']) }}</td>
    </tr>
</table>

@if ($summary['count'] === 0)
    <div class="end">There are no personal {{ strtolower($label) }} entries to show yet. Add an entry in FinanceAI and download the statement again.</div>
@else

<table class="cards">
    <tr>
        <td class="card"><div class="k">Total {{ $isExpense ? 'spent' : 'received' }}</div><div class="v">{{ $inr($summary['total']) }}</div><div class="n">{{ number_format($summary['count']) }} entries</div></td>
        <td class="card"><div class="k">This month</div><div class="v">{{ $inr($summary['thisMonth']) }}</div><div class="n">{{ now()->format('F Y') }}</div></td>
        <td class="card"><div class="k">Average entry</div><div class="v">{{ $inr($summary['average']) }}</div><div class="n">per {{ strtolower($label) }}</div></td>
        <td class="card"><div class="k">Largest entry</div><div class="v">{{ $inr($summary['highest']) }}</div><div class="n">{{ \Illuminate\Support\Str::limit($summary['highestTitle'], 26) }}</div></td>
    </tr>
</table>

<div class="keep">
<h2>{{ $label }} by category</h2>
<table class="data">
    <thead><tr><th style="width:24%">Category</th><th class="r" style="width:11%">Entries</th><th class="r" style="width:20%">Amount</th><th class="r" style="width:11%">Share</th><th style="width:34%"></th></tr></thead>
    <tbody>
    @foreach ($byCategory as $i => $c)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td><strong>{{ $c->category ?: 'Others' }}</strong></td>
            <td class="r">{{ $c->entries }}</td>
            <td class="r">{{ $inr($c->total) }}</td>
            <td class="r">{{ $summary['total'] > 0 ? number_format($c->total / $summary['total'] * 100, 1) : '0.0' }}%</td>
            <td><div class="bar {{ $isExpense ? '' : 'green' }}"><div style="width: {{ round($c->total / $maxCat * 100) }}%"></div></div></td>
        </tr>
    @endforeach
        <tr class="total"><td>Total</td><td class="r">{{ number_format($summary['count']) }}</td><td class="r">{{ $inr($summary['total']) }}</td><td class="r">100%</td><td></td></tr>
    </tbody>
</table>
</div>

<div class="keep">
<h2>Month by month</h2>
<table class="data">
    <thead><tr><th style="width:24%">Month</th><th class="r" style="width:11%">Entries</th><th class="r" style="width:20%">Amount</th><th class="r" style="width:11%">Change</th><th style="width:34%"></th></tr></thead>
    <tbody>
    @foreach ($byMonth as $i => $m)
        @php $prev = $byMonth[$i + 1] ?? null; $chg = $prev && $prev->total > 0 ? ($m->total - $prev->total) / $prev->total * 100 : null; @endphp
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td><strong>{{ \Carbon\Carbon::create($m->y, $m->m, 1)->format('F Y') }}</strong></td>
            <td class="r">{{ $m->entries }}</td>
            <td class="r">{{ $inr($m->total) }}</td>
            <td class="r">@if ($chg === null)<span class="muted">—</span>@elseif (round($chg) == 0)<span class="muted">0%</span>@else<span class="{{ ($chg > 0) === $isExpense ? 'neg' : 'pos' }}">{{ $chg > 0 ? '+' : '' }}{{ number_format($chg, 0) }}%</span>@endif</td>
            <td><div class="bar {{ $isExpense ? '' : 'green' }}"><div style="width: {{ round($m->total / $maxMonth * 100) }}%"></div></div></td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="note" style="margin-top:4px">Change compares each month with the month before it.@if ($summary['partialMonth']) {{ now()->format('F') }} is still in progress.@endif</div>
</div>

<h2>All entries</h2>
<div class="note">Newest first, grouped by month. @if ($summary['count'] > $rows->count()) Showing the latest {{ $rows->count() }} of {{ $summary['count'] }} entries. @endif</div>
<table class="data">
    <thead><tr><th style="width:5%">#</th><th style="width:15%">Date</th><th style="width:44%">{{ $isExpense ? 'Description' : 'Source' }}</th><th style="width:16%">Category</th><th class="r" style="width:20%">Amount</th></tr></thead>
    <tbody>
    @php $n = 0; @endphp
    @foreach ($rows->groupBy(fn ($r) => $r->date->format('Y-m')) as $ym => $group)
        <tr class="sub"><td colspan="3">{{ \Carbon\Carbon::parse($ym . '-01')->format('F Y') }}</td><td class="r">{{ $group->count() }} {{ $group->count() === 1 ? 'entry' : 'entries' }}</td><td class="r">{{ $inr($group->sum('amount')) }}</td></tr>
        @foreach ($group as $r)
            @php $n++; @endphp
            <tr class="{{ $n % 2 ? '' : 'alt' }}">
                <td class="muted">{{ $n }}</td>
                <td>{{ $r->date->format('d M Y') }}</td>
                <td>{{ $r->title }}</td>
                <td><span class="pill {{ $isExpense ? '' : 'green' }}">{{ $r->category ?: 'Others' }}</span></td>
                <td class="r {{ $isExpense ? 'neg' : 'pos' }}">{{ $isExpense ? '-' : '+' }}{{ $inr($r->amount) }}</td>
            </tr>
        @endforeach
    @endforeach
        <tr class="total"><td colspan="4">Total {{ $isExpense ? 'spent' : 'received' }} ({{ number_format($rows->count()) }} entries shown)</td><td class="r">{{ $inr($rows->sum('amount')) }}</td></tr>
    </tbody>
</table>

<div class="end">
    This statement lists your personal {{ strtolower($label) }} entries recorded in FinanceAI. Family workspace entries are not included.
    Amounts are in Indian Rupees. Generated automatically on {{ $generatedAt }}.
</div>
@endif

</body>
</html>
