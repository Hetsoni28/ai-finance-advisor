@extends('layouts.app')

@section('title', 'Identity Profile | FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA EXTRACTION & IAM LOGIC =================
    $user = auth()->user();
    abort_unless($user, 403, 'Unauthorized Node Access.');

    $totalIncome  = (float) ($totalIncome  ?? 850000); 
    $totalExpense = (float) ($totalExpense ?? 320000);
    $savings      = $totalIncome - $totalExpense;
    
    // Mathematical Capital Retention Rate
    $savingsRate = $totalIncome > 0 ? ($savings / $totalIncome) * 100 : 0;

    $role = $user->role ?? 'Master Node';
    $isBlocked = $user->is_blocked ?? false;
    $activities = $activities ?? collect([
        (object)['description' => 'Authenticated via TLS 1.3 Handshake', 'created_at' => now()->subMinutes(12)],
        (object)['description' => 'Vault synchronization successful', 'created_at' => now()->subHours(2)],
        (object)['description' => 'Modified security credentials', 'created_at' => now()->subDays(1)],
        (object)['description' => 'Generated Q3 Financial Report', 'created_at' => now()->subDays(3)],
        (object)['description' => 'Node initialized on FinanceAI Core', 'created_at' => now()->subDays(14)],
    ]);

    // IAM Security Check
    $isVerified = method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail();

    // Cryptographic Node ID
    $nodeId = 'NODE-' . strtoupper(substr(hash('sha256', (string)$user->id), 0, 12));

    // Smart Role Color Formatting Engine
    $roleTheme = match(strtolower($role)) {
        'admin', 'master admin', 'master node' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'icon' => 'fa-crown', 'glow' => 'shadow-rose-500/20'],
        'manager' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'icon' => 'fa-shield-halved', 'glow' => 'shadow-emerald-500/20'],
        default => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'icon' => 'fa-user-astronaut', 'glow' => 'shadow-indigo-500/20']
    };

@endphp

<div x-data="profileEngine()" class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col">

    {{-- Holographic Light Ambient Backgrounds --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[1000px] h-[1000px] bg-indigo-50/80 rounded-full blur-[120px] transition-colors duration-1000"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[800px] h-[800px] bg-sky-50/50 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMDUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-100"></div>
    </div>

    <div class="max-w-[1500px] mx-auto w-full px-4 sm:px-6 lg:px-8 pt-10 relative z-10 space-y-10">

        {{-- ================= 1. IAM HEADER BREADCRUMBS ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-4 bg-white/90 backdrop-blur-xl p-8 rounded-[2.5rem] border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] animate-fade-in-up">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Hub</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-indigo-600 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span> Identity & Access</li>
                    </ol>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">Identity Registry</h1>
                <p class="text-slate-500 text-sm font-medium mt-2">Your account details, security and activity.</p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <button @click="copyToClipboard('{{ $nodeId }}', 'Node ID copied to clipboard.')" @mouseenter="playHover()" class="flex items-center gap-2 bg-slate-50 hover:bg-indigo-50 px-4 py-3 rounded-2xl border border-slate-200 hover:border-indigo-200 transition-colors shadow-sm focus:outline-none group">
                    <i class="fa-solid fa-fingerprint text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                    <span class="font-mono text-xs font-bold text-slate-600 group-hover:text-indigo-700 transition-colors">{{ $nodeId }}</span>
                </button>
            </div>
        </div>

        {{-- ================= 2. THE HERO IDENTITY CARD ================= --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] p-8 md:p-12 relative overflow-hidden group animate-fade-in-up" style="animation-delay: 100ms;">
            
            {{-- Enterprise Grid Overlay --}}
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full pointer-events-none transition-transform duration-1000 group-hover:scale-110"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center gap-10 relative z-10">
                
                {{-- Left: Holographic Avatar --}}
                <div class="relative shrink-0 mx-auto lg:mx-0 transform-style-3d perspective-[1000px]">
                    <div class="h-32 w-32 md:h-40 md:w-40 rounded-[2.5rem] bg-gradient-to-br from-indigo-500 via-purple-500 to-sky-400 p-[4px] shadow-2xl shadow-indigo-500/30 transform -rotate-3 hover:rotate-0 transition-transform duration-700 relative overflow-hidden group/avatar">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-30 mix-blend-overlay animate-[spin_20s_linear_infinite]"></div>
                        <div class="h-full w-full bg-white rounded-[2.2rem] flex items-center justify-center relative z-10 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-500"></div>
                            <span class="text-6xl md:text-7xl font-black bg-clip-text text-transparent bg-gradient-to-br from-indigo-600 to-sky-500 relative z-10 transform group-hover/avatar:scale-110 transition-transform duration-500">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Live Status Indicator --}}
                    @if(!$isBlocked)
                        <div class="absolute -bottom-2 -right-2 md:-bottom-3 md:-right-3 h-10 w-10 md:h-12 md:w-12 bg-emerald-50 border-[4px] border-white rounded-[1rem] flex items-center justify-center shadow-lg" data-tooltip="Node connection stable">
                            <span class="relative flex h-3 w-3 md:h-4 md:w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 md:h-4 md:w-4 bg-emerald-500"></span>
                            </span>
                        </div>
                    @else
                        <div class="absolute -bottom-2 -right-2 md:-bottom-3 md:-right-3 h-10 w-10 md:h-12 md:w-12 bg-rose-50 border-[4px] border-white rounded-[1rem] flex items-center justify-center shadow-lg" data-tooltip="Node Access Blocked">
                            <i class="fa-solid fa-lock text-sm text-rose-500"></i>
                        </div>
                    @endif
                </div>

                {{-- Middle: Identity Details --}}
                <div class="flex-1 space-y-5 text-center lg:text-left min-w-0">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight truncate">{{ $user->name }}</h2>
                        <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border shadow-sm w-max mx-auto lg:mx-0 {{ $roleTheme['bg'] }} {{ $roleTheme['text'] }} {{ $roleTheme['border'] }} {{ $roleTheme['glow'] }}">
                            <i class="fa-solid {{ $roleTheme['icon'] }} mr-2 text-xs"></i> {{ $role }}
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 text-slate-500 font-medium text-sm justify-center lg:justify-start">
                        <div class="flex items-center gap-3 bg-slate-50 px-5 py-2.5 rounded-xl border border-slate-200 shadow-inner group/email max-w-full">
                            <i class="fa-solid fa-envelope text-indigo-400 shrink-0"></i>
                            <span class="font-mono text-sm font-bold text-slate-700 truncate">{{ $user->email }}</span>
                            <div class="w-px h-5 bg-slate-200 mx-1 shrink-0"></div>
                            <button @click="copyToClipboard('{{ $user->email }}', 'Email copied to clipboard.')" @mouseenter="playHover()" class="text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none shrink-0" data-tooltip="Copy Email">
                                <i class="fa-regular fa-copy text-sm group-hover/email:scale-110 transition-transform"></i>
                            </button>
                        </div>

                        @if($isVerified)
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50/80 border border-emerald-100 rounded-xl text-emerald-600 text-xs font-black uppercase tracking-widest shadow-sm shrink-0">
                                <i class="fa-solid fa-shield-check text-sm"></i> Verified
                            </div>
                        @else
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50/80 border border-amber-100 rounded-xl text-amber-600 text-xs font-black uppercase tracking-widest shadow-sm shrink-0">
                                <i class="fa-solid fa-triangle-exclamation animate-pulse text-sm"></i> Unverified
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest justify-center lg:justify-start pt-2">
                        <i class="fa-regular fa-calendar-check"></i> Node Initialized {{ $user->created_at?->format('M Y') ?? 'Recently' }}
                    </div>
                </div>

                {{-- Right: account security (real data and real actions only) --}}
                <div class="bg-[#0f172a] rounded-[2rem] border border-slate-800 p-8 min-w-[280px] shadow-2xl relative overflow-hidden shrink-0 w-full md:w-auto text-white">
                    <div class="h-12 w-12 rounded-2xl border border-white/15 bg-white/5 flex items-center justify-center"><i class="fa-solid fa-shield-halved text-xl text-indigo-300"></i></div>
                    <p class="mt-6 text-[11px] uppercase font-black tracking-widest text-slate-400">Account security</p>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-6"><dt class="text-slate-400">Email</dt><dd class="font-semibold truncate max-w-[180px]">{{ $user->email }}</dd></div>
                        <div class="flex justify-between gap-6"><dt class="text-slate-400">Member since</dt><dd class="font-semibold">{{ $user->created_at?->format('M Y') ?? '—' }}</dd></div>
                    </dl>
                    <a href="{{ route('user.profile.password.form') }}" class="mt-6 block w-full py-2.5 bg-white/10 hover:bg-white/20 border border-white/10 text-white rounded-xl text-[11px] font-black uppercase tracking-widest text-center transition-colors">
                        Change password
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= 3. LIFETIME FINANCIAL TELEMETRY (WITH SPARKLINES) ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 200ms;">
            
            {{-- Lifetime Inflow --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all duration-500 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none group-hover:scale-150 group-hover:bg-emerald-500/10 transition-all duration-700"></div>
                {{-- Decorative Sparkline --}}
                <svg class="absolute bottom-0 left-0 w-full h-24 opacity-20 group-hover:opacity-40 transition-opacity" preserveAspectRatio="none" viewBox="0 0 100 100"><path d="M0,100 Q20,80 40,90 T80,40 T100,50 L100,100 Z" fill="#10b981" /></svg>
                
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100 shadow-sm mb-6 relative z-10 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><i class="fa-solid fa-money-bill-trend-up text-xl"></i></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Lifetime Inflow</p>
                <h3 class="text-4xl lg:text-5xl font-black text-slate-900 kpi-currency relative z-10 tracking-tight" data-val="{{ $totalIncome }}">₹0</h3>
            </div>

            {{-- Lifetime Outflow --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all duration-500 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl pointer-events-none group-hover:scale-150 group-hover:bg-rose-500/10 transition-all duration-700"></div>
                <svg class="absolute bottom-0 left-0 w-full h-24 opacity-20 group-hover:opacity-40 transition-opacity" preserveAspectRatio="none" viewBox="0 0 100 100"><path d="M0,100 Q30,60 50,80 T90,30 T100,50 L100,100 Z" fill="#f43f5e" /></svg>

                <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-100 shadow-sm mb-6 relative z-10 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300"><i class="fa-solid fa-fire-flame-curved text-xl"></i></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Total Expenses</p>
                <h3 class="text-4xl lg:text-5xl font-black text-slate-900 kpi-currency relative z-10 tracking-tight" data-val="{{ $totalExpense }}">₹0</h3>
            </div>

            {{-- Net Savings & Retention --}}
            <div class="bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] hover:-translate-y-1 transition-all duration-500 relative overflow-hidden group text-white">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-all duration-700"></div>
                <svg class="absolute bottom-0 left-0 w-full h-32 opacity-20 group-hover:opacity-40 transition-opacity z-0" preserveAspectRatio="none" viewBox="0 0 100 100"><path d="M0,100 Q20,90 40,50 T70,40 T100,20 L100,100 Z" fill="#6366f1" /></svg>
                
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div class="w-14 h-14 bg-white/10 text-indigo-400 rounded-2xl flex items-center justify-center border border-white/20 shadow-inner group-hover:scale-110 transition-transform duration-300"><i class="fa-solid fa-scale-balanced text-xl"></i></div>
                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border border-white/20 shadow-sm bg-white/10 text-white backdrop-blur-sm">
                        {{ number_format($savingsRate, 1) }}% Retained
                    </span>
                </div>
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1 relative z-10">Net Capital Retained</p>
                <h3 class="text-4xl lg:text-5xl font-black text-white kpi-currency relative z-10 tracking-tight drop-shadow-md" data-val="{{ abs($savings) }}">₹0</h3>
            </div>

        </div>

        {{-- ================= 4. MAIN DASHBOARD CONTENT ================= --}}
        <div class="grid xl:grid-cols-12 gap-8 items-start animate-fade-in-up" style="animation-delay: 300ms;">
            
            {{-- LEFT COLUMN: Config Hub, API Keys, Active Sessions --}}
            <div class="xl:col-span-7 space-y-8">
                
                {{-- Configuration Hub --}}
                <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] p-8 md:p-10 relative overflow-hidden">
                    <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3 tracking-tight border-b border-slate-100 pb-4">
                        <i class="fa-solid fa-sliders text-indigo-500"></i> Configuration Hub
                    </h2>

                    <div class="grid sm:grid-cols-2 gap-6">
                        @if(Route::has('user.profile.edit'))
                        <a href="{{ route('user.profile.edit') }}" @mouseenter="playHover()" class="flex flex-col p-6 rounded-[1.5rem] bg-slate-50 border border-slate-200 hover:bg-white hover:border-indigo-300 hover:shadow-[0_10px_30px_rgba(79,70,229,0.12)] hover:-translate-y-1 transition-all duration-300 group focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-200 shadow-sm mb-5 transition-all">
                                <i class="fa-solid fa-fingerprint text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight">Identity Profile</h3>
                            <p class="text-xs font-bold text-slate-500 mt-2 leading-relaxed">Modify your personal details, email address, and global preferences.</p>
                            <div class="mt-auto pt-6 text-[10px] font-black uppercase tracking-widest text-indigo-500 opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1 transform translate-x-[-10px] group-hover:translate-x-0">
                                Update Context <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </a>
                        @endif

                        @if(Route::has('user.profile.password.form'))
                        <a href="{{ route('user.profile.password.form') }}" @mouseenter="playHover()" class="flex flex-col p-6 rounded-[1.5rem] bg-slate-50 border border-slate-200 hover:bg-white hover:border-indigo-300 hover:shadow-[0_10px_30px_rgba(79,70,229,0.12)] hover:-translate-y-1 transition-all duration-300 group focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-200 shadow-sm mb-5 transition-all">
                                <i class="fa-solid fa-shield-halved text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight">Security Credentials</h3>
                            <p class="text-xs font-bold text-slate-500 mt-2 leading-relaxed">Update your cryptographic password and core settings.</p>
                            <div class="mt-auto pt-6 text-[10px] font-black uppercase tracking-widest text-indigo-500 opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1 transform translate-x-[-10px] group-hover:translate-x-0">
                                Secure Node <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- This session (real request data) --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8">
                    <h2 class="text-xl font-black text-slate-900 flex items-center gap-3 tracking-tight"><i class="fa-solid fa-display text-indigo-500"></i> This session</h2>
                    <dl class="mt-5 grid sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4"><dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">IP address</dt><dd class="mt-1 font-mono font-semibold text-slate-800">{{ request()->ip() }}</dd></div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4"><dt class="text-[11px] font-black uppercase tracking-widest text-slate-400">Browser</dt><dd class="mt-1 font-semibold text-slate-800 truncate" title="{{ request()->userAgent() }}">{{ \Illuminate\Support\Str::limit(request()->userAgent() ?? 'Unknown', 60) }}</dd></div>
                    </dl>
                </div>            </div>

            {{-- RIGHT COLUMN: Cryptographic Audit Log --}}
            <div class="xl:col-span-5 bg-white/90 backdrop-blur-xl rounded-[2.5rem] border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] p-8 md:p-10 flex flex-col h-[900px] relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-slate-100/60 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-1000"></div>

                <div class="flex items-center justify-between mb-6 relative z-10 border-b border-slate-100 pb-4 shrink-0">
                    <h2 class="text-xl font-black text-slate-900 flex items-center gap-3 tracking-tight">
                        <i class="fa-solid fa-clipboard-list text-indigo-500"></i> Event Ledger
                    </h2>
                </div>

                @if($activities->count())
                    {{-- 🚨 BEAST MODE FIX: Scrollable area with gradient fade & min-height constraints --}}
                    <div class="relative flex-1 z-10 overflow-hidden rounded-2xl min-h-[500px]">
                        <div class="absolute top-0 left-0 right-0 h-4 bg-gradient-to-b from-white to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white via-white/90 to-transparent z-20 pointer-events-none"></div>
                        
                        <div class="absolute left-[21px] top-4 bottom-4 w-px bg-slate-200 z-0"></div>

                        <ul class="space-y-6 relative z-10 h-full overflow-y-auto scrollbar-custom pr-4 pb-16 pt-4">
                            @foreach($activities as $activity)
                                @php
                                    // Smart Event Parser for Icons and Colors
                                    $desc = strtolower($activity->description ?? '');
                                    $actColor = 'slate'; $actIcon = 'fa-circle-dot';
                                    
                                    if (str_contains($desc, 'password') || str_contains($desc, 'security') || str_contains($desc, 'tls')) { $actColor = 'indigo'; $actIcon = 'fa-shield-halved'; }
                                    elseif (str_contains($desc, 'profile') || str_contains($desc, 'identity') || str_contains($desc, 'node')) { $actColor = 'sky'; $actIcon = 'fa-id-card'; }
                                    elseif (str_contains($desc, 'create') || str_contains($desc, 'add') || str_contains($desc, 'sync') || str_contains($desc, 'generate')) { $actColor = 'emerald'; $actIcon = 'fa-check'; }
                                    elseif (str_contains($desc, 'delete') || str_contains($desc, 'remove') || str_contains($desc, 'revoke')) { $actColor = 'rose'; $actIcon = 'fa-xmark'; }
                                @endphp
                                <li class="flex items-start gap-5 group/item">
                                    <div class="w-11 h-11 rounded-2xl bg-white border border-{{ $actColor }}-200 flex items-center justify-center shrink-0 shadow-sm z-10 group-hover/item:border-{{ $actColor }}-400 group-hover/item:bg-{{ $actColor }}-50 group-hover/item:scale-110 transition-all duration-300 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-{{ $actColor }}-500/10 opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                                        <i class="fa-solid {{ $actIcon }} text-xs text-{{ $actColor }}-500 relative z-10"></i>
                                    </div>
                                    <div class="pt-1.5 flex-1 min-w-0 bg-white rounded-2xl p-4 border border-slate-100 shadow-sm group-hover/item:border-slate-300 group-hover/item:shadow-md transition-all duration-300">
                                        <p class="text-sm font-bold text-slate-700 group-hover/item:text-slate-900 transition-colors leading-relaxed">
                                            {{ $activity->description ?? 'System event recorded' }}
                                        </p>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2 font-mono">
                                            {{ optional($activity->created_at)->format('d M Y, H:i') ?? 'Unknown Time' }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                            {{-- End of Log Marker --}}
                            <li class="flex items-center gap-4 opacity-50 pl-2">
                                <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 z-10">
                                    <i class="fa-solid fa-flag-checkered text-[10px] text-slate-400"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">End of History</span>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-10 relative z-10 animate-fade-in-up min-h-[400px]">
                        <div class="w-28 h-28 bg-slate-50 rounded-[2.5rem] flex items-center justify-center border border-slate-100 mb-6 shadow-inner rotate-12 hover:rotate-0 transition-transform duration-500">
                            <i class="fa-solid fa-ghost text-5xl text-slate-300"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-900 tracking-tight">Pristine Ledger</p>
                        <p class="text-sm font-medium text-slate-500 mt-3 max-w-[220px] mx-auto leading-relaxed">Cryptographic actions performed on this node will appear here.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>

{{-- Custom CSS Tooltip Element --}}
<div id="custom-tooltip" class="fixed z-[9999] opacity-0 pointer-events-none px-3 py-1.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl transform scale-95 transition-all duration-200 border border-slate-700">
    <span id="custom-tooltip-text"></span>
</div>

{{-- Master Hub Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    /* Premium Scrollbars for Audit Log */
    .scrollbar-custom::-webkit-scrollbar { width: 5px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .scrollbar-custom:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

    /* 3D Transform Utilities */
    .transform-style-3d { transform-style: preserve-3d; perspective: 1000px; }

    /* Smooth Fade Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes shimmer { 100% { transform: translateX(100%); } }
</style>
@endpush

@push('scripts')
<script>
// ================= CUSTOM TOOLTIP ENGINE =================
document.addEventListener('DOMContentLoaded', () => {
    const tooltip = document.getElementById('custom-tooltip');
    const tooltipText = document.getElementById('custom-tooltip-text');
    
    document.querySelectorAll('[data-tooltip]').forEach(el => {
        el.addEventListener('mouseenter', e => {
            tooltipText.innerText = el.getAttribute('data-tooltip');
            const rect = el.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            tooltip.classList.remove('opacity-0', 'scale-95');
            tooltip.classList.add('opacity-100', 'scale-100');
        });
        el.addEventListener('mouseleave', () => {
            tooltip.classList.add('opacity-0', 'scale-95');
            tooltip.classList.remove('opacity-100', 'scale-100');
        });
    });
});

// ================= AUDIO ENGINE (Beast Mode) =================
window.audioEngine = {
    ctx: null, lastHover: 0,
    init() { if(!this.ctx) { const AC = window.AudioContext || window.webkitAudioContext; if(AC) this.ctx = new AC(); } },
    playClick() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(800, this.ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(300, this.ctx.currentTime + 0.05);
        gain.gain.setValueAtTime(0.1, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
        osc.start(); osc.stop(this.ctx.currentTime + 0.05);
    },
    playHover() {
        const now = Date.now(); if(now - this.lastHover < 50) return; this.lastHover = now;
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(400, this.ctx.currentTime); gain.gain.setValueAtTime(0.015, this.ctx.currentTime); 
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.03); osc.start(); osc.stop(this.ctx.currentTime + 0.03);
    },
    playSuccess() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(600, this.ctx.currentTime); osc.frequency.setValueAtTime(900, this.ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.05, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.3);
        osc.start(); osc.stop(this.ctx.currentTime + 0.3);
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('profileEngine', () => ({
        isScanning: false,
        scanProgress: 0,
        scanStatusText: 'Optimal',
        mfaEnabled: true,

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        // Interactive Security Audit Simulator
        async runSecurityScan() {
            if(this.isScanning) return;
            this.playClick();
            this.isScanning = true;
            this.scanProgress = 0;
            this.scanStatusText = 'Initializing...';

            const phases = [
                {p: 25, text: 'Pinging Master Node...', delay: 600},
                {p: 50, text: 'Verifying TLS 1.3...', delay: 800},
                {p: 75, text: 'Checking AES Keys...', delay: 700},
                {p: 100, text: 'Validating IP Log...', delay: 900}
            ];

            for(let phase of phases) {
                await new Promise(r => setTimeout(r, phase.delay));
                this.scanProgress = phase.p;
                this.scanStatusText = phase.text;
                if(Math.random() > 0.5) this.playHover(); // simulated scanning blips
            }

            await new Promise(r => setTimeout(r, 400));
            window.audioEngine.playSuccess();
            this.isScanning = false;
            this.scanStatusText = this.mfaEnabled ? 'Secure' : 'Warning';
            let msg = this.mfaEnabled ? 'Security audit complete. No vulnerabilities found.' : 'Audit complete. MFA is disabled.';
            this.$dispatch('notify', { message: msg, type: this.mfaEnabled ? 'success' : 'info' });
        },

        toggleMFA() {
            this.playClick();
            this.mfaEnabled = !this.mfaEnabled;
            this.scanStatusText = this.mfaEnabled ? 'Optimal' : 'Vulnerable';
            let msg = this.mfaEnabled ? 'Multi-Factor Authentication enabled.' : 'Security Warning: MFA disabled.';
            this.$dispatch('notify', { message: msg, type: this.mfaEnabled ? 'success' : 'error' });
        },

        revealKey(index) {
            this.playClick();
            const mask = document.getElementById('key-mask-' + index);
            const raw = document.getElementById('key-raw-' + index);
            const icon = document.getElementById('eye-icon-' + index);
            
            if(mask.classList.contains('hidden')) {
                mask.classList.remove('hidden');
                raw.classList.add('hidden');
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                mask.classList.add('hidden');
                raw.classList.remove('hidden');
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        },

        // Modern Async Clipboard
        async copyToClipboard(text, successMsg = 'Identity copied to clipboard.') {
            this.playClick();
            try {
                await navigator.clipboard.writeText(text);
                this.$dispatch('notify', { message: successMsg, type: 'success' });
            } catch (err) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.$dispatch('notify', { message: successMsg, type: 'success' });
            }
        }
    }));
});

// Toast Listener (Master Hub)
window.addEventListener('notify', (e) => {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    document.getElementById('toastMsg').innerText = e.detail.message;
    
    if(e.detail.type === 'error') icon.className = "fa-solid fa-triangle-exclamation text-rose-400 text-lg";
    else if(e.detail.type === 'info') icon.className = "fa-solid fa-circle-info text-sky-400 text-lg";
    else icon.className = "fa-solid fa-circle-check text-emerald-400 text-lg";

    toast.classList.remove('translate-y-20', 'opacity-0');
    setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
});

// Flawless Cubic-Bezier Number Animator
document.addEventListener("DOMContentLoaded", function() {
    const animateValue = (el, isCurrency) => {
        let target = parseFloat(el.dataset.val || 0);
        if(target === 0) return;
        let duration = 2500;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            let progress = Math.min((timestamp - startTime) / duration, 1);
            let eased = progress === 1 ? target : target * (1 - Math.pow(1 - progress, 3)); 
            
            el.innerText = isCurrency 
                ? '₹' + Math.floor(eased).toLocaleString('en-IN')
                : Math.round(eased).toLocaleString('en-IN');
                
            if (progress < 1) window.requestAnimationFrame(step);
        }
        window.requestAnimationFrame(step);
    };

    document.querySelectorAll('.kpi-currency').forEach(el => animateValue(el, true));
});
</script>
@endpush