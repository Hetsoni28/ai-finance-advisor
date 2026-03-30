@extends('layouts.app')

@section('title', 'User Governance - FinanceAI Admin')

@section('content')

@php
    // Safe Fallbacks for UI demonstration
    $stats = $stats ?? ['total'=>1250, 'active'=>1100, 'blocked'=>145, 'admins'=>5];
    $users = $users ?? collect([]); 
    
    $total   = (int) ($stats['total'] ?? 0);
    $active  = (int) ($stats['active'] ?? 0);
    $blocked = (int) ($stats['blocked'] ?? 0);
    $admins  = (int) ($stats['admins'] ?? 0);

    $health = $total > 0 ? round(($active / $total) * 100) : 0;
@endphp

<div class="min-h-screen bg-[#f8fafc] pb-24 font-sans selection:bg-indigo-100 selection:text-indigo-900 relative">

    {{-- Pristine Ambient Background --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.02]">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-indigo-50 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10 space-y-8">

        {{-- ================= 1. PAGE HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-200 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.02)]">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-bold tracking-wide uppercase mb-4">
                    <i class="fa-solid fa-shield-halved"></i> Admin Governance
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">User Control Matrix</h1>
                <p class="text-slate-500 mt-2 font-medium">Manage accounts, monitor roles, and enforce platform security.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="exportCleanCSV()" class="group relative px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold shadow-sm hover:border-indigo-300 hover:text-indigo-600 hover:shadow-md transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-file-csv"></i> Export Data
                </button>
                <a href="#" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-[0_4px_15px_rgba(79,70,229,0.3)] hover:bg-indigo-700 hover:shadow-[0_6px_25px_rgba(79,70,229,0.4)] transition-all flex items-center gap-2 hover:-translate-y-0.5">
                    <i class="fa-solid fa-user-plus"></i> Invite User
                </a>
            </div>
        </div>

        {{-- ================= 2. KPI CARDS & GROWTH CHART ================= --}}
        <div class="grid lg:grid-cols-12 gap-8">
            
            {{-- KPI Stats (Left 8 cols) --}}
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                @php
                    $kpiCards = [
                        ['label'=>'Total Registered', 'value'=>$total, 'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50', 'border'=>'border-indigo-100', 'icon'=>'fa-users', 'trend'=>'+12%'],
                        ['label'=>'Active Accounts', 'value'=>$active, 'color'=>'text-emerald-600', 'bg'=>'bg-emerald-50', 'border'=>'border-emerald-100', 'icon'=>'fa-user-check', 'trend'=>'+8%'],
                        ['label'=>'Suspended', 'value'=>$blocked, 'color'=>'text-rose-600', 'bg'=>'bg-rose-50', 'border'=>'border-rose-100', 'icon'=>'fa-user-lock', 'trend'=>'-2%'],
                        ['label'=>'System Admins', 'value'=>$admins, 'color'=>'text-amber-600', 'bg'=>'bg-amber-50', 'border'=>'border-amber-100', 'icon'=>'fa-user-shield', 'trend'=>'Stable'],
                    ];
                @endphp

                @foreach($kpiCards as $card)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl {{ $card['bg'] }} {{ $card['color'] }} flex items-center justify-center border {{ $card['border'] }} shadow-sm group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid {{ $card['icon'] }} text-xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-slate-50 text-slate-500 border border-slate-100 rounded-lg text-xs font-bold">{{ $card['trend'] }}</span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 relative z-10">{{ $card['label'] }}</p>
                    <p class="text-4xl font-black text-slate-900 relative z-10 counter" data-target="{{ $card['value'] }}">0</p>
                </div>
                @endforeach
            </div>

            {{-- User Growth Chart (Right 4 cols) --}}
            <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Network Growth</h3>
                    <p class="text-2xl font-black text-slate-900 mb-6">Last 30 Days</p>
                </div>
                <div class="h-40 w-full relative">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ================= 3. ADVANCED FILTER BAR ================= --}}
        <div class="bg-white p-4 rounded-[1.5rem] border border-slate-200 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.02)] sticky top-4 z-30 transition-all" id="filterBar">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                
                {{-- Magnetic Search --}}
                <div class="relative w-full md:w-96 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search name, email..." 
                           class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none transition-all placeholder-slate-400">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <kbd class="hidden sm:inline-block px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black text-slate-400 shadow-sm">/</kbd>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    {{-- Role Filter --}}
                    <div class="relative">
                        <select name="role" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none cursor-pointer appearance-none pr-10 transition-all">
                            <option value="">All Roles</option>
                            <option value="admin" @selected(request('role')=='admin')>Administrators</option>
                            <option value="user" @selected(request('role')=='user')>Standard Users</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    {{-- Status Filter --}}
                    <div class="relative">
                        <select name="status" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 outline-none cursor-pointer appearance-none pr-10 transition-all">
                            <option value="">All Statuses</option>
                            <option value="active" @selected(request('status')=='active')>Active Only</option>
                            <option value="blocked" @selected(request('status')=='blocked')>Suspended Only</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    <button type="submit" class="px-6 py-3.5 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl font-bold shadow-md transition-colors">
                        Filter
                    </button>
                    
                    @if(request()->anyFilled(['search', 'status', 'role']))
                        <a href="{{ route('admin.users.index') ?? '#' }}" class="px-4 py-3.5 text-slate-400 hover:text-rose-500 text-sm font-bold transition-colors">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ================= 4. MAIN DATA TABLE ================= --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden relative">
            <div class="overflow-x-auto">
                <table id="userTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200">
                            <th class="px-6 py-5 w-12">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="masterCheckbox" onclick="toggleAllCheckboxes(this)" class="peer sr-only">
                                    <div class="w-5 h-5 rounded-[6px] border-2 border-slate-300 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 flex items-center justify-center transition-all bg-white shadow-sm">
                                        <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                    </div>
                                </label>
                            </th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">User Profile</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Contact Info</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Access Role</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">System Status</th>
                            <th class="px-6 py-5 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $usr)
                            @php
                                $isAdmin = method_exists($usr, 'isAdmin') && $usr->isAdmin();
                                $isBlocked = $usr->is_blocked ?? false;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer row-item">
                                
                                {{-- Checkbox --}}
                                <td class="px-6 py-4 w-12" onclick="event.stopPropagation();">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" class="rowCheckbox peer sr-only" value="{{ $usr->id }}" onclick="handleRowCheck()">
                                        <div class="w-5 h-5 rounded-[6px] border-2 border-slate-300 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 flex items-center justify-center transition-all bg-white shadow-sm">
                                            <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                        </div>
                                    </label>
                                </td>

                                {{-- User Profile --}}
                                <td class="px-6 py-4" onclick="openUserDrawer('{{ e($usr->name) }}', '{{ e($usr->email) }}', '{{ $usr->id }}', '{{ $isAdmin ? 'Admin' : 'User' }}', '{{ $isBlocked ? 'Blocked' : 'Active' }}')">
                                    <div class="flex items-center gap-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($usr->name) }}&background=e2e8f0&color=475569&bold=true" class="w-12 h-12 rounded-2xl border border-slate-200 shadow-sm group-hover:border-indigo-200 transition-colors" alt="Avatar">
                                        <div>
                                            <p class="font-bold text-slate-900 text-base group-hover:text-indigo-600 transition-colors" data-export="name">{{ e($usr->name) }}</p>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ID: #{{ str_pad($usr->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email (With Copy Micro-Interaction) --}}
                                <td class="px-6 py-4" onclick="event.stopPropagation();">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-slate-600" data-export="email">{{ e($usr->email) }}</span>
                                        <button onclick="copyText('{{ e($usr->email) }}', this)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all focus:outline-none shadow-sm" title="Copy Email">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </td>

                                {{-- Role Badge --}}
                                <td class="px-6 py-4" onclick="openUserDrawer('{{ e($usr->name) }}', '{{ e($usr->email) }}', '{{ $usr->id }}', '{{ $isAdmin ? 'Admin' : 'User' }}', '{{ $isBlocked ? 'Blocked' : 'Active' }}')">
                                    @if($isAdmin)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold shadow-sm" data-export="role">
                                            <i class="fa-solid fa-shield-halved text-[10px]"></i> Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold shadow-sm" data-export="role">
                                            <i class="fa-regular fa-user text-[10px]"></i> User
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4" onclick="openUserDrawer('{{ e($usr->name) }}', '{{ e($usr->email) }}', '{{ $usr->id }}', '{{ $isAdmin ? 'Admin' : 'User' }}', '{{ $isBlocked ? 'Blocked' : 'Active' }}')">
                                    @if($isBlocked)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold shadow-sm" data-export="status">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" data-export="status">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right" onclick="event.stopPropagation();">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="openUserDrawer('{{ e($usr->name) }}', '{{ e($usr->email) }}', '{{ $usr->id }}', '{{ $isAdmin ? 'Admin' : 'User' }}', '{{ $isBlocked ? 'Blocked' : 'Active' }}')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 flex items-center justify-center transition-all shadow-sm" title="View Profile">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>

                                        @if(auth()->id() !== $usr->id)
                                            @if($isBlocked)
                                                <button type="button" onclick="openConfirmModal('unblock', '{{ Route::has('admin.users.block') ? route('admin.users.block', $usr) : '#' }}', '{{ e($usr->name) }}')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 flex items-center justify-center transition-all shadow-sm" title="Unblock User">
                                                    <i class="fa-solid fa-unlock text-sm"></i>
                                                </button>
                                            @else
                                                <button type="button" onclick="openConfirmModal('block', '{{ Route::has('admin.users.block') ? route('admin.users.block', $usr) : '#' }}', '{{ e($usr->name) }}')" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-all shadow-sm" title="Suspend User">
                                                    <i class="fa-solid fa-ban text-sm"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-32 text-center">
                                    <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-6 shadow-sm">
                                        <i class="fa-solid fa-users-slash text-3xl"></i>
                                    </div>
                                    <p class="text-xl text-slate-800 font-black mb-2">No records found.</p>
                                    <p class="text-slate-500 font-medium">Try adjusting your search criteria or clearing filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination Placeholder --}}
            @if(method_exists($users, 'links') && $users->hasPages())
                <div class="px-8 py-5 border-t border-slate-100 bg-slate-50/50">
                    {{ $users->withQueryString()->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ================= MODALS, DRAWERS, & TOASTS ================= --}}

{{-- 1. FLOATING BULK ACTIONS BAR (Vanilla JS) --}}
<div id="bulkActionBar" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white px-8 py-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex items-center gap-6 transform translate-y-40 opacity-0 transition-all duration-400 ease-out pointer-events-none border border-slate-700">
    <div class="flex items-center gap-3">
        <span class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-black shadow-inner" id="bulkCount">0</span>
        <span class="text-sm font-bold text-slate-300 tracking-wide uppercase">Selected</span>
    </div>
    <div class="h-8 w-px bg-slate-700"></div>
    <div class="flex items-center gap-3">
        <button class="px-5 py-2.5 bg-slate-800 border border-slate-700 hover:bg-emerald-500 hover:border-emerald-500 text-white rounded-xl text-sm font-bold transition-colors shadow-sm">Unblock All</button>
        <button class="px-5 py-2.5 bg-slate-800 border border-slate-700 hover:bg-rose-500 hover:border-rose-500 text-white rounded-xl text-sm font-bold transition-colors shadow-sm">Suspend All</button>
    </div>
</div>

{{-- 2. USER DETAILS DRAWER (With Tabs) --}}
<div id="userDrawerOverlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[100] hidden opacity-0 transition-opacity duration-300" onclick="closeUserDrawer()"></div>
<div id="userDrawer" class="fixed top-0 right-0 h-full w-full max-w-lg bg-white shadow-[-20px_0_50px_rgba(0,0,0,0.1)] z-[101] transform translate-x-full transition-transform duration-400 ease-out flex flex-col border-l border-slate-200">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
        <h2 class="text-xl font-black text-slate-900">User Intelligence</h2>
        <button onclick="closeUserDrawer()" class="w-10 h-10 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center justify-center shadow-sm">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto bg-slate-50/50">
        {{-- Profile Header --}}
        <div class="p-8 text-center bg-white border-b border-slate-100">
            <img src="" id="drawerAvatar" class="w-28 h-28 rounded-3xl mx-auto border border-slate-200 shadow-md mb-5" alt="Avatar">
            <h3 class="text-3xl font-black text-slate-900 mb-1" id="drawerName">User Name</h3>
            <p class="text-slate-500 font-bold flex items-center justify-center gap-2">
                <span id="drawerEmail">user@email.com</span>
                <button onclick="copyText(document.getElementById('drawerEmail').innerText, this)" class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all flex items-center justify-center shadow-sm"><i class="fa-regular fa-copy text-xs"></i></button>
            </p>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex border-b border-slate-200 bg-white px-6">
            <button class="drawer-tab active px-4 py-4 text-sm font-bold text-indigo-600 border-b-2 border-indigo-600 transition-colors" data-target="tab-overview">Overview</button>
            <button class="drawer-tab px-4 py-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 transition-colors" data-target="tab-security">Security</button>
            <button class="drawer-tab px-4 py-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 transition-colors" data-target="tab-logs">Activity Logs</button>
        </div>

        {{-- Tab Content: Overview --}}
        <div id="tab-overview" class="drawer-content p-8 block">
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Platform Role</p>
                    <p class="font-black text-lg text-slate-800" id="drawerRole">User</p>
                </div>
                <div class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">System Status</p>
                    <p class="font-black text-lg text-slate-800" id="drawerStatus">Active</p>
                </div>
                <div class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Internal ID</p>
                    <p class="font-mono font-bold text-lg text-slate-800" id="drawerId">#0000</p>
                </div>
                <div class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Date Joined</p>
                    <p class="font-bold text-lg text-slate-800">12 May 2025</p>
                </div>
            </div>
            
            <div class="space-y-4 pt-6 border-t border-slate-200">
                <button class="w-full py-4 rounded-xl bg-white border-2 border-slate-900 text-slate-900 font-bold shadow-sm hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-invoice"></i> View Billing History
                </button>
                <button class="w-full py-4 rounded-xl bg-white border-2 border-rose-200 text-rose-600 font-bold hover:bg-rose-50 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-ban"></i> Suspend Account
                </button>
            </div>
        </div>

        {{-- Tab Content: Security (Dummy Data for Demo) --}}
        <div id="tab-security" class="drawer-content p-8 hidden">
            <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4">Authentication Status</h4>
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm mb-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-slate-700">Two-Factor Auth</span>
                    <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-black uppercase rounded-lg">Enabled</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-700">Last Password Change</span>
                    <span class="text-sm font-medium text-slate-500">2 months ago</span>
                </div>
            </div>
            <button class="w-full py-4 rounded-xl bg-slate-900 text-white font-bold shadow-md hover:bg-indigo-600 transition-colors">
                Force Password Reset
            </button>
        </div>

        {{-- Tab Content: Logs (Dummy Data for Demo) --}}
        <div id="tab-logs" class="drawer-content p-8 hidden">
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="w-2 h-2 mt-2 rounded-full bg-emerald-500"></div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Successful Login</p>
                        <p class="text-xs text-slate-500 font-mono mt-1">IP: 192.168.1.1 • Today, 10:42 AM</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Generated AI Forecast</p>
                        <p class="text-xs text-slate-500 font-mono mt-1">Via Web Dashboard • Yesterday, 2:15 PM</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- 3. CUSTOM CONFIRMATION MODAL (Replaces ugly browser confirm) --}}
<div id="confirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] hidden flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="confirmModalContent" class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200">
        <div class="p-8 text-center">
            <div id="confirmIconContainer" class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i id="confirmIcon" class="fa-solid fa-ban text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2" id="confirmTitle">Suspend User?</h3>
            <p class="text-slate-500 font-medium mb-8" id="confirmDesc">
                Are you sure you want to suspend <strong id="confirmUserName" class="text-slate-800">User</strong>? They will lose access immediately.
            </p>
            
            <div class="flex gap-4">
                <button onclick="closeConfirmModal()" class="flex-1 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors">
                    Cancel
                </button>
                <form id="confirmForm" action="#" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" id="confirmSubmitBtn" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-colors">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 4. GLOBAL TOAST --}}
<div id="toast" class="fixed bottom-8 right-8 z-[120] bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 transform translate-y-24 opacity-0 transition-all duration-400 pointer-events-none border border-slate-800">
    <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30 text-emerald-400">
        <i id="toastIcon" class="fa-solid fa-check"></i>
    </div>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action Successful</span>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. ANIMATED COUNTERS
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseFloat(el.dataset.target) || 0;
        const duration = 1500;
        let startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const easedValue = progress === 1 ? target : target * (1 - Math.pow(2, -10 * progress));
            el.innerText = easedValue.toLocaleString('en-US', { maximumFractionDigits: 0 });
            if (progress < 1) window.requestAnimationFrame(step);
        }
        if(target > 0) window.requestAnimationFrame(step);
    });

    // 2. SEARCH BAR MAGNETIC SHORTCUT (Cmd+K or /)
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
            e.preventDefault();
            const searchInput = document.getElementById('searchInput');
            searchInput.focus();
            // Optional: Smooth scroll to search bar if offscreen
            searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // 3. USER GROWTH CHART (Chart.js)
    const ctx = document.getElementById('growthChart');
    if(ctx) {
        // Dummy data for visual effect
        const labels = ['1W', '2W', '3W', '4W', '5W', 'Now'];
        const data = [850, 920, 1050, 1100, 1180, 1250];
        
        const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 160);
        grad.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        grad.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: grad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 8 } },
                scales: {
                    x: { display: false },
                    y: { display: false, min: 800 } // Hide axes for a clean sparkline look
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    }

    // 4. DRAWER TAB LOGIC (Vanilla JS)
    const tabs = document.querySelectorAll('.drawer-tab');
    const contents = document.querySelectorAll('.drawer-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset tabs
            tabs.forEach(t => {
                t.classList.remove('text-indigo-600', 'border-indigo-600', 'active');
                t.classList.add('text-slate-500', 'border-transparent');
            });
            // Set active tab
            tab.classList.remove('text-slate-500', 'border-transparent');
            tab.classList.add('text-indigo-600', 'border-indigo-600', 'active');

            // Hide contents
            contents.forEach(c => c.classList.add('hidden'));
            
            // Show target
            const target = document.getElementById(tab.dataset.target);
            if(target) target.classList.remove('hidden');
        });
    });

});

// ================= GLOBAL FUNCTIONS =================

// 5. CHECKBOX & BULK ACTION LOGIC
function toggleAllCheckboxes(masterCheckbox) {
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    rowCheckboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    handleRowCheck();
}

function handleRowCheck() {
    const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
    const bulkBar = document.getElementById('bulkActionBar');
    const bulkCount = document.getElementById('bulkCount');

    if(checkedCount > 0) {
        bulkCount.innerText = checkedCount;
        bulkBar.classList.remove('translate-y-40', 'opacity-0', 'pointer-events-none');
        bulkBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
    } else {
        bulkBar.classList.add('translate-y-40', 'opacity-0', 'pointer-events-none');
        bulkBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        document.getElementById('masterCheckbox').checked = false;
    }
}

// 6. FLAWLESS CSV EXPORT ENGINE
function exportCleanCSV() {
    showToast("Generating CSV Export...", true);
    
    const rows = document.querySelectorAll("#userTable tbody tr.row-item");
    let csv = "Name,Email,Role,Status\n";

    rows.forEach(row => {
        // Using dataset attributes for exact data extraction, ignoring HTML tags
        const name = row.querySelector('[data-export="name"]')?.innerText.replace(/,/g, "") || '';
        const email = row.querySelector('[data-export="email"]')?.innerText.replace(/,/g, "") || '';
        const role = row.querySelector('[data-export="role"]')?.innerText.replace(/,/g, "").trim() || '';
        const status = row.querySelector('[data-export="status"]')?.innerText.replace(/,/g, "").trim() || '';

        if(name && email) {
            csv += `${name},${email},${role},${status}\n`;
        }
    });

    // Trigger Download
    setTimeout(() => {
        const blob = new Blob([csv], {type: "text/csv;charset=utf-8;"});
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "FinanceAI_User_Audit_" + new Date().toISOString().slice(0,10) + ".csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }, 800);
}

// 7. SLIDING DRAWER LOGIC
function openUserDrawer(name, email, id, role, status) {
    document.getElementById('drawerName').innerText = name;
    document.getElementById('drawerEmail').innerText = email;
    document.getElementById('drawerId').innerText = '#' + String(id).padStart(5, '0');
    document.getElementById('drawerRole').innerText = role;
    document.getElementById('drawerStatus').innerText = status;
    document.getElementById('drawerAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=e2e8f0&color=4f46e5&bold=true&size=200`;

    const roleEl = document.getElementById('drawerRole');
    roleEl.className = role === 'Admin' ? 'font-black text-indigo-600 text-lg' : 'font-bold text-slate-800 text-lg';
    
    const statusEl = document.getElementById('drawerStatus');
    statusEl.className = status === 'Blocked' ? 'font-black text-rose-600 text-lg' : 'font-bold text-emerald-600 text-lg';

    document.body.classList.add('drawer-open');
    const overlay = document.getElementById('userDrawerOverlay');
    const drawer = document.getElementById('userDrawer');
    
    overlay.classList.remove('hidden');
    setTimeout(() => {
        overlay.classList.remove('opacity-0');
        drawer.classList.remove('translate-x-full');
    }, 10);
}

function closeUserDrawer() {
    document.body.classList.remove('drawer-open');
    const overlay = document.getElementById('userDrawerOverlay');
    const drawer = document.getElementById('userDrawer');
    
    overlay.classList.add('opacity-0');
    drawer.classList.add('translate-x-full');
    
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 300);
}

// 8. CUSTOM CONFIRM MODAL
function openConfirmModal(action, url, userName) {
    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('confirmForm');
    const title = document.getElementById('confirmTitle');
    const desc = document.getElementById('confirmDesc');
    const iconContainer = document.getElementById('confirmIconContainer');
    const icon = document.getElementById('confirmIcon');
    const submitBtn = document.getElementById('confirmSubmitBtn');

    form.action = url;
    document.getElementById('confirmUserName').innerText = userName;

    if(action === 'block') {
        title.innerText = 'Suspend User?';
        desc.innerHTML = `Are you sure you want to suspend <strong class="text-slate-800">${userName}</strong>? They will lose access immediately.`;
        iconContainer.className = "w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm";
        icon.className = "fa-solid fa-ban text-2xl";
        submitBtn.className = "w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-colors";
        submitBtn.innerText = "Yes, Suspend";
    } else {
        title.innerText = 'Unblock User?';
        desc.innerHTML = `Are you sure you want to restore access for <strong class="text-slate-800">${userName}</strong>?`;
        iconContainer.className = "w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm";
        icon.className = "fa-solid fa-unlock text-2xl";
        submitBtn.className = "w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-colors";
        submitBtn.innerText = "Yes, Restore Access";
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('confirmModalContent').classList.remove('scale-95');
    }, 10);
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('opacity-0');
    document.getElementById('confirmModalContent').classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// Intercept form submissions from the table
function confirmAction(e, message) {
    e.preventDefault(); 
    // The specific action logic is now handled by the custom modal button clicks
    return false; 
}

// 9. MICRO-INTERACTIONS
function copyText(text, btnElement) {
    event.stopPropagation(); 
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i>';
        showToast("Email copied to clipboard", true);
        setTimeout(() => { btnElement.innerHTML = originalHtml; }, 2000);
    });
}

function showToast(msg, isSuccess = true) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMsg').innerText = msg;
    
    toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
    
    setTimeout(() => {
        toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    }, 3000);
}
</script>
@endpush