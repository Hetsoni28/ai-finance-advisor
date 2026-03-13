<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Expense Report | FinanceAI</title>

<style>

@page {
    margin: 40px 35px 80px 35px;
}

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color: #0f172a;
}

h1,h2,h3,h4 { margin:0; }

/* WATERMARK */
.watermark {
    position: fixed;
    top: 40%;
    left: 25%;
    font-size: 60px;
    color: rgba(0,0,0,0.04);
    transform: rotate(-30deg);
    z-index: -1;
}

/* HEADER */
.header {
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.brand {
    font-size: 22px;
    font-weight: bold;
}

.brand span {
    color:#2563eb;
}

.meta {
    float:right;
    text-align:right;
    font-size:9px;
    color:#64748b;
}

/* SUMMARY */
.summary {
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

.summary td {
    width:25%;
    border:1px solid #e5e7eb;
    background:#f8fafc;
    padding:10px;
    text-align:center;
}

.summary p {
    margin:0;
    font-size:9px;
    text-transform:uppercase;
    color:#64748b;
}

.summary h3 {
    margin-top:4px;
    font-size:14px;
}

/* INSIGHT */
.insight {
    border:1px solid #e5e7eb;
    background:#f9fafb;
    padding:14px;
    margin-bottom:20px;
    font-size:10px;
    line-height:1.7;
}

/* TABLE */
table.report {
    width:100%;
    border-collapse:collapse;
}

thead { display: table-header-group; }
tfoot { display: table-row-group; }

th {
    background:#f1f5f9;
    border:1px solid #cbd5e1;
    padding:6px;
    font-size:9px;
    text-transform:uppercase;
}

td {
    border:1px solid #e5e7eb;
    padding:6px;
    word-break: break-word;
}

tbody tr:nth-child(even) {
    background:#f9fafb;
}

.right { text-align:right; }

/* FOOTER */
.footer {
    position: fixed;
    bottom: -50px;
    left: 0;
    right: 0;
    height: 50px;
    border-top:1px solid #e5e7eb;
    font-size:9px;
    text-align:center;
    color:#64748b;
}

.page-number:before {
    content: "Page " counter(page) " of " counter(pages);
}

</style>
</head>

<body>

<div class="watermark">CONFIDENTIAL</div>

@php
$total = (float)($summary['total'] ?? 0);
$count = (int)($summary['count'] ?? 0);
$average = (float)($summary['average'] ?? 0);
$highest = $summary['highest'] ?? null;
$topCategory = $summary['topCategory'] ?? null;
$scope = $summary['scope'] ?? 'Personal';

$riskScore = 0;
if($average > 5000) $riskScore = 3;
elseif($average > 2000) $riskScore = 2;
else $riskScore = 1;

function currency($v){
    return '₹'.number_format($v,2);
}
@endphp

{{-- HEADER --}}
<div class="header">
    <div class="brand">
        Finance<span>AI</span>
    </div>

    <div class="meta">
        <strong>Expense Intelligence Report</strong><br>
        Report ID: {{ $reportId ?? 'N/A' }}<br>
        Generated {{ now()->format('d M Y, h:i A') }}<br>
        Scope: {{ $scope }}
    </div>

    <div style="clear:both;"></div>
</div>

{{-- SUMMARY --}}
<table class="summary">
<tr>
<td>
<p>Total Expenses</p>
<h3>{{ currency($total) }}</h3>
</td>

<td>
<p>Transactions</p>
<h3>{{ $count }}</h3>
</td>

<td>
<p>Average</p>
<h3>{{ currency($average) }}</h3>
</td>

<td>
<p>Risk Level</p>
<h3>
@if($riskScore==3)
High
@elseif($riskScore==2)
Moderate
@else
Low
@endif
</h3>
</td>
</tr>
</table>

{{-- INSIGHT --}}
<div class="insight">

<strong>Executive Financial Analysis</strong><br><br>

• Highest Expense:
{{ $highest ? $highest->title.' ('.currency($highest->amount).')' : 'N/A' }}<br>

• Dominant Category:
{{ $topCategory ?? 'None' }}<br>

• Spending Behavior:
@if($average > 5000)
High transaction concentration detected.
@elseif($average > 2000)
Moderate expense distribution observed.
@else
Stable and controlled spending.
@endif<br>

• Strategic Recommendation:
{{ $topCategory
? 'Audit high allocation category and consider optimization.'
: 'Maintain diversified expense distribution.' }}

</div>

{{-- TABLE --}}
<table class="report">
<thead>
<tr>
<th width="5%">#</th>
<th width="35%">Title</th>
<th width="20%">Category</th>
<th width="15%" class="right">Amount</th>
<th width="15%">Date</th>
</tr>
</thead>

<tbody>
@forelse($expenses as $index => $expense)
<tr>
<td>{{ $index+1 }}</td>
<td>{{ $expense->title }}</td>
<td>{{ $expense->category }}</td>
<td class="right">{{ currency($expense->amount) }}</td>
<td>{{ optional($expense->expense_date)->format('d M Y') }}</td>
</tr>
@empty
<tr>
<td colspan="5" style="text-align:center;padding:12px;color:#64748b;">
No expenses recorded.
</td>
</tr>
@endforelse
</tbody>

@if($count>0)
<tfoot>
<tr>
<th colspan="3" class="right">Grand Total</th>
<th class="right">{{ currency($total) }}</th>
<th></th>
</tr>
</tfoot>
@endif

</table>

{{-- FOOTER --}}
<div class="footer">
© {{ date('Y') }} FinanceAI • Confidential Financial Document<br>
<span class="page-number"></span>
</div>

</body>
</html>