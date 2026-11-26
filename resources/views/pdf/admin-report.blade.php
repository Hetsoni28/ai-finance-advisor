@php
    $inr = require resource_path('views/pdf/partials/inr.php');
    $maxMonth = max(1, (float) $months->max(fn ($m) => max($m->income, $m->expenses)));
    $maxCat = max(1, (float) ($categories->max('total') ?? 0));
    $catTotal = (float) $categories->sum('total');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Platform report · FinanceAI</title>
    @include('pdf.partials.style')
</head>
<body>

<div class="footer">
    FinanceAI · Platform report · {{ $reportNo }} · For admin use only
    <span class="right">Page <span class="pg"></span></span>
</div>

<table class="brand">
    <tr>
        <td><div class="logo">Finance<span>AI</span></div><div class="muted" style="font-size:8.5px">AI Personal Finance Advisor</div></td>
        <td>
            <div class="doc-title">Platform Report</div>
            <div class="doc-sub">Users, money recorded and subscriptions</div>
        </td>
    </tr>
</table>

<table class="meta">
    <tr>
        <td class="k">Prepared for</td><td class="v">{{ $admin->name ?? 'Admin' }} (Admin)</td>
        <td class="k">Report no.</td><td class="v">{{ $reportNo }}</td>
    </tr>
    <tr>
        <td class="k">Generated on</td><td class="v">{{ $generatedAt }}</td>
        <td class="k">Data</td><td class="v">All accounts, live database</td>
    </tr>
</table>

<h2>Users</h2>
<table class="cards">
    <tr>
        <td class="card"><div class="k">Total users</div><div class="v">{{ $people['total'] }}</div><div class="n">{{ $people['admins'] }} admin{{ $people['admins'] === 1 ? '' : 's' }}</div></td>
        <td class="card"><div class="k">Active</div><div class="v">{{ $people['active'] }}</div><div class="n">{{ $people['suspended'] }} suspended</div></td>
        <td class="card"><div class="k">New in last 30 days</div><div class="v">{{ $people['new30'] }}</div><div class="n">sign-ups</div></td>
        <td class="card"><div class="k">Paid subscribers</div><div class="v">{{ $paidUsers }}</div><div class="n">{{ $people['total'] > 0 ? round($paidUsers / $people['total'] * 100) : 0 }}% of users</div></td>
    </tr>
</table>

<h2>Money recorded by all users</h2>
<table class="cards">
    <tr>
        <td class="card"><div class="k">Total income</div><div class="v pos">{{ $inr($money['income'], 0) }}</div><div class="n">{{ inr_number($money['incomeRows']) }} entries</div></td>
        <td class="card"><div class="k">Total expenses</div><div class="v neg">{{ $inr($money['expenses'], 0) }}</div><div class="n">{{ inr_number($money['expenseRows']) }} entries</div></td>
        <td class="card"><div class="k">Net saved</div><div class="v">{{ $inr($money['net'], 0) }}</div><div class="n">income minus expenses</div></td>
        <td class="card"><div class="k">Savings rate</div><div class="v">{{ inr_number($money['rate'], 1) }}%</div><div class="n">of income kept</div></td>
    </tr>
</table>

<div class="keep">
<h2>Last six months</h2>
<table class="data">
    <thead><tr><th style="width:17%">Month</th><th class="r" style="width:15%">Income</th><th class="r" style="width:15%">Expenses</th><th class="r" style="width:15%">Net</th><th class="r" style="width:10%">Sign-ups</th><th style="width:28%">Income <span style="color:#86efac">■</span> / expenses <span style="color:#fca5a5">■</span></th></tr></thead>
    <tbody>
    @foreach ($months as $i => $m)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td><strong>{{ $m->label }}</strong></td>
            <td class="r">{{ $inr($m->income, 0) }}</td>
            <td class="r">{{ $inr($m->expenses, 0) }}</td>
            <td class="r {{ $m->net < 0 ? 'neg' : 'pos' }}">{{ $inr($m->net, 0) }}</td>
            <td class="r">{{ $m->signups }}</td>
            <td>
                <div class="bar green" style="margin-bottom:2px"><div style="width: {{ round($m->income / $maxMonth * 100) }}%"></div></div>
                <div class="bar red"><div style="width: {{ round($m->expenses / $maxMonth * 100) }}%"></div></div>
            </td>
        </tr>
    @endforeach
        <tr class="total"><td>6-month total</td><td class="r">{{ $inr($months->sum('income'), 0) }}</td><td class="r">{{ $inr($months->sum('expenses'), 0) }}</td><td class="r">{{ $inr($months->sum('net'), 0) }}</td><td class="r">{{ $months->sum('signups') }}</td><td></td></tr>
    </tbody>
</table>
<div class="note" style="margin-top:4px">Grouped by the date of each transaction. {{ now()->format('F') }} is still in progress.</div>
</div>

<div class="keep">
<h2>Where users spend</h2>
<table class="data">
    <thead><tr><th style="width:24%">Category</th><th class="r" style="width:11%">Entries</th><th class="r" style="width:20%">Amount</th><th class="r" style="width:11%">Share</th><th style="width:34%"></th></tr></thead>
    <tbody>
    @forelse ($categories as $i => $c)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td><strong>{{ $c->category ?: 'Others' }}</strong></td>
            <td class="r">{{ inr_number($c->entries) }}</td>
            <td class="r">{{ $inr($c->total, 0) }}</td>
            <td class="r">{{ $catTotal > 0 ? inr_number($c->total / $catTotal * 100, 1) : '0.0' }}%</td>
            <td><div class="bar"><div style="width: {{ round($c->total / $maxCat * 100) }}%"></div></div></td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted c">No expenses recorded yet.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

<div class="keep">
<h2>Plans and subscriptions</h2>
<table class="data">
    <thead><tr><th style="width:28%">Plan</th><th class="r" style="width:18%">Price</th><th class="r" style="width:18%">Active users</th><th class="r" style="width:16%">Times bought</th><th class="r" style="width:20%">Revenue (demo)</th></tr></thead>
    <tbody>
    @foreach ($plans as $i => $p)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td><strong>{{ $p->name }}</strong></td>
            <td class="r">{{ $p->price > 0 ? $inr($p->price, 0) . ' / month' : 'Free' }}</td>
            <td class="r">{{ $p->price > 0 ? $p->active : max(0, $people['total'] - $paidUsers) }}</td>
            <td class="r">{{ $p->sold }}</td>
            <td class="r">{{ $inr($p->revenue, 0) }}</td>
        </tr>
    @endforeach
        <tr class="total"><td colspan="3">Total</td><td class="r">{{ $plans->sum('sold') }}</td><td class="r">{{ $inr($plans->sum('revenue'), 0) }}</td></tr>
    </tbody>
</table>
<div class="note" style="margin-top:4px">Users without a paid plan are on the free Starter plan. Payments are demo payments; no real money is charged.</div>
</div>

<h2>All users</h2>
<div class="note">Newest first. Records = income and expense entries. Personal amounts are not shown here to protect each user’s privacy.</div>
<table class="data">
    <thead><tr><th style="width:5%">#</th><th style="width:22%">Name</th><th style="width:29%">Email</th><th style="width:13%">Plan</th><th style="width:11%">Status</th><th class="r" style="width:8%">Records</th><th class="r" style="width:12%">Joined</th></tr></thead>
    <tbody>
    @foreach ($users as $i => $u)
        @php $isAdmin = $u->role === 'admin' || $u->is_admin; @endphp
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td class="muted">{{ $i + 1 }}</td>
            <td><strong>{{ $u->name }}</strong>@if ($isAdmin) <span class="pill">Admin</span>@endif</td>
            <td>{{ $u->email }}</td>
            <td>{{ optional(optional($u->activeSubscription)->plan)->name ?? 'Starter' }}</td>
            <td>@if ($u->is_blocked)<span class="pill red">Suspended</span>@else<span class="pill green">Active</span>@endif</td>
            <td class="r">{{ $u->incomes_count + $u->expenses_count }}</td>
            <td class="r">{{ $u->created_at->format('d M Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="keep">
<h2>Recent activity</h2>
<table class="data">
    <thead><tr><th style="width:20%">When</th><th style="width:22%">User</th><th style="width:58%">What happened</th></tr></thead>
    <tbody>
    @forelse ($activity as $i => $a)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td>{{ $a->created_at->format('d M Y, h:i A') }}</td>
            <td>{{ optional($a->user)->name ?? 'System' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($a->description, 95) }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="muted c">No activity recorded yet.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

<div class="end">
    Contact messages: {{ $messages['total'] }} in total, {{ $messages['unread'] }} unread.
    This report is generated from the FinanceAI database on {{ $generatedAt }} and is meant for administrators only.
</div>

</body>
</html>
