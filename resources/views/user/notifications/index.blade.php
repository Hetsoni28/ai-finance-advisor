@extends('layouts.app')

@section('title', 'Command Center - FinanceAI')

@section('content')

@php
    // ================= 1. SAFE DATA PREPARATION =================
    $notifications = $notifications ?? collect();
    $totalIncome   = (float) ($totalIncome ?? 450000);
    $totalExpense  = (float) ($totalExpense ?? 125000);

    $canMarkRead = Route::has('user.notifications.read');

    // Safe extraction for current page metrics
    $currentPageItems = method_exists($notifications, 'items') ? collect($notifications->items()) : collect($notifications);
    $unreadCount = $currentPageItems->filter(fn($n) => !($n->is_read ?? false))->count();
    $totalCount  = $currentPageItems->count();
    $unreadPercent = $totalCount > 0 ? round(($unreadCount / $totalCount) * 100) : 0;

    // Multi-Color Theme Mapping for Notification Types
    $themeMap = [
        'danger'  => ['cat' => 'security', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'icon' => 'fa-shield-virus'],
        'warning' => ['cat' => 'system', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'fa-triangle-exclamation'],
        'success' => ['cat' => 'finance', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'fa-money-bill-trend-up'],
        'info'    => ['cat' => 'system', 'color' => 'text-sky-600', 'bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'icon' => 'fa-circle-info'],
        'system'  => ['cat' => 'system', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'icon' => 'fa-server'],
    ];

    // Category Counters (Current Page)
    $catCounts = [
        'security' => $currentPageItems->filter(fn($n) => in_array(strtolower($n->type ?? ''), ['danger']))->count(),
        'finance'  => $currentPageItems->filter(fn($n) => in_array(strtolower($n->type ?? ''), ['success']))->count(),
        'system'   => $currentPageItems->filter(fn($n) => in_array(strtolower($n->type ?? ''), ['warning', 'info', 'system']))->count(),
    ];
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-32 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative"
     x-data="inboxController()"
     @keydown.window.prevent.slash="$refs.searchInput.focus()">

    {{-- Pristine Light Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-white/40">
        <div class="absolute top-[-10%] left-[-5%] w-[1000px] h-[1000px] bg-indigo-50/80 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[800px] h-[800px] bg-sky-50/50 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuNSIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc+')] opacity-40"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10 space-y-8">

        {{-- ================= 1. COMMAND HEADER & RADAR ================= --}}
        <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 bg-white/95 backdrop-blur-xl p-8 md:p-10 rounded-[2.5rem] border border-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden group">
            
            {{-- Accent Line --}}
            <div class="absolute top-0 left-0 w-2 h-full bg-gradient-to-b from-indigo-500 to-sky-400"></div>
            
            {{-- Animated Radar Background --}}
            <div class="absolute -right-20 top-1/2 -translate-y-1/2 w-96 h-96 border border-slate-100/50 rounded-full flex items-center justify-center opacity-50 pointer-events-none overflow-hidden">
                <div class="w-64 h-64 border border-slate-100/50 rounded-full flex items-center justify-center">
                    <div class="w-32 h-32 border border-slate-100/50 rounded-full"></div>
                </div>
                <div class="absolute inset-0 origin-center animate-[spin_3s_linear_infinite]" style="background: conic-gradient(from 0deg, transparent 70%, rgba(79,70,229,0.08) 100%);"></div>
            </div>

            <div class="relative z-10 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <li>FinanceAI Engine</li>
                        <li><i class="fa-solid fa-chevron-right text-[8px] mx-1 opacity-50"></i></li>
                        <li class="text-indigo-600 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span> Command Center
                        </li>
                    </ol>
                </nav>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-3">
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">System Inbox</h1>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg shadow-sm w-max mt-2 sm:mt-0">
                        <div class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Network Optimal</span>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2 max-w-xl leading-relaxed">
                    Encrypted event logging and financial alert routing. Review your telemetry below.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
                {{-- Live Polling Toggle --}}
                <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm mr-2 hidden sm:flex">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest" :class="isPolling ? 'text-indigo-600' : ''">Auto-Sync</span>
                        <span class="text-[8px] font-bold text-slate-400" x-text="isPolling ? 'Active' : 'Paused'"></span>
                    </div>
                    <button @click="togglePolling()" @mouseenter="playHover()" class="w-10 h-5 rounded-full relative transition-colors focus:outline-none shadow-inner border border-slate-200" :class="isPolling ? 'bg-indigo-500 border-indigo-600' : 'bg-slate-300'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-300" :class="isPolling ? 'translate-x-5' : 'translate-x-0'"></div>
                    </button>
                </div>

                <button @click="simulateLiveAlert()" @mouseenter="playHover()" class="px-5 py-3.5 bg-white text-indigo-600 border border-slate-200 rounded-2xl font-bold text-sm hover:bg-indigo-50 hover:border-indigo-300 hover:-translate-y-0.5 transition-all flex items-center gap-2 focus:outline-none shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-satellite-dish animate-pulse"></i> Inject Test
                </button>
                
                <button @click="markAllReadBatch()" @mouseenter="playHover()" id="btnMarkAllRead" class="px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-[0_10px_20px_-5px_rgba(15,23,42,0.4)] hover:bg-indigo-600 hover:shadow-[0_10px_20px_-5px_rgba(79,70,229,0.4)] transition-all flex items-center gap-2 hover:-translate-y-0.5 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-check-double"></i> <span>Acknowledge All</span>
                </button>
            </div>
        </div>

        {{-- ================= 2. KPI GRID ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 perspective-[1500px]">
            
            {{-- Global Inflow --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group transform-style-3d">
                <div class="translate-z-20">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Global Inflow</p>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 tabular-nums tracking-tight"><span class="text-emerald-500">₹</span>{{ number_format($totalIncome, 0) }}</h2>
                </div>
                <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center border border-emerald-100 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform translate-z-30"><i class="fa-solid fa-arrow-trend-up text-xl"></i></div>
            </div>

            {{-- Global Outflow --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group transform-style-3d">
                <div class="translate-z-20">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Global Outflow</p>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 tabular-nums tracking-tight"><span class="text-rose-500">₹</span>{{ number_format($totalExpense, 0) }}</h2>
                </div>
                <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center border border-rose-100 shadow-sm group-hover:scale-110 group-hover:-rotate-6 transition-transform translate-z-30"><i class="fa-solid fa-arrow-trend-down text-xl"></i></div>
            </div>

            {{-- Unread Alerts (Hero Card) --}}
            <div class="bg-slate-900 p-6 rounded-[2rem] border border-slate-800 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group relative overflow-hidden text-white transform-style-3d">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/30 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
                
                <div class="relative z-10 translate-z-20">
                    <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">Unread Alerts</p>
                    <h2 class="text-3xl md:text-4xl font-black text-white flex items-baseline gap-2 tabular-nums tracking-tight">
                        <span id="unreadCountText" class="animate-number" x-text="totalUnread">{{ $unreadCount }}</span> 
                        <span class="text-sm font-bold text-slate-500 mb-1">/ <span x-text="totalCount">{{ $totalCount }}</span></span>
                    </h2>
                </div>
                <div class="relative z-10 w-14 h-14 bg-white/10 text-indigo-400 rounded-[1rem] flex items-center justify-center border border-white/20 shadow-inner group-hover:scale-110 transition-transform translate-z-30">
                    <i class="fa-regular fa-bell text-2xl" :class="totalUnread > 0 ? 'animate-swing' : ''"></i>
                </div>
            </div>

            {{-- Event Velocity --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-[0_15px_30px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group relative overflow-hidden transform-style-3d">
                <div class="relative z-10 translate-z-20">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Event Velocity</p>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 tabular-nums tracking-tight"><span x-text="velocity">14</span><span class="text-sm font-bold text-slate-400">/min</span></h2>
                </div>
                
                <div class="flex items-end gap-1.5 h-10 relative z-10 opacity-60 group-hover:opacity-100 transition-opacity translate-z-30">
                    <div class="w-1.5 bg-sky-400 rounded-full animate-waveform shadow-sm" style="height: 40%; animation-delay: 0.0s"></div>
                    <div class="w-1.5 bg-indigo-400 rounded-full animate-waveform shadow-sm" style="height: 80%; animation-delay: 0.2s"></div>
                    <div class="w-1.5 bg-indigo-600 rounded-full animate-waveform shadow-sm" style="height: 100%; animation-delay: 0.4s"></div>
                    <div class="w-1.5 bg-indigo-400 rounded-full animate-waveform shadow-sm" style="height: 60%; animation-delay: 0.6s"></div>
                    <div class="w-1.5 bg-sky-400 rounded-full animate-waveform shadow-sm" style="height: 30%; animation-delay: 0.8s"></div>
                </div>
            </div>
        </div>

        {{-- ================= 3. SAAS INBOX LAYOUT ================= --}}
        <div class="grid lg:grid-cols-12 gap-8 items-start">
            
            {{-- ================= LEFT: INBOX NAVIGATION & FILTERS ================= --}}
            <div class="lg:col-span-3 space-y-6">
                
                {{-- Routing Card --}}
                <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] border border-white shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-7 sticky top-28">
                    
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 pl-2 flex items-center gap-2">
                        <i class="fa-solid fa-route text-slate-300"></i> Inbox Routing
                    </h3>
                    
                    <ul class="space-y-2">
                        <li>
                            <button @click="filter = 'all'; playClick()" @mouseenter="playHover()" :class="filter === 'all' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-sm ring-1 ring-indigo-500/10' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 border border-slate-100/0 hover:border-slate-200'" class="w-full flex items-center justify-between px-4 py-3.5 rounded-[1.25rem] font-bold text-sm transition-all focus:outline-none group">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-inbox w-4 text-center transition-transform group-hover:-translate-y-0.5"></i> All Events</span>
                                <span class="bg-white px-2 py-0.5 rounded-lg shadow-sm border border-slate-200 text-xs font-black tabular-nums" x-text="totalCount">{{ $totalCount }}</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'unread'; playClick()" @mouseenter="playHover()" :class="filter === 'unread' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-sm ring-1 ring-indigo-500/10' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 border border-slate-100/0 hover:border-slate-200'" class="w-full flex items-center justify-between px-4 py-3.5 rounded-[1.25rem] font-bold text-sm transition-all focus:outline-none group">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-circle-dot w-4 text-center text-indigo-500 transition-transform group-hover:scale-110"></i> Unread</span>
                                <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm text-xs font-black tabular-nums transition-transform" :class="totalUnread > 0 ? 'scale-100' : 'scale-90 opacity-50'" x-text="totalUnread">{{ $unreadCount }}</span>
                            </button>
                        </li>
                    </ul>

                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-8 mb-4 pl-2 flex items-center gap-2">
                        <i class="fa-solid fa-tags text-slate-300"></i> Smart Categories
                    </h3>
                    
                    <ul class="space-y-2">
                        <li>
                            <button @click="filter = 'security'; playClick()" @mouseenter="playHover()" :class="filter === 'security' ? 'bg-rose-50 text-rose-700 border-rose-200 shadow-sm ring-1 ring-rose-500/10' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 border border-slate-100/0 hover:border-slate-200'" class="w-full flex items-center justify-between px-4 py-3.5 rounded-[1.25rem] font-bold text-sm transition-all focus:outline-none group">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-shield-halved w-4 text-center text-rose-500 transition-transform group-hover:rotate-12"></i> Security</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'finance'; playClick()" @mouseenter="playHover()" :class="filter === 'finance' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-sm ring-1 ring-emerald-500/10' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 border border-slate-100/0 hover:border-slate-200'" class="w-full flex items-center justify-between px-4 py-3.5 rounded-[1.25rem] font-bold text-sm transition-all focus:outline-none group">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-vault w-4 text-center text-emerald-500 transition-transform group-hover:rotate-12"></i> Financial</span>
                            </button>
                        </li>
                        <li>
                            <button @click="filter = 'system'; playClick()" @mouseenter="playHover()" :class="filter === 'system' ? 'bg-sky-50 text-sky-700 border-sky-200 shadow-sm ring-1 ring-sky-500/10' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 border border-slate-100/0 hover:border-slate-200'" class="w-full flex items-center justify-between px-4 py-3.5 rounded-[1.25rem] font-bold text-sm transition-all focus:outline-none group">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-server w-4 text-center text-sky-500 transition-transform group-hover:rotate-12"></i> System</span>
                            </button>
                        </li>
                    </ul>

                    {{-- NEW FUN: System Telemetry Node --}}
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <div class="bg-slate-900 rounded-2xl p-5 shadow-inner relative overflow-hidden group">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                            <div class="absolute -right-5 -top-5 w-20 h-20 bg-emerald-500/20 rounded-full blur-xl group-hover:bg-emerald-500/40 transition-colors"></div>
                            
                            <h4 class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-3 flex items-center gap-2 relative z-10">
                                <i class="fa-solid fa-satellite-dish animate-pulse"></i> Node Telemetry
                            </h4>
                            
                            <div class="space-y-2.5 relative z-10">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-bold">Uptime</span>
                                    <span class="text-white font-mono" x-text="uptime">00:00:00</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-bold">DB Ping</span>
                                    <span class="text-emerald-400 font-mono" x-text="dbPing + 'ms'">12ms</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-bold">Encryption</span>
                                    <span class="text-white font-mono text-[9px] bg-white/10 px-1.5 py-0.5 rounded border border-white/20">AES-256-GCM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ================= RIGHT: NOTIFICATION FEED & PAYLOAD INSPECTOR ================= --}}
            <div class="lg:col-span-9 bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-white overflow-hidden flex flex-col min-h-[700px] relative">
                
                {{-- Feed Toolbar --}}
                <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 z-20">
                    <div class="relative w-full sm:w-[400px] group">
                        <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Search event logs (Press '/' to focus)..." 
                               class="w-full pl-12 pr-12 py-4 bg-white border border-slate-200 rounded-[1.25rem] text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all shadow-sm placeholder:font-medium placeholder:text-slate-400">
                        <kbd class="absolute right-4 top-1/2 -translate-y-1/2 px-2 py-0.5 bg-slate-100 border border-slate-200 rounded text-[10px] font-mono text-slate-400 font-bold hidden sm:block shadow-sm">/</kbd>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-list-check"></i>
                            Showing <span x-text="visibleCount" class="text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded"></span> Records
                        </div>
                    </div>
                </div>

                {{-- Feed Container --}}
                <div class="flex-1 overflow-y-auto bg-slate-50/30 p-4 sm:p-8 space-y-4 relative z-10 scrollbar-custom" id="notificationFeed">
                    
                    @forelse($currentPageItems as $note)
                        @php
                            $rawType = strtolower($note->type ?? 'info');
                            $priority = strtolower($note->priority ?? 'low');
                            $isRead = $note->is_read ?? false;
                            $message = $note->message ?? 'System Notification';
                            $time = isset($note->created_at) ? \Carbon\Carbon::parse($note->created_at)->diffForHumans() : 'Just now';
                            $fullTime = isset($note->created_at) ? \Carbon\Carbon::parse($note->created_at)->format('Y-m-d H:i:s T') : now()->format('Y-m-d H:i:s T');
                            
                            $theme = $themeMap[$rawType] ?? $themeMap['info'];
                            $category = $theme['cat']; 
                            
                            // Mock Metadata for the Payload expansion drawer
                            $mockIp = '192.168.' . rand(1, 255) . '.' . rand(1, 255);
                            $mockId = 'EVT-' . strtoupper(substr(md5($note->id ?? rand()), 0, 12));
                            $mockPayload = json_encode([
                                "event_id" => $mockId,
                                "timestamp" => $fullTime,
                                "source_ip" => $mockIp,
                                "level" => strtoupper($priority),
                                "category" => strtoupper($category),
                                "message" => $message,
                                "metadata" => [
                                    "node" => "aws-ap-south-1a",
                                    "latency_ms" => rand(12, 45),
                                    "encrypted" => true,
                                    "status_code" => 200
                                ]
                            ], JSON_PRETTY_PRINT);
                        @endphp

                        <div x-data="{ expanded: false, isVisible: true, isResolving: false }"
                             x-show="isVisible && matchesSearch('{{ addslashes($message) }}') && matchesFilter({{ $isRead ? 'true' : 'false' }}, '{{ $category }}')" 
                             x-init="$watch('search', () => updateVisibleCount()); $watch('filter', () => updateVisibleCount());"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 -translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100 h-[auto]" x-transition:leave-end="opacity-0 scale-95 h-0 overflow-hidden mb-0 pb-0 border-0"
                             id="note-{{ $note->id }}"
                             class="notification-card group bg-white border rounded-[1.5rem] p-1.5 shadow-sm transition-all duration-300 {{ !$isRead ? 'border-indigo-300 ring-4 ring-indigo-50/50 unread-card' : 'border-slate-200 hover:border-slate-300' }}"
                             :class="isResolving ? 'ring-0 border-emerald-200 opacity-50 bg-emerald-50/20' : ''">
                            
                            {{-- Main Clickable Area --}}
                            <div @click="expanded = !expanded; playClick()" @mouseenter="playHover()" class="p-4 sm:p-5 flex flex-col sm:flex-row gap-5 items-start cursor-pointer rounded-[1.2rem] hover:bg-slate-50 transition-colors relative overflow-hidden">
                                
                                {{-- Subtle Active Background Glow --}}
                                <div class="absolute inset-0 bg-indigo-50/50 opacity-0 transition-opacity duration-300 pointer-events-none" :class="expanded ? 'opacity-100' : ''"></div>

                                {{-- Icon Module --}}
                                <div class="shrink-0 relative z-10">
                                    <div class="w-12 h-12 rounded-[1rem] {{ $theme['bg'] }} {{ $theme['color'] }} border {{ $theme['border'] }} flex items-center justify-center text-lg shadow-sm transition-transform duration-300" :class="expanded ? 'scale-110' : 'group-hover:scale-110'">
                                        <i class="fa-solid {{ $theme['icon'] }}"></i>
                                    </div>
                                    {{-- Unread Pulse Dot --}}
                                    <div id="unread-dot-{{ $note->id }}" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-rose-500 border-[3px] border-white rounded-full shadow-sm transition-transform duration-500 {{ $isRead ? 'scale-0' : 'scale-100 animate-pulse' }}"></div>
                                </div>

                                {{-- Content Module --}}
                                <div class="flex-1 w-full relative z-10">
                                    <div class="flex flex-col sm:flex-row justify-between items-start gap-2 mb-1.5">
                                        <h3 class="text-base font-bold text-slate-900 leading-snug pr-4 transition-colors" :class="expanded ? 'text-indigo-700' : ''">{{ $message }}</h3>
                                        <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap uppercase tracking-widest flex items-center gap-1.5"><i class="fa-regular fa-clock"></i> {{ $time }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest {{ $theme['bg'] }} {{ $theme['color'] }} border {{ $theme['border'] }}">
                                            {{ ucfirst($rawType) }}
                                        </span>
                                        @if($priority === 'high')
                                            <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200 shadow-sm">
                                                <i class="fa-solid fa-bolt text-rose-500 mr-0.5"></i> Urgent
                                            </span>
                                        @endif
                                        <span class="text-[9px] font-bold text-slate-400 font-mono ml-2 opacity-0 group-hover:opacity-100 transition-opacity hidden sm:inline-block bg-slate-100 px-2 py-0.5 rounded">ID: {{ substr($mockId, 0, 12) }}...</span>
                                    </div>
                                </div>
                                
                                {{-- Expand Chevron --}}
                                <div class="shrink-0 flex items-center gap-3 relative z-10 mt-2 sm:mt-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 bg-white border border-slate-200 shadow-sm transition-all duration-300" :class="expanded ? 'bg-indigo-50 border-indigo-200 text-indigo-600 rotate-180' : 'group-hover:bg-slate-100'">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Expanded Focus Drawer (Master-Detail UI) --}}
                            <div x-show="expanded" x-collapse.duration.300ms>
                                <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-[1.2rem] mt-1 flex flex-col lg:flex-row gap-6">
                                    
                                    {{-- Left: SYNTAX HIGHLIGHTED JSON PAYLOAD --}}
                                    <div class="flex-1 bg-[#0f172a] rounded-[1.25rem] border border-slate-700 shadow-[inset_0_2px_10px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col">
                                        <div class="bg-[#1e293b] px-5 py-3 border-b border-slate-700 flex justify-between items-center">
                                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest font-mono flex items-center gap-2">
                                                <div class="flex gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div></div>
                                                <span class="ml-2">Event Payload</span>
                                            </span>
                                            <button @click="copyToClipboard(`{{ str_replace('"', '\"', $mockPayload) }}`, 'Payload copied')" @mouseenter="playHover()" class="text-slate-400 hover:text-white transition-colors focus:outline-none" title="Copy JSON"><i class="fa-regular fa-copy text-sm"></i></button>
                                        </div>
                                        <div class="p-5 overflow-x-auto text-[11px] font-mono leading-loose whitespace-pre" x-html="syntaxHighlight(`{{ str_replace('"', '\"', $mockPayload) }}`)"></div>
                                    </div>

                                    {{-- Right: Actions & Resolution --}}
                                    <div class="w-full lg:w-64 shrink-0 flex flex-col gap-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 border-b border-slate-200 pb-2">Resolution Actions</h4>
                                        
                                        @if($canMarkRead)
                                            <form method="POST" action="{{ route('user.notifications.read', $note->id) }}" class="m-0" @submit.prevent="optimisticMarkRead($event, {{ $note->id }})">
                                                @csrf
                                                <button type="submit" id="btn-read-{{ $note->id }}" @mouseenter="playHover()" class="w-full px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none flex items-center justify-center gap-2 {{ $isRead ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed' : 'bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:shadow-[0_5px_15px_rgba(79,70,229,0.3)] hover:-translate-y-0.5' }}" {{ $isRead ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-check"></i> <span>{{ $isRead ? 'Acknowledged' : 'Mark as Read' }}</span>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button @click="copyToClipboard('{{ $mockId }}', 'Trace ID copied')" @mouseenter="playHover()" class="w-full px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 hover:border-slate-300 hover:-translate-y-0.5">
                                            <i class="fa-solid fa-share-nodes"></i> Forward to Slack
                                        </button>

                                        @if($priority === 'high')
                                            <button @mouseenter="playHover()" class="w-full mt-auto px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none flex items-center justify-center gap-2 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white hover:shadow-[0_5px_15px_rgba(225,29,72,0.3)] hover:-translate-y-0.5">
                                                <i class="fa-solid fa-ticket"></i> Escalate to Jira
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Empty State Handled by Alpine --}}
                    @endforelse

                    {{-- Alpine JS Empty State for Search or 0 Items --}}
                    <div x-show="visibleCount === 0" style="display: none;" class="py-32 text-center flex flex-col items-center justify-center animate-fade-in-up">
                        <div class="relative w-32 h-32 mb-8">
                            <div class="absolute inset-0 bg-indigo-500/10 rounded-full blur-2xl"></div>
                            <div class="w-24 h-24 bg-white border border-slate-100 rounded-[2rem] shadow-[0_10px_30px_rgba(0,0,0,0.05)] flex items-center justify-center relative z-10 mx-auto rotate-12 hover:rotate-0 transition-transform duration-500">
                                <i class="fa-solid fa-mug-hot text-indigo-400 text-5xl"></i>
                            </div>
                        </div>
                        <h4 class="text-slate-900 font-black text-3xl tracking-tight mb-3">Inbox Zero</h4>
                        <p class="text-slate-500 font-medium text-lg max-w-md mx-auto leading-relaxed" x-text="search !== '' ? 'No telemetry records match your cryptographic search query.' : 'You are all caught up on system alerts. No anomalies detected.'"></p>
                        
                        <button x-show="search !== ''" @click="search = ''; playClick()" @mouseenter="playHover()" class="mt-8 px-8 py-3.5 bg-white border border-slate-200 text-indigo-600 rounded-xl font-bold text-sm shadow-sm hover:bg-indigo-50 hover:border-indigo-300 hover:-translate-y-0.5 transition-all focus:outline-none">
                            <i class="fa-solid fa-rotate-left mr-2"></i> Clear Filters
                        </button>
                    </div>
                </div>

                {{-- Pagination Placeholder --}}
                @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
                <div class="px-8 py-5 bg-white/95 backdrop-blur-md border-t border-slate-200 relative z-20 shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                    {{ $notifications->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    /* Premium Scrollbars */
    .scrollbar-custom::-webkit-scrollbar { width: 6px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .scrollbar-custom:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

    /* Animations */
    @keyframes swing { 20% { transform: rotate(15deg); } 40% { transform: rotate(-10deg); } 60% { transform: rotate(5deg); } 80% { transform: rotate(-5deg); } 100% { transform: rotate(0deg); } }
    .animate-swing { transform-origin: top center; animation: swing 1s ease-in-out infinite; }
    
    @keyframes waveform { 0%, 100% { height: 20%; } 50% { height: 100%; } }
    .animate-waveform { animation: waveform 1s ease-in-out infinite; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    .transform-style-3d { transform-style: preserve-3d; perspective: 1000px; }
    .translate-z-20 { transform: translateZ(20px); }
    .translate-z-30 { transform: translateZ(30px); }
</style>
@endpush

@push('scripts')
<script>
// ================= AUDIO ENGINE =================
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
    Alpine.data('inboxController', () => ({
        search: '',
        filter: 'all', 
        totalUnread: {{ $unreadCount }},
        totalCount: {{ $totalCount }},
        visibleCount: {{ $totalCount }},
        velocity: 14,
        
        // Live Polling Engine
        isPolling: true,
        pollInterval: null,
        uptime: '00:00:00',
        uptimeSeconds: 0,
        dbPing: 12,

        init() {
            setTimeout(() => this.updateVisibleCount(), 100);

            // System Uptime Clock
            setInterval(() => {
                this.uptimeSeconds++;
                const h = Math.floor(this.uptimeSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((this.uptimeSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (this.uptimeSeconds % 60).toString().padStart(2, '0');
                this.uptime = `${h}:${m}:${s}`;
            }, 1000);

            // DB Ping Simulation
            setInterval(() => { this.dbPing = Math.floor(Math.random() * 15) + 8; }, 2000);

            // Velocity simulation
            setInterval(() => { this.velocity = Math.floor(Math.random() * (22 - 8 + 1) + 8); }, 3000);

            if(this.isPolling) this.startPolling();
        },

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        togglePolling() {
            this.isPolling = !this.isPolling;
            this.playClick();
            if(this.isPolling) {
                this.startPolling();
                this.$dispatch('notify', { message: 'Live auto-sync resumed.', type: 'success' });
            } else {
                clearInterval(this.pollInterval);
                this.$dispatch('notify', { message: 'Live auto-sync paused.', type: 'info' });
            }
        },

        startPolling() {
            this.pollInterval = setInterval(() => {
                // In production, an AJAX fetch would happen here.
                console.log('Background polling executed.');
            }, 10000);
        },

        // JSON Syntax Highlighter
        syntaxHighlight(json) {
            if (typeof json != 'string') json = JSON.stringify(json, undefined, 2);
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = 'text-sky-400'; // number
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) cls = 'text-indigo-300'; // key
                    else cls = 'text-emerald-400'; // string
                } else if (/true|false/.test(match)) cls = 'text-rose-400'; // boolean
                else if (/null/.test(match)) cls = 'text-slate-500'; // null
                return '<span class="' + cls + '">' + match + '</span>';
            });
        },

        matchesSearch(msg) {
            if (this.search === '') return true;
            return msg.toLowerCase().includes(this.search.toLowerCase());
        },

        matchesFilter(isRead, category) {
            if (this.filter === 'all') return true;
            if (this.filter === 'unread') return !isRead;
            return this.filter === category;
        },

        updateVisibleCount() {
            setTimeout(() => {
                const cards = document.querySelectorAll('.notification-card');
                let count = 0;
                cards.forEach(card => { if (card.style.display !== 'none') count++; });
                this.visibleCount = count;
            }, 50);
        },

        copyToClipboard(text, successMsg = "Copied to clipboard") {
            this.playClick();
            navigator.clipboard.writeText(text).then(() => {
                this.$dispatch('notify', { message: successMsg, type: 'success' });
            });
        },

        // Optimistic Update + Zen Auto-Hide
        optimisticMarkRead(event, id) {
            const form = event.target;
            const btn = document.getElementById('btn-read-' + id);
            const card = document.getElementById('note-' + id);
            const dot = document.getElementById('unread-dot-' + id);

            this.playClick();

            // 1. Visual Acknowledge
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i> <span class="text-emerald-700">Acknowledged</span>';
            btn.className = 'w-full px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-inner focus:outline-none flex items-center justify-center gap-2 bg-emerald-50 border border-emerald-200 cursor-not-allowed';
            btn.disabled = true;

            card.classList.remove('border-indigo-300', 'ring-4', 'ring-indigo-50/50', 'unread-card');
            card.classList.add('border-emerald-200', 'bg-emerald-50/10');
            
            if(dot) {
                dot.classList.remove('scale-100', 'animate-pulse');
                dot.classList.add('scale-0');
            }

            if (this.totalUnread > 0) {
                this.totalUnread--;
                document.getElementById('unreadCountText').innerText = this.totalUnread;
                document.getElementById('navUnreadCount').innerText = this.totalUnread;
            }

            window.audioEngine.playSuccess();
            this.$dispatch('notify', { message: 'Alert officially acknowledged.', type: 'success' });

            // 2. ZEN MODE (Auto Hide)
            setTimeout(() => {
                // Determine if we need to set Alpine visibility to false based on filters
                // If filter is 'unread', marking it read means it should disappear.
                if(this.filter === 'unread' || this.filter === 'all') {
                    // Let Alpine handle the scale down & fade out via x-show binding
                    // But we must update Alpine's internal data state for this item so `isVisible` flips to false
                    const elData = Alpine.$data(card);
                    if(elData) elData.isVisible = false;
                    
                    setTimeout(() => { this.updateVisibleCount(); }, 350);
                }
            }, 1500);

            // 3. Background Sync (Mocked for Demo, execute real if form action exists)
            if(form.action && form.action !== window.location.href) {
                fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .catch(err => console.error("Sync failed", err));
            }
        },

        // Optimistic Batch Update
        markAllReadBatch() {
            const btn = document.getElementById('btnMarkAllRead');
            if(btn) btn.disabled = true;
            this.playClick();
            this.$dispatch('notify', { message: 'Acknowledging all visible alerts...', type: 'info' });

            const unreadForms = document.querySelectorAll('.unread-card form');
            unreadForms.forEach(form => { form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true })); });

            if(unreadForms.length === 0) {
                 this.$dispatch('notify', { message: 'No pending alerts to acknowledge.', type: 'info' });
                 if(btn) btn.disabled = false;
            }
        },

        // Live WebSocket Injection Simulation
        simulateLiveAlert() {
            this.playClick();
            const feed = document.getElementById('notificationFeed');
            const newId = Math.floor(Math.random() * 100000);
            
            // Format the time exactly as Blade does
            const d = new Date();
            const h = d.getHours().toString().padStart(2,'0');
            const m = d.getMinutes().toString().padStart(2,'0');
            const s = d.getSeconds().toString().padStart(2,'0');
            const timeStr = `${d.getFullYear()}-${(d.getMonth()+1).toString().padStart(2,'0')}-${d.getDate().toString().padStart(2,'0')} ${h}:${m}:${s} UTC`;

            const rawObj = {
                event_id: `EVT-MOCK-${newId}`,
                timestamp: timeStr,
                source_ip: "192.168.1.42",
                level: "HIGH",
                category: "SECURITY",
                message: "Unrecognized Login Attempt Detected (Eastern Europe)",
                metadata: { node: "aws-eu-central-1", latency_ms: 14, encrypted: true, status_code: 401 }
            };
            
            const rawPayloadString = JSON.stringify(rawObj, null, 2).replace(/"/g, '&quot;');
            
            const html = `
                <div x-data="{ expanded: false, isVisible: true }" id="note-${newId}" x-show="isVisible" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100 h-[auto]" x-transition:leave-end="opacity-0 scale-95 h-0 overflow-hidden mb-0 pb-0 border-0" class="notification-card group bg-white border border-rose-300 ring-4 ring-rose-50/50 rounded-[1.5rem] p-1.5 shadow-sm transition-all duration-500 unread-card opacity-0 -translate-y-4 scale-95" style="will-change: transform, opacity;">
                    <div @click="expanded = !expanded; playClick()" @mouseenter="playHover()" class="p-4 sm:p-5 flex flex-col sm:flex-row gap-5 items-start cursor-pointer rounded-[1.2rem] hover:bg-slate-50 transition-colors relative overflow-hidden">
                        <div class="absolute inset-0 bg-indigo-50/50 opacity-0 transition-opacity duration-300 pointer-events-none" :class="expanded ? 'opacity-100' : ''"></div>
                        <div class="shrink-0 relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center text-lg shadow-sm transition-transform duration-300" :class="expanded ? 'scale-110' : 'group-hover:scale-110'">
                                <i class="fa-solid fa-shield-virus animate-pulse"></i>
                            </div>
                            <div id="unread-dot-${newId}" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-rose-500 border-[3px] border-white rounded-full shadow-sm animate-pulse transition-transform duration-500 scale-100"></div>
                        </div>
                        <div class="flex-1 w-full relative z-10">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-2 mb-1.5">
                                <h3 class="text-base font-bold text-slate-900 leading-snug pr-4 transition-colors" :class="expanded ? 'text-indigo-700' : ''">Unrecognized Login Attempt Detected (Eastern Europe)</h3>
                                <span class="text-[10px] font-bold text-rose-500 whitespace-nowrap uppercase tracking-widest flex items-center gap-1.5"><i class="fa-regular fa-clock"></i> Just now</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200">Security</span>
                                <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200 shadow-sm"><i class="fa-solid fa-bolt text-rose-500 mr-0.5"></i> Urgent</span>
                                <span class="text-[9px] font-bold text-slate-400 font-mono ml-2 opacity-0 group-hover:opacity-100 transition-opacity hidden sm:inline-block bg-slate-100 px-2 py-0.5 rounded">ID: EVT-MOCK-${newId}</span>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-3 relative z-10 mt-2 sm:mt-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 bg-white border border-slate-200 shadow-sm transition-all duration-300" :class="expanded ? 'bg-indigo-50 border-indigo-200 text-indigo-600 rotate-180' : 'group-hover:bg-slate-100'">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                    <div x-show="expanded" x-collapse.duration.300ms style="display: none;">
                        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-[1.2rem] mt-1 flex flex-col lg:flex-row gap-6">
                            <div class="flex-1 bg-[#0f172a] rounded-[1.25rem] border border-slate-700 shadow-inner overflow-hidden flex flex-col">
                                <div class="bg-[#1e293b] px-5 py-3 border-b border-slate-700 flex justify-between items-center">
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest font-mono flex items-center gap-2">
                                        <div class="flex gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div></div>
                                        <span class="ml-2">Live Event Payload</span>
                                    </span>
                                    <button @click="copyToClipboard('${rawPayloadString}', 'Payload copied')" @mouseenter="playHover()" class="text-slate-400 hover:text-white transition-colors"><i class="fa-regular fa-copy text-sm"></i></button>
                                </div>
                                <div class="p-5 overflow-x-auto text-[11px] font-mono leading-loose whitespace-pre" x-html="syntaxHighlight('${rawPayloadString}')"></div>
                            </div>
                            <div class="w-full lg:w-64 shrink-0 flex flex-col gap-3">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 border-b border-slate-200 pb-2">Resolution Actions</h4>
                                <form action="#" class="m-0" @submit.prevent="optimisticMarkRead($event, ${newId})">
                                    <button type="submit" id="btn-read-${newId}" @mouseenter="playHover()" class="w-full px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none flex items-center justify-center gap-2 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa-solid fa-check"></i> <span>Mark as Read</span>
                                    </button>
                                </form>
                                <button class="w-full mt-auto px-4 py-3.5 rounded-xl text-xs font-bold transition-all shadow-sm focus:outline-none flex items-center justify-center gap-2 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white">
                                    <i class="fa-solid fa-ticket"></i> Escalate to Jira
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const emptyState = document.querySelector('[x-show="visibleCount === 0"]');
            if(emptyState && this.visibleCount === 0) emptyState.style.display = 'none';

            feed.insertAdjacentHTML('afterbegin', html);
            const newEl = document.getElementById(`note-${newId}`);
            
            // Trigger Animation
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    newEl.classList.remove('scale-95', 'opacity-0', '-translate-y-4');
                    newEl.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                });
            });

            this.totalUnread++;
            this.totalCount++;
            this.visibleCount++;
            document.getElementById('unreadCountText').innerText = this.totalUnread;
            document.getElementById('navUnreadCount').innerText = this.totalUnread;

            window.audioEngine.playSuccess();
            this.$dispatch('notify', { message: 'Critical Security Alert Received', type: 'error' });
        }
    }));

    // Toast Listener
    window.addEventListener('notify', (e) => {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        document.getElementById('toastMsg').innerText = e.detail.message;
        
        if(e.detail.type === 'error') {
            icon.className = "fa-solid fa-triangle-exclamation text-rose-400 text-lg";
        } else if(e.detail.type === 'info') {
            icon.className = "fa-solid fa-circle-info text-sky-400 text-lg";
        } else {
            icon.className = "fa-solid fa-circle-check text-emerald-400 text-lg";
        }

        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
    });
});
</script>
@endpush