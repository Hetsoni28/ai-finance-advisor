<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased selection:bg-indigo-500 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FinanceAI Enterprise Terminal')</title>

    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind CSS (JIT) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: { brand: '#4f46e5' }, // Enterprise Indigo
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.05)',
                        'floating': '0 20px 60px -10px rgba(79, 70, 229, 0.15)',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js Ecosystem --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        /* 🌐 ENTERPRISE LIGHT WHITE SCROLLBAR */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* 🧊 GLASSMORPHIC UTILITIES */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        /* ⚡ HARDWARE ACCELERATED ANIMATIONS & UTILS */
        [x-cloak] { display: none !important; }
        .transform-gpu { transform: translateZ(0); }
        
        /* 🧾 PDF PRINT SAFETY */
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .print-full-width { width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; }
        }

        /* Loading Bar & Skeleton Animations */
        @keyframes loading-strip {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .animate-loading-strip { animation: loading-strip 1.5s infinite linear; }
        
        /* AI Orb Pulse */
        @keyframes ai-pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
        .ai-orb-pulse { animation: ai-pulse 2s infinite; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800 h-screen overflow-hidden flex relative" 
      x-data="globalArchitecture()" 
      @keydown.window.prevent.ctrl.k="toggleCommandPalette()" 
      @keydown.window.prevent.cmd.k="toggleCommandPalette()"
      @keydown.window.prevent.ctrl.i="toggleInspector()" 
      @keydown.window.prevent.cmd.i="toggleInspector()"
      @keyup.shift.i.window="triggerQuickAction('income')"
      @keyup.shift.e.window="triggerQuickAction('expense')"
      @keyup.shift.?.window="helpModalOpen = true"
      @keyup.shift.z.window="toggleZenMode()">

    {{-- ================= 0. NETWORK STATUS MONITOR ================= --}}
    <div x-show="!isOnline" x-cloak 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
         class="fixed top-0 inset-x-0 z-[99999] bg-rose-600 text-white px-4 py-2 flex items-center justify-center gap-3 shadow-lg">
        <i class="fa-solid fa-wifi text-rose-300"></i>
        <span class="text-[10px] font-black uppercase tracking-widest">Connection Lost. Waiting for network...</span>
        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin ml-2"></div>
    </div>

    {{-- ================= 1. INITIAL PAGE BOOT LOADER ================= --}}
    <div x-show="!isLoaded" x-transition.opacity.duration.600ms class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center no-print">
        <div class="relative w-24 h-24 flex items-center justify-center mb-6">
            <div class="absolute inset-0 border-4 border-indigo-50 rounded-[2rem] shadow-inner"></div>
            <div class="absolute inset-0 border-4 border-indigo-600 rounded-[2rem] border-t-transparent animate-spin"></div>
            <i class="fa-solid fa-cube text-indigo-600 text-2xl animate-pulse shadow-sm"></i>
        </div>
        <h2 class="text-slate-900 font-black tracking-[0.2em] uppercase text-xs mb-2">FinanceAI Terminal</h2>
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
            <p class="text-slate-400 font-bold text-[9px] uppercase tracking-widest">Securing 256-Bit Connection</p>
        </div>
    </div>

    {{-- ================= 2. TOP SPA NAVIGATION LOADER ================= --}}
    <div x-show="isNavigating" x-cloak class="fixed top-0 left-0 w-full h-1 z-[9000] overflow-hidden bg-indigo-50 no-print">
        <div class="h-full bg-indigo-600 w-1/2 rounded-r-full animate-loading-strip shadow-[0_0_15px_rgba(79,70,229,0.8)]"></div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen && !zenMode" x-cloak @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden no-print"></div>

    {{-- ================= 3. SIDEBAR COMPONENT INJECTION ================= --}}
    <div x-show="!zenMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="z-50 shrink-0">
        @if(View::exists('partials.sidebar'))
            @include('partials.sidebar')
        @else
            <aside class="fixed lg:static inset-y-0 left-0 w-72 bg-white border-r border-slate-200 transition-transform duration-300 flex flex-col no-print shadow-[0_0_40px_rgba(0,0,0,0.05)] lg:shadow-none" 
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <div class="h-20 flex items-center px-8 border-b border-slate-100 shrink-0 bg-slate-50/50">
                    <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white mr-3 shadow-md shadow-indigo-500/20"><i class="fa-solid fa-cube"></i></div>
                    <span class="font-black text-xl text-slate-900 tracking-tight">Finance<span class="text-indigo-600">AI</span></span>
                </div>
                <div class="flex-1 p-8 flex flex-col items-center justify-center text-center bg-slate-50/50">
                    <div class="w-16 h-16 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center mb-6 relative">
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 border-2 border-white rounded-full animate-pulse"></span>
                        <i class="fa-solid fa-code-merge text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mb-2">Awaiting UI Injection</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">The sidebar partial <code class="bg-white border border-slate-200 px-1 py-0.5 rounded text-[10px] text-rose-500 font-mono">partials.sidebar</code> was not found.</p>
                    <button @click="sidebarOpen = false" class="lg:hidden px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold shadow-md">Close Menu</button>
                </div>
            </aside>
        @endif
    </div>

    {{-- ================= 4. MAIN CONTENT WRAPPER ================= --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden print-full-width relative min-w-0 bg-[#f8fafc] transition-all duration-500">
        
        {{-- Inner Ambient Glows --}}
        <div class="absolute top-[-10%] left-[-5%] w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none z-0 no-print"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] bg-sky-500/5 rounded-full blur-[100px] pointer-events-none z-0 no-print"></div>
        
        {{-- Navbar Component Injection --}}
        <div x-show="!zenMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full" class="z-30 shrink-0">
            @if(View::exists('partials.navbar'))
                @include('partials.navbar')
            @else
                <header class="h-20 bg-white border-b border-slate-200 flex items-center px-8 z-30 shrink-0">
                    <span class="text-xs font-bold text-slate-500">Missing Partial: <code class="text-rose-500 bg-rose-50 px-1 rounded">partials.navbar</code></span>
                </header>
            @endif
        </div>

        {{-- Main Content Scroll Area --}}
        <main id="mainScrollArea" 
              class="flex-1 overflow-y-auto overflow-x-hidden relative z-10 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] pb-8" 
              @scroll.throttle.50ms="checkScroll($event)"
              :class="isNavigating ? 'scale-[0.98] blur-[2px] opacity-60 pointer-events-none' : 'scale-100 blur-0 opacity-100'">
            
            {{-- Scroll Progress Bar --}}
            <div x-show="!zenMode" class="fixed top-20 left-0 h-0.5 bg-indigo-500 z-[60] transition-all duration-150 no-print shadow-[0_0_8px_rgba(99,102,241,0.8)] lg:left-72" :style="`width: ${scrollProgress}%`"></div>
            
            {{-- Skeleton Loading State (Shows while navigating) --}}
            <div x-show="isNavigating" x-cloak class="absolute inset-0 p-8 z-50 bg-[#f8fafc]">
                <div class="max-w-7xl mx-auto space-y-8">
                    <div class="h-10 w-1/4 skeleton rounded-xl"></div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="h-32 skeleton rounded-[2rem]"></div>
                        <div class="h-32 skeleton rounded-[2rem]"></div>
                        <div class="h-32 skeleton rounded-[2rem]"></div>
                        <div class="h-32 skeleton rounded-[2rem]"></div>
                    </div>
                    <div class="h-96 skeleton rounded-[2.5rem]"></div>
                </div>
            </div>

            {{-- Actual Blade View Content --}}
            <div class="w-full min-w-0 h-full" x-show="!isNavigating">
                @yield('content')
            </div>
        </main>

        {{-- Micro Telemetry Footer (NEW FUN!) --}}
        <div class="h-6 bg-slate-900 border-t border-slate-800 flex items-center justify-between px-4 shrink-0 z-50 no-print" :class="zenMode ? 'hidden' : ''">
            <div class="flex items-center gap-4 text-[9px] font-mono text-slate-500">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-code-branch text-indigo-500"></i> Main</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> Laravel {{ app()->version() }}</span>
            </div>
            <div class="flex items-center gap-4 text-[9px] font-mono text-slate-500">
                <span class="hidden sm:inline-block">Session: {{ session()->getId() ? substr(session()->getId(), 0, 8) : 'Guest' }}</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.8)]"></span> DB: <span x-text="networkPing"></span>ms</span>
            </div>
        </div>

    </div>

    {{-- ====================================================================== --}}
    {{-- OVERLAYS, MODALS & GLOBAL TOOLS                                        --}}
    {{-- ====================================================================== --}}

    {{-- 1. NODE INSPECTOR DRAWER --}}
    <div x-show="inspectorOpen" x-cloak class="fixed inset-0 z-[600] flex justify-end pointer-events-none no-print">
        <div x-show="inspectorOpen" x-transition.opacity.duration.300ms @click="toggleInspector()" class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm pointer-events-auto"></div>
        
        <div x-show="inspectorOpen" 
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-sm bg-white h-full shadow-2xl flex flex-col pointer-events-auto border-l border-slate-200" @click.stop>
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-sm font-black text-slate-900 tracking-tight uppercase flex items-center gap-2"><i class="fa-solid fa-sliders text-indigo-500"></i> Node Inspector</span>
                <button @click="toggleInspector()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors focus:outline-none shadow-sm"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8">
                {{-- UI Density --}}
                <div>
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Workspace Mode</h5>
                    <div class="flex p-1 bg-slate-50 border border-slate-200 rounded-xl">
                        <button @click="zenMode = false" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all" :class="!zenMode ? 'bg-white shadow-sm text-slate-900 border border-slate-200' : 'text-slate-500 hover:text-slate-900'">Standard</button>
                        <button @click="zenMode = true" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-2" :class="zenMode ? 'bg-white shadow-sm text-indigo-600 border border-slate-200' : 'text-slate-500 hover:text-slate-900'">Zen <span class="text-[8px] bg-slate-200 px-1 rounded text-slate-500">⇧Z</span></button>
                    </div>
                </div>

                {{-- API Key Block --}}
                <div>
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 flex items-center justify-between">
                        REST API Key <span class="px-2 py-0.5 bg-rose-50 text-rose-500 rounded text-[8px]">Secret</span>
                    </h5>
                    <div class="relative group">
                        <input type="password" value="sk_live_9f8g7h6j5k4l3m2n1b" readonly class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-mono text-slate-900 outline-none select-all cursor-text">
                        <button @click="copyApiKey()" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 shadow-sm transition-colors"><i class="fa-regular fa-copy text-[10px]"></i></button>
                    </div>
                </div>

                {{-- Active Sessions --}}
                <div>
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Active Network Sessions</h5>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                            <i class="fa-solid fa-laptop text-indigo-500 text-lg"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">MacBook Pro (Current)</p>
                                <p class="text-[9px] text-slate-500 font-mono mt-0.5">IP: 192.168.1.1</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.8)]"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. COMMAND PALETTE (CMD+K) WITH KEYBOARD NAVIGATION --}}
    <div x-show="commandPalette" x-cloak class="fixed inset-0 z-[1000] p-4 sm:p-6 md:p-20 flex items-start justify-center no-print">
        <div x-show="commandPalette" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="toggleCommandPalette()"></div>
        
        <div x-show="commandPalette" 
             x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 scale-95 -translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
             class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden border border-slate-200 flex flex-col transform-gpu"
             @click.stop x-trap.noscroll="commandPalette"
             @keydown.arrow-down.prevent="cmdIndex = (cmdIndex + 1) % filteredActions.length"
             @keydown.arrow-up.prevent="cmdIndex = (cmdIndex - 1 + filteredActions.length) % filteredActions.length"
             @keydown.enter.prevent="executeCommand()">
            
            <div class="relative flex items-center p-4 border-b border-slate-100 bg-slate-50/50">
                <i class="fa-solid fa-magnifying-glass text-indigo-500 ml-4 text-xl"></i>
                <input type="text" x-model="searchQuery" x-ref="searchInput" placeholder="Search commands, ledgers, or settings..." 
                       class="w-full bg-transparent border-none px-5 py-4 text-xl font-bold text-slate-900 placeholder-slate-400 focus:ring-0 focus:outline-none">
                <kbd class="hidden sm:inline-block px-2.5 py-1.5 rounded-lg bg-slate-200 border border-slate-300 text-xs font-mono text-slate-500 shadow-inner mr-2">ESC</kbd>
            </div>

            <div class="max-h-[60vh] overflow-y-auto p-3 bg-white" id="cmdPaletteList">
                <div class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400" x-text="searchQuery === '' ? 'Recent Telemetry' : 'Search Results'"></div>
                
                <template x-for="(action, index) in filteredActions" :key="action.name">
                    <a :href="action.route" 
                       @click.prevent="executeCommand(index)"
                       @mouseenter="cmdIndex = index; playHoverSound()" 
                       class="flex items-center gap-4 px-4 py-3 rounded-[1.5rem] transition-colors group mb-1 border"
                       :class="cmdIndex === index ? 'bg-indigo-50 border-indigo-100' : 'hover:bg-slate-50 text-slate-700 border-transparent'">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center border shadow-sm shrink-0 transition-colors" 
                             :class="cmdIndex === index ? `bg-${action.color}-500 text-white border-${action.color}-600` : `bg-${action.color}-50 text-${action.color}-600 border-${action.color}-100`">
                            <i class="fa-solid text-lg" :class="action.icon"></i>
                        </div>
                        <div>
                            <span class="block font-black text-sm transition-colors" :class="cmdIndex === index ? 'text-indigo-700' : 'text-slate-900'" x-text="action.name"></span>
                            <span class="block text-[10px] font-bold uppercase tracking-widest mt-0.5" :class="cmdIndex === index ? 'text-indigo-400' : 'text-slate-400'" x-text="action.context"></span>
                        </div>
                        <div class="ml-auto hidden sm:flex items-center gap-1 transition-opacity" :class="cmdIndex === index ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'">
                            <template x-if="action.shortcut">
                                <div class="flex gap-1">
                                    <template x-for="key in action.shortcut.split('+')">
                                        <kbd class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px] font-mono text-slate-500 shadow-sm" x-text="key.trim()"></kbd>
                                    </template>
                                </div>
                            </template>
                            <span x-show="!action.shortcut" class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mr-2">Press Enter</span>
                            <i class="fa-solid fa-arrow-right ml-2 text-indigo-500"></i>
                        </div>
                    </a>
                </template>

                <div x-show="filteredActions.length === 0" class="py-12 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center border border-slate-100 mb-4 shadow-inner"><i class="fa-solid fa-ghost text-slate-300 text-2xl"></i></div>
                    <p class="text-sm font-bold text-slate-500">No telemetry matches found.</p>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                <span><i class="fa-solid fa-arrow-up-down mr-1 text-slate-300"></i> Navigate</span>
                <span><i class="fa-solid fa-reply mr-1 text-slate-300"></i> Execute Node</span>
            </div>
        </div>
    </div>

    {{-- 3. KEYBOARD SHORTCUTS HELP MODAL (Shift + ?) --}}
    <div x-show="helpModalOpen" x-cloak class="fixed inset-0 z-[2000] p-4 sm:p-6 flex items-center justify-center no-print">
        <div x-show="helpModalOpen" x-transition.opacity.duration.300ms class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="helpModalOpen = false"></div>
        <div x-show="helpModalOpen" 
             x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg glass-panel rounded-[2rem] shadow-[0_30px_100px_-15px_rgba(0,0,0,0.5)] border border-slate-200 overflow-hidden z-10" @click.stop>
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center"><i class="fa-regular fa-keyboard"></i></div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Command Glossary</h3>
                </div>
                <button @click="helpModalOpen = false" class="text-slate-400 hover:text-slate-700 focus:outline-none"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="text-sm font-bold text-slate-600">Command Palette</span>
                    <div class="flex gap-1"><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">⌘</kbd><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">K</kbd></div>
                </div>
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="text-sm font-bold text-slate-600">Zen Mode (Focus)</span>
                    <div class="flex gap-1"><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">Shift</kbd><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">Z</kbd></div>
                </div>
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="text-sm font-bold text-slate-600">Node Inspector</span>
                    <div class="flex gap-1"><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">⌘</kbd><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">I</kbd></div>
                </div>
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="text-sm font-bold text-slate-600">Record Capital Inflow</span>
                    <div class="flex gap-1"><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">Shift</kbd><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">I</kbd></div>
                </div>
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="text-sm font-bold text-slate-600">Record Capital Outflow</span>
                    <div class="flex gap-1"><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">Shift</kbd><kbd class="px-2 py-1 bg-slate-100 border border-slate-200 rounded-md text-xs font-mono text-slate-600">E</kbd></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. GLOBAL TOAST NOTIFICATION --}}
    <div x-show="toast.show" x-cloak 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         @notify.window="triggerGlobalToast($event.detail)"
         class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[3000] no-print">
        <div class="bg-slate-900/95 backdrop-blur-xl text-white px-6 py-4 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-4 border border-slate-700 max-w-sm w-max">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border" :class="toast.type === 'success' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : (toast.type === 'error' ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' : 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30')">
                <i class="fa-solid text-lg" :class="toast.type === 'success' ? 'fa-check' : (toast.type === 'error' ? 'fa-triangle-exclamation' : 'fa-info')"></i>
            </div>
            <div>
                <h4 class="text-[10px] font-black uppercase tracking-widest mb-0.5" :class="toast.type === 'success' ? 'text-emerald-500' : (toast.type === 'error' ? 'text-rose-500' : 'text-indigo-400')" x-text="toast.type === 'success' ? 'Success' : (toast.type === 'error' ? 'Error' : 'System Notice')"></h4>
                <span class="text-sm font-bold tracking-wide leading-tight text-slate-100" x-text="toast.message"></span>
            </div>
        </div>
    </div>

    {{-- ================= CORE ALPINE ENGINE & AUDIO CONTEXT ================= --}}
    <script>
        // 🚨 CRITICAL FIX: Global Web Audio API Engine with Suspension Resolution
        window.audioEngine = {
            ctx: null,
            init() {
                if(!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if(AudioContext) this.ctx = new AudioContext();
                }
                if (this.ctx && this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
            },
            playHover() {
                if(!this.ctx || this.ctx.state !== 'running') return;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.connect(gain); gain.connect(this.ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(400, this.ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(600, this.ctx.currentTime + 0.05);
                gain.gain.setValueAtTime(0.01, this.ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
                osc.start(); osc.stop(this.ctx.currentTime + 0.05);
            },
            playClick() {
                if(!this.ctx || this.ctx.state !== 'running') return;
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.connect(gain); gain.connect(this.ctx.destination);
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(800, this.ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, this.ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.05, this.ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.1);
                osc.start(); osc.stop(this.ctx.currentTime + 0.1);
            }
        };

        window.playHoverSound = () => window.audioEngine.playHover();
        window.playClickSound = () => window.audioEngine.playClick();
        
        // Initialize audio engine on first user interaction to bypass browser autoplay blocks
        document.body.addEventListener('click', () => window.audioEngine.init(), { once: true });
        document.body.addEventListener('keydown', () => window.audioEngine.init(), { once: true });

        document.addEventListener('alpine:init', () => {
            Alpine.data('globalArchitecture', () => ({
                isLoaded: false,
                isNavigating: false,
                isOnline: navigator.onLine,
                sidebarOpen: false,
                isScrolled: false,
                scrollProgress: 0,
                zenMode: false,
                
                networkPing: 12, 
                
                // Feature States
                commandPalette: false,
                inspectorOpen: false,
                helpModalOpen: false,
                searchQuery: '',
                cmdIndex: 0,
                
                // Command Palette Data
                actions: [
                    { name: 'Record Capital Inflow', context: 'Log Income', route: '{{ Route::has("user.incomes.create") ? route("user.incomes.create") : "#" }}', icon: 'fa-arrow-trend-up', color: 'emerald', shortcut: 'Shift+I' },
                    { name: 'Record Capital Outflow', context: 'Log Expense', route: '{{ Route::has("user.expenses.create") ? route("user.expenses.create") : "#" }}', icon: 'fa-arrow-trend-down', color: 'rose', shortcut: 'Shift+E' },
                    { name: 'Global Command Dashboard', context: 'Home Overview', route: '{{ Route::has("user.dashboard") ? route("user.dashboard") : "#" }}', icon: 'fa-chart-pie', color: 'indigo', shortcut: 'Cmd+D' },
                    { name: 'Identity & Security Profile', context: 'Settings', route: '{{ Route::has("user.profile.index") ? route("user.profile.index") : "#" }}', icon: 'fa-fingerprint', color: 'slate', shortcut: 'Cmd+,' },
                ],
                get filteredActions() {
                    if (this.searchQuery === '') return this.actions;
                    return this.actions.filter(a => a.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || a.context.toLowerCase().includes(this.searchQuery.toLowerCase()));
                },
                
                // Global Toast State
                toast: { show: false, message: '', type: 'info', timeout: null },

                init() {
                    // Network Listeners
                    window.addEventListener('online', () => { this.isOnline = true; this.triggerGlobalToast({message: 'Network restored.', type: 'success'}); });
                    window.addEventListener('offline', () => { this.isOnline = false; });

                    // Drop loader when DOM is ready
                    window.addEventListener('load', () => {
                        setTimeout(() => { this.isLoaded = true; }, 400);
                    });

                    // Responsive Sidebar Logic
                    this.sidebarOpen = window.innerWidth >= 1024;
                    window.addEventListener('resize', () => {
                        if(!this.zenMode) this.sidebarOpen = window.innerWidth >= 1024;
                    });

                    // Network Ping Simulator
                    setInterval(() => {
                        this.networkPing = Math.floor(Math.random() * (18 - 10 + 1) + 10);
                    }, 3000);

                    // Reset Command Palette Index on Search
                    this.$watch('searchQuery', () => { this.cmdIndex = 0; });
                },

                checkScroll(e) {
                    const el = e.target;
                    this.isScrolled = el.scrollTop > 10;
                    const scrollHeight = el.scrollHeight - el.clientHeight;
                    this.scrollProgress = scrollHeight > 0 ? (el.scrollTop / scrollHeight) * 100 : 0;
                },

                toggleCommandPalette() {
                    this.commandPalette = !this.commandPalette;
                    this.searchQuery = ''; 
                    this.cmdIndex = 0;
                    if(this.commandPalette) {
                        window.playClickSound();
                        setTimeout(() => this.$refs.searchInput.focus(), 100);
                    }
                },

                executeCommand(index = null) {
                    let targetIndex = index !== null ? index : this.cmdIndex;
                    let action = this.filteredActions[targetIndex];
                    if(action && action.route !== '#') {
                        this.toggleCommandPalette();
                        this.simulateNavigation();
                        window.location.href = action.route;
                    }
                },

                toggleInspector() {
                    this.inspectorOpen = !this.inspectorOpen;
                    window.playClickSound();
                },

                toggleZenMode() {
                    this.zenMode = !this.zenMode;
                    this.triggerGlobalToast({ message: this.zenMode ? 'Focus Mode Activated' : 'Standard Mode Restored', type: 'success' });
                    window.playClickSound();
                },

                copyApiKey() {
                    navigator.clipboard.writeText('sk_live_9f8g7h6j5k4l3m2n1b');
                    this.triggerGlobalToast({ message: 'API Key copied to clipboard.', type: 'success' });
                    window.playClickSound();
                },

                triggerGlobalToast(detail) {
                    this.toast.message = detail.message || 'System updated.';
                    this.toast.type = detail.type || 'info';
                    this.toast.show = true;
                    
                    if(this.toast.timeout) clearTimeout(this.toast.timeout);
                    this.toast.timeout = setTimeout(() => { this.toast.show = false; }, 4000);
                },

                // Simulated Fast SPA Navigation (Smooth blur out)
                simulateNavigation() {
                    window.playClickSound();
                    this.isNavigating = true;
                },

                // Hotkey Engine Triggers
                triggerQuickAction(type) {
                    if(this.commandPalette || this.inspectorOpen || this.helpModalOpen || document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') return;
                    
                    let targetUrl = '#';
                    if (type === 'income') {
                        targetUrl = '{{ Route::has("user.incomes.create") ? route("user.incomes.create") : "" }}';
                    } else if (type === 'expense') {
                        targetUrl = '{{ Route::has("user.expenses.create") ? route("user.expenses.create") : "" }}';
                    }
                    
                    if(targetUrl && targetUrl !== '#') {
                        this.simulateNavigation();
                        window.location.href = targetUrl;
                    }
                }
            }));
        });
    </script>

    @stack('scripts')

</body>
</html>