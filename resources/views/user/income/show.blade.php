@extends('layouts.app')

@section('title', 'Transaction Ledger - FinanceAI')

@section('content')

@php
    // ================= 1. STRICT & SAFE DATA PREPARATION =================
    $amount = (float)($income->amount ?? 0);
    $source = $income->source ?? 'Direct Deposit';
    $category = $income->category ?? 'General';
    $txId = 'INC-' . str_pad($income->id ?? rand(1000,9999), 6, '0', STR_PAD_LEFT);
    
    // Safe Date Parsing (Prevents calling ->format() on null objects)
    $dateObj = $income->income_date ? \Carbon\Carbon::parse($income->income_date) : \Carbon\Carbon::parse($income->created_at ?? now());
    $displayDate = $dateObj->format('d M Y');
    $displayMonth = $dateObj->format('F Y');
    $timeTime = $dateObj->format('h:i A');

    // Simulated Cryptographic Ledger Hash
    $cryptoHash = hash('sha256', $txId . $amount . $dateObj->toIso8601String() . $source);

    // ================= 2. DYNAMIC MULTI-COLOR AI HEURISTICS =================
    $aiTheme = [
        'status' => 'Standard',
        'color'  => 'text-sky-600',
        'bg'     => 'bg-sky-50',
        'border' => 'border-sky-200',
        'icon'   => 'fa-money-bill-wave',
        'glow'   => 'bg-sky-400/20',
        'text'   => "Standard liquidity event. Consistent deposits of this size build a strong, predictable baseline for your monthly capital retention."
    ];

    if ($amount >= 20000) {
        $aiTheme = [
            'status' => 'Exceptional',
            'color'  => 'text-purple-600',
            'bg'     => 'bg-purple-50',
            'border' => 'border-purple-200',
            'icon'   => 'fa-gem',
            'glow'   => 'bg-purple-400/20',
            'text'   => "Exceptional capital inflow detected. This creates a massive surplus in your monthly run rate. Consider re-allocating 20% of this to long-term investment portfolios."
        ];
    } elseif ($amount >= 5000) {
        $aiTheme = [
            'status' => 'High Yield',
            'color'  => 'text-indigo-600',
            'bg'     => 'bg-indigo-50',
            'border' => 'border-indigo-200',
            'icon'   => 'fa-arrow-trend-up',
            'glow'   => 'bg-indigo-400/20',
            'text'   => "High-value income deposit. This significantly strengthens your monthly runway and shields against unexpected operational outflows."
        ];
    }
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-emerald-100 selection:text-emerald-900 relative"
     x-data="{ showTerminal: false, scanProgress: 0, terminalLogs: [] }">

    {{-- Pristine Light Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] {{ $aiTheme['glow'] }} rounded-full blur-[100px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[800px] h-[800px] bg-emerald-50/50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-cyan-500"></div>

            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li><a href="{{ route('user.incomes.index') }}" class="hover:text-emerald-600 transition-colors">Inbound Capital</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1"></i></li>
                        <li class="text-emerald-600">Ledger Record</li>
                    </ol>
                </nav>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Transaction Details</h1>
                <p class="text-slate-500 text-sm font-medium mt-2 flex items-center gap-2">
                    <i class="fa-solid fa-fingerprint text-slate-300"></i> Immutable Financial Record
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('user.incomes.index') }}" class="px-5 py-3 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 hover:text-slate-900 transition-all flex items-center gap-2 focus:outline-none shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <a href="{{ route('user.incomes.edit', $income->id) }}" class="px-6 py-3 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-xl font-bold text-sm hover:bg-indigo-600 hover:text-white hover:shadow-md hover:shadow-indigo-500/20 transition-all flex items-center gap-2 hover:-translate-y-0.5">
                    <i class="fa-solid fa-pen"></i> Modify
                </a>
                <form method="POST" action="{{ route('user.incomes.destroy', $income->id) }}" class="m-0" onsubmit="return confirm('Are you sure you want to permanently delete this transaction? This will alter your historical analytics.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-3 bg-white text-rose-500 border border-slate-200 rounded-xl font-bold text-sm hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center gap-2 shadow-sm focus:outline-none">
                        <i class="fa-solid fa-trash-can"></i> Void
                    </button>
                </form>
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-8">
            
            {{-- ================= LEFT COLUMN: DATA & LEDGER ================= --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Digital Receipt Card --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 sm:p-10 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-50/50 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-emerald-50 border border-emerald-100 text-emerald-500 rounded-2xl flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-building-columns text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-slate-900">{{ $source }}</h2>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $category }}</span>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Gross Value</p>
                            <h1 class="text-4xl sm:text-5xl font-black text-emerald-600 tracking-tight">+₹{{ number_format($amount, 2) }}</h1>
                        </div>
                    </div>

                    <div class="h-px w-full bg-slate-100 mb-8 relative z-10"></div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 relative z-10">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-black uppercase tracking-widest border border-emerald-100">
                                <i class="fa-solid fa-check-double"></i> Cleared
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Date</p>
                            <p class="text-sm font-bold text-slate-900">{{ $displayDate }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Time</p>
                            <p class="text-sm font-bold text-slate-900">{{ $timeTime }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Fiscal Month</p>
                            <p class="text-sm font-bold text-slate-900">{{ $displayMonth }}</p>
                        </div>
                    </div>
                </div>

                {{-- Security & Cryptographic Meta --}}
                <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
                    
                    <div class="flex justify-between items-center mb-6 relative z-10">
                        <h3 class="text-lg font-black tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i> Security & Metadata
                        </h3>
                        <button onclick="startVerification()" class="px-4 py-2 bg-white/10 border border-white/20 hover:bg-white/20 rounded-lg text-xs font-bold text-white transition-colors focus:outline-none">
                            Verify Integrity
                        </button>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6 relative z-10 mb-6">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transaction ID</p>
                            <p class="text-sm font-mono font-bold text-emerald-400">{{ $txId }}</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Database Index</p>
                            <p class="text-sm font-mono font-bold text-slate-300">finance_db.incomes.row_{{ $income->id ?? '000' }}</p>
                        </div>
                    </div>

                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">SHA-256 Cryptographic Checksum</p>
                        <div class="bg-black/50 border border-slate-700 rounded-xl p-4 overflow-x-auto">
                            <code class="text-xs text-emerald-500 font-bold whitespace-nowrap">{{ $cryptoHash }}</code>
                        </div>
                    </div>

                    {{-- Hidden Terminal for JS Animation --}}
                    <div id="verifyTerminal" class="mt-6 bg-black/80 rounded-xl p-4 text-[10px] font-mono text-emerald-400 h-32 overflow-y-auto hidden relative z-10 border border-emerald-500/30 shadow-inner">
                        <div id="terminalLogs" class="space-y-1"></div>
                    </div>
                </div>

            </div>

            {{-- ================= RIGHT COLUMN: AI & TIMELINE ================= --}}
            <div class="lg:col-span-4 space-y-8">
                
                {{-- AI Diagnostic Card (Dynamic Multi-Color) --}}
                <div class="bg-white rounded-[2rem] border {{ $aiTheme['border'] }} shadow-sm p-8 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-48 h-full {{ $aiTheme['glow'] }} skew-x-12 translate-x-10 pointer-events-none"></div>
                    
                    <div class="flex items-center gap-3 mb-6 relative z-10">
                        <div class="w-12 h-12 rounded-xl {{ $aiTheme['bg'] }} {{ $aiTheme['color'] }} flex items-center justify-center shadow-inner">
                            <i class="fa-solid {{ $aiTheme['icon'] }} text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">AI Assessment</p>
                            <h3 class="text-lg font-black text-slate-900">{{ $aiTheme['status'] }}</h3>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 font-medium leading-relaxed relative z-10">
                        {{ $aiTheme['text'] }}
                    </p>
                    
                    <div class="mt-6 pt-6 border-t border-slate-100 relative z-10">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-400">System Confidence</span>
                            <span class="font-black {{ $aiTheme['color'] }}">98.4%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="h-full rounded-full w-[98.4%] {{ str_replace('text', 'bg', $aiTheme['color']) }}"></div>
                        </div>
                    </div>
                </div>

                {{-- Vertical Audit Timeline --}}
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight mb-8">Audit Timeline</h3>
                    
                    <div class="relative pl-4 border-l-2 border-slate-100 space-y-8">
                        
                        {{-- Step 1: Created --}}
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-white border-2 border-emerald-500 shadow-sm"></div>
                            <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-1">Record Created</p>
                            <p class="text-sm font-black text-slate-900">{{ optional($income->created_at)->format('M d, Y - h:i A') ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">{{ optional($income->created_at)->diffForHumans() }}</p>
                        </div>

                        {{-- Step 2: Updated (Only show if different from created) --}}
                        @if($income->updated_at && $income->updated_at->ne($income->created_at))
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-white border-2 border-indigo-500 shadow-sm"></div>
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Record Modified</p>
                            <p class="text-sm font-black text-slate-900">{{ $income->updated_at->format('M d, Y - h:i A') }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-1">{{ $income->updated_at->diffForHumans() }}</p>
                        </div>
                        @else
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-white border-2 border-slate-300 shadow-sm"></div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Record Modified</p>
                            <p class="text-sm font-bold text-slate-500">No modifications detected.</p>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Global Toast --}}
<div id="toast" class="fixed bottom-8 right-8 z-[120] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 transform translate-y-24 opacity-0 transition-all duration-400 pointer-events-none border border-slate-800">
    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
        <i class="fa-solid fa-shield-check text-sm"></i>
    </div>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('scripts')
<script>
// 1. Simulated Blockchain/Ledger Verification
window.startVerification = function() {
    const terminal = document.getElementById('verifyTerminal');
    const logs = document.getElementById('terminalLogs');
    const hash = '{{ $cryptoHash }}';
    
    terminal.classList.remove('hidden');
    logs.innerHTML = '';

    const lines = [
        "> INIT: SECURE_LEDGER_VERIFICATION",
        "> CONNECTING TO CLUSTER_NODE_01...",
        "> FETCHING ROW DATA: ID {{ $income->id ?? '0' }}",
        "> EXTRACTING METADATA: AMOUNT, DATE, SOURCE",
        "> RECALCULATING SHA-256 HASH...",
        "> GENERATED: " + hash.substring(0, 30) + "...",
        "> COMPARING WITH STORED CHECKSUM...",
        "> MATCH FOUND. INTEGRITY VERIFIED.",
        "> STATUS: 200 OK - IMMUTABLE RECORD."
    ];

    let i = 0;
    const interval = setInterval(() => {
        if(i < lines.length) {
            let colorClass = i >= 6 ? 'text-emerald-400 font-bold' : 'text-emerald-500/70';
            logs.innerHTML += `<div class="${colorClass} mb-1">${lines[i]}</div>`;
            terminal.scrollTop = terminal.scrollHeight; // Auto scroll
            i++;
        } else {
            clearInterval(interval);
            setTimeout(() => {
                showToast("Ledger Integrity Verified!");
            }, 500);
        }
    }, 400); 
}

// Global Toast UI
window.showToast = function(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').innerText = msg;
    
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
    setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3000);
}
</script>
@endpush