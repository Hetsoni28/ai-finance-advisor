<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>FinanceAI Income Ledger | {{ $reportId ?? 'DRAFT' }}</title>

    <style>
        /* DOMPDF-safe CSS: no flexbox, no grid, tables for layout. Mirrors user/expenses/pdf. */
        @page { margin: 130px 40px 80px 40px; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
            background-color: #ffffff;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -130px;
            left: -40px;
            right: -40px;
            height: 90px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .header-stripe { width: 100%; border-collapse: collapse; }
        .header-stripe td { height: 4px; padding: 0; }
        .header-content { padding: 25px 40px 0 40px; }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 8px;
            padding-top: 15px;
            font-family: 'Courier New', Courier, monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .page-number:after { content: counter(page); }

        h1, h2, h3, p { margin: 0; padding: 0; }
        h1 { font-size: 26px; letter-spacing: -0.5px; color: #0f172a; font-weight: 900; }
        h1 span { color: #4f46e5; }
        h2 { font-size: 14px; margin-top: 30px; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; color: #0f172a; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        h3 { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; margin-bottom: 4px; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .mt-10 { margin-top: 10px; }
        .mt-20 { margin-top: 20px; }
        .mb-10 { margin-bottom: 10px; }
        .page-break { page-break-after: always; }
        .avoid-break { page-break-inside: avoid; }

        .text-indigo { color: #4f46e5; }
        .text-emerald { color: #10b981; }
        .text-slate { color: #64748b; }
        .text-slate-dark { color: #0f172a; }

        .layout-table { width: 100%; border-collapse: collapse; border: none; }
        .layout-table td { vertical-align: top; padding: 0; }

        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-left: -8px;
            margin-right: -8px;
            margin-top: 15px;
        }
        .kpi-table td {
            width: 25%;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 12px;
            vertical-align: top;
        }
        .kpi-val { font-size: 16px; font-weight: 900; color: #0f172a; margin-top: 4px; font-family: 'Courier New', Courier, monospace; letter-spacing: -0.5px; }

        .meta-table {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }
        .meta-table td { padding: 8px 12px; border-bottom: 1px solid #f8fafc; font-size: 9px; }
        .meta-table td:first-child { width: 140px; font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 8px; letter-spacing: 1px; background-color: #f8fafc; border-right: 1px solid #e2e8f0; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px;
            text-align: left;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e1;
        }
        .data-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; font-size: 9px; }
        .data-table tr:nth-child(even) td { background-color: #fcfcfd; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-emerald { background: #d1fae5; color: #059669; border: 1px solid #6ee7b7; }
        .badge-slate { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .progress-wrapper { background-color: #f1f5f9; border-radius: 2px; height: 6px; width: 100%; margin-top: 4px; overflow: hidden; border: 1px solid #e2e8f0; }
        .progress-fill { height: 100%; border-radius: 2px; }

        .row-micro-bar { height: 3px; background-color: #f1f5f9; border-radius: 2px; margin-top: 4px; width: 100%; overflow: hidden; }
        .row-micro-fill { height: 100%; background-color: #10b981; border-radius: 2px; }

        .crypto-block {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 15px;
            border-radius: 4px;
            font-size: 8px;
            margin-top: 30px;
            border: 1px solid #e2e8f0;
            font-family: 'Courier New', Courier, monospace;
        }
        .crypto-title { font-weight: 900; font-size: 9px; margin-bottom: 8px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 4px; letter-spacing: 1px; }
    </style>
</head>
<body>

@php
    $summary  = $summary ?? [];
    $incomes  = $incomes ?? collect([]);
    $reportId = $reportId ?? 'FA-INC-' . now()->format('YmdHis');

    $total   = (float) ($summary['total'] ?? 0);
    $count   = (int) ($summary['count'] ?? count($incomes));
    $average = (float) ($summary['average'] ?? ($count > 0 ? $total / $count : 0));
    $highest = (float) ($summary['highest'] ?? 0);
    $topCategory = $summary['topCategory'] ?? 'Uncategorized';

    $currency = function ($v) { return 'INR ' . number_format((float) $v, 2); };

    // Category and source breakdown for the listed records
    $categoryTotals = [];
    $sourceTotals = [];
    $listedTotal = 0;
    $listedHighest = 0;
    foreach ($incomes as $inc) {
        $amt = (float) ($inc->amount ?? 0);
        $cat = $inc->category ?: 'Uncategorized';
        $src = $inc->source ?: 'Unspecified';
        $categoryTotals[$cat] = ($categoryTotals[$cat] ?? 0) + $amt;
        $sourceTotals[$src] = ($sourceTotals[$src] ?? 0) + $amt;
        $listedTotal += $amt;
        $listedHighest = max($listedHighest, $amt);
    }
    arsort($categoryTotals);
    arsort($sourceTotals);
    $sourceTotals = array_slice($sourceTotals, 0, 10, true);

    $generationTime = now()->toIso8601String();
    $verificationHash = hash('sha256', $reportId . $total . $count . $generationTime . config('app.key'));
    $colorPalette = ['#10b981', '#4f46e5', '#0ea5e9', '#f59e0b', '#a855f7', '#e11d48', '#64748b'];
@endphp

<header>
    <table class="header-stripe" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background-color: #10b981; width: 25%;"></td>
            <td style="background-color: #0ea5e9; width: 25%;"></td>
            <td style="background-color: #4f46e5; width: 25%;"></td>
            <td style="background-color: #a855f7; width: 25%;"></td>
        </tr>
    </table>
    <div class="header-content">
        <table class="layout-table">
            <tr>
                <td style="vertical-align: middle;">
                    <h1>Finance<span>AI</span></h1>
                    <p style="font-size: 8px; font-weight: 900; color: #94a3b8; margin-top: 2px; letter-spacing: 1px;">INCOME LEDGER REPORT</p>
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <p style="font-size: 7px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Report ID</p>
                    <p style="font-size: 11px; font-weight: 900; color: #0f172a;" class="font-mono">{{ $reportId }}</p>
                </td>
            </tr>
        </table>
    </div>
</header>

<footer>
    FinanceAI Income Report &bull; Confidential &bull; Page <span class="page-number"></span>
</footer>

{{-- Summary --}}
<div class="mt-10">
    <h2 style="border:none; margin-bottom: 2px;">Income Summary</h2>
    <p class="text-slate">Overview of your personal income records by category and source.</p>
</div>

<table class="meta-table">
    <tr>
        <td>Account</td>
        <td class="text-slate-dark font-bold">{{ auth()->user()->name ?? 'Account holder' }} ({{ auth()->user()->email ?? '' }})</td>
    </tr>
    <tr>
        <td>Generated</td>
        <td class="font-mono text-slate-dark">{{ now()->format('Y-m-d H:i:s T') }}</td>
    </tr>
    <tr>
        <td>Records</td>
        <td>
            {{ number_format($count) }} income {{ \Illuminate\Support\Str::plural('record', $count) }}
            @if($count > $incomes->count())
                <span class="text-slate">(ledger below shows the latest {{ $incomes->count() }})</span>
            @endif
        </td>
    </tr>
</table>

<table class="kpi-table avoid-break">
    <tr>
        <td>
            <h3>Total Income</h3>
            <div class="kpi-val text-emerald">{{ $currency($total) }}</div>
        </td>
        <td>
            <h3>Average Entry</h3>
            <div class="kpi-val text-indigo">{{ $currency($average) }}</div>
        </td>
        <td>
            <h3>Largest Entry</h3>
            <div class="kpi-val">{{ $currency($highest) }}</div>
        </td>
        <td>
            <h3>Top Category</h3>
            <div style="margin-top: 6px;"><span class="badge badge-emerald">{{ $topCategory }}</span></div>
        </td>
    </tr>
</table>

{{-- Category breakdown --}}
<h2>Income by Category</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 35%;">Category</th>
            <th style="width: 25%; text-align: right;">Amount (INR)</th>
            <th style="width: 10%; text-align: right;">Share</th>
            <th style="width: 30%;"></th>
        </tr>
    </thead>
    <tbody>
        @php $ci = 0; @endphp
        @forelse($categoryTotals as $catName => $catTotal)
            @php $pct = $listedTotal > 0 ? round($catTotal / $listedTotal * 100, 1) : 0; @endphp
            <tr class="avoid-break">
                <td class="font-bold text-slate-dark">{{ $catName }}</td>
                <td class="text-right font-mono">{{ $currency($catTotal) }}</td>
                <td class="text-right font-bold">{{ $pct }}%</td>
                <td>
                    <div class="progress-wrapper">
                        <div class="progress-fill" style="width: {{ $pct }}%; background-color: {{ $colorPalette[$ci++ % count($colorPalette)] }};"></div>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-slate" style="padding: 20px 0;">No income recorded yet.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Source breakdown --}}
@if(count($sourceTotals) > 0)
    <div class="avoid-break">
        <h2>Top Income Sources</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Source</th>
                    <th style="width: 25%; text-align: right;">Amount (INR)</th>
                    <th style="width: 15%; text-align: right;">Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sourceTotals as $srcName => $srcTotal)
                    <tr>
                        <td class="font-bold text-slate-dark">{{ $srcName }}</td>
                        <td class="text-right font-mono">{{ $currency($srcTotal) }}</td>
                        <td class="text-right">{{ $listedTotal > 0 ? round($srcTotal / $listedTotal * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="page-break"></div>

{{-- Ledger --}}
<h2>Income Ledger</h2>
<p class="text-slate mb-10">Most recent income entries, newest first.</p>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 35%;">Source</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 15%; text-align: right;">Date</th>
            <th style="width: 25%; text-align: right;">Amount (INR)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incomes as $i => $income)
            @php
                $amt = (float) ($income->amount ?? 0);
                $relativePct = $listedHighest > 0 ? ($amt / $listedHighest) * 100 : 0;
            @endphp
            <tr class="avoid-break">
                <td class="text-slate">{{ $i + 1 }}</td>
                <td>
                    <strong class="text-slate-dark">{{ $income->source ?: 'Unspecified' }}</strong><br>
                    <span style="font-size: 6px; color: #94a3b8;" class="font-mono">INC-{{ str_pad($income->id, 5, '0', STR_PAD_LEFT) }}</span>
                </td>
                <td><span class="badge badge-slate">{{ $income->category ?: 'Uncategorized' }}</span></td>
                <td class="text-right text-slate font-mono">
                    {{ $income->income_date ? \Carbon\Carbon::parse($income->income_date)->format('d M Y') : 'N/A' }}
                </td>
                <td class="text-right">
                    <span class="font-bold text-emerald font-mono">+{{ $currency($amt) }}</span>
                    <div class="row-micro-bar">
                        <div class="row-micro-fill" style="width: {{ $relativePct }}%;"></div>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-slate" style="padding: 30px 0;">No income records found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="crypto-block avoid-break">
    <div class="crypto-title">END OF LEDGER - VERIFICATION</div>
    <strong>GENERATED:</strong> {{ $generationTime }}<br>
    <strong>RECORDS:</strong> {{ $count }}<br>
    <strong>TOTAL INCOME:</strong> {{ $currency($total) }}<br>
    <strong>SHA256:</strong> {{ $verificationHash }}<br><br>
    <span style="color: #475569;">Generated automatically by FinanceAI.</span>
</div>

</body>
</html>
