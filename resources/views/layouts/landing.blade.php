<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased scroll-smooth selection:bg-indigo-500 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'FinanceAI | Enterprise Financial Intelligence'))</title>
    <meta name="description" content="@yield('meta_description', 'FinanceAI empowers families and professionals with real-time insights, AI-driven forecasting, and structured financial growth systems.')">

    <meta property="og:title" content="@yield('title', config('app.name', 'FinanceAI'))">
    <meta property="og:description" content="@yield('meta_description', 'FinanceAI SaaS Platform')">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/og-image.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
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

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        /* 🌐 ENTERPRISE LIGHT WHITE SCROLLBAR */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* 🧊 DYNAMIC GLASSMORPHIC UTILITIES */
        .nav-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        /* ⚡ HARDWARE ACCELERATED ANIMATIONS & UTILS */
        [x-cloak] { display: none !important; }
        .transform-gpu { transform: translateZ(0); }
        .text-balance { text-wrap: balance; }
        
        /* Float Animation for Back-to-top Button */
        @keyframes float-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .animate-float-subtle { animation: float-subtle 4s ease-in-out infinite; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800 font-sans relative overflow-x-hidden flex flex-col min-h-screen" 
      x-data="landingEngine()" 
      @scroll.window="handleScroll()"
      @keydown.window.prevent.cmd.k="cmdKOpen = true"
      @keydown.window.prevent.ctrl.k="cmdKOpen = true"
      :class="(mobileMenuOpen || cmdKOpen) ? 'overflow-hidden' : ''"
      @notify.window="showToast($event.detail.message, $event.detail.type)">

    <div x-show="!isLoaded" x-transition.opacity.duration.600ms class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center">
        <div class="relative w-20 h-20 flex items-center justify-center mb-6">
            <div class="absolute inset-0 border-4 border-indigo-50 rounded-[1.5rem] shadow-inner"></div>
            <div class="absolute inset-0 border-4 border-indigo-600 rounded-[1.5rem] border-t-transparent animate-spin"></div>
            <i class="fa-solid fa-cube text-indigo-600 text-xl animate-pulse shadow-sm"></i>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 animate-pulse">Initializing Cryptography</p>
    </div>

    <div class="fixed top-0 left-0 h-1 bg-indigo-600 z-[1000] transition-all duration-150 shadow-[0_0_10px_rgba(79,70,229,0.8)]" :style="`width: ${scrollProgress}%`"></div>

    <div x-show="showPromo" x-collapse class="relative z-[900] bg-slate-900 text-white overflow-hidden border-b border-slate-800">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-10 flex items-center justify-center relative">
            <p class="text-[11px] font-bold tracking-widest uppercase flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span> 
                <span>FinanceAI Neural Engine 3.0 is now live.</span>
                <a href="{{ route('features') ?? '#' }}" class="text-indigo-400 hover:text-indigo-300 transition-colors ml-2 underline decoration-indigo-500/50 underline-offset-4 hidden sm:inline-block">View Capabilities &rarr;</a>
            </p>
            <button @click="dismissPromo()" class="absolute right-4 text-slate-400 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <nav class="sticky top-0 w-full z-[500] transition-all duration-300 transform-gpu"
         :class="isScrolled ? 'nav-glass py-2' : 'bg-transparent py-4 border-b border-transparent'">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="{{ route('home') ?? url('/') }}" class="flex items-center gap-3 group focus:outline-none">
                <div class="h-10 w-10 rounded-[12px] flex items-center justify-center text-indigo-600 transition-all duration-300 relative overflow-hidden"
                     :class="isScrolled ? 'bg-indigo-50 border border-indigo-100' : 'bg-white shadow-md border border-slate-100'">
                    <i class="fa-solid fa-cube text-lg relative z-10 group-hover:scale-110 transition-transform"></i>
                </div>
                <span class="text-2xl font-black text-slate-900 tracking-tight leading-none">Finance<span class="text-indigo-600">AI</span></span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex items-center gap-1">
                @php $current = Route::currentRouteName(); @endphp

                @foreach([
                    'features' => 'Features',
                    'pricing' => 'Pricing',
                    'about' => 'Company',
                    'contact' => 'Contact'
                ] as $route => $label)
                    @if(Route::has($route))
                        <a href="{{ route($route) }}" 
                           class="px-4 py-2 rounded-xl text-sm font-bold transition-colors focus:outline-none {{ $current == $route ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Right Actions (Search, Auth & Mobile Toggle) --}}
            <div class="flex items-center gap-3 sm:gap-4">
                
                {{-- Cmd+K Search Trigger (NEW FUN) --}}
                <button @click="cmdKOpen = true" class="hidden md:flex items-center gap-3 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 hover:bg-white hover:border-indigo-300 hover:text-indigo-600 transition-all shadow-inner focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span class="text-xs font-bold">Search...</span>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-sm">⌘K</span>
                </button>

                {{-- Desktop Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3 border-l border-slate-200 pl-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md hover:bg-indigo-600 hover:shadow-indigo-500/30 transition-all hover:-translate-y-0.5 focus:outline-none flex items-center gap-2">
                            Enter App <i class="fa-solid fa-arrow-right opacity-70"></i>
                        </a>
                    @else
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors focus:outline-none">
                                Sign In
                            </a>
                        @endif
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md shadow-indigo-500/20 hover:bg-indigo-700 hover:shadow-indigo-500/40 transition-all hover:-translate-y-0.5 focus:outline-none">
                                Start Free
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- Mobile Menu Trigger --}}
                <button @click="mobileMenuOpen = true" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:text-indigo-600 focus:outline-none transition-colors">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

        </div>
    </nav>

    <div x-show="cmdKOpen" x-cloak class="fixed inset-0 z-[1000] overflow-y-auto p-4 pt-[10vh] sm:p-20 md:p-[15vh]">
        {{-- Backdrop --}}
        <div x-show="cmdKOpen" x-transition.opacity.duration.300ms @click="cmdKOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        
        {{-- Palette Modal --}}
        <div x-show="cmdKOpen" 
             x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="mx-auto max-w-xl transform overflow-hidden rounded-[2rem] bg-white border border-slate-200 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] relative z-10"
             @click.stop x-trap.noscroll="cmdKOpen">
            
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-indigo-500 text-lg pointer-events-none"></i>
                <input type="text" placeholder="Search functionality, pricing, or support..." class="w-full bg-transparent border-0 border-b border-slate-100 pl-16 pr-6 py-6 text-lg font-bold text-slate-900 placeholder-slate-400 focus:ring-0 outline-none">
                <button @click="cmdKOpen = false" class="absolute right-6 top-1/2 -translate-y-1/2 px-2 py-1 bg-slate-100 rounded text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">ESC</button>
            </div>

            <div class="max-h-80 overflow-y-auto p-4 space-y-6">
                {{-- Group 1 --}}
                <div>
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-4 mb-2">Quick Navigation</h5>
                    <ul class="space-y-1">
                        @if(Route::has('features'))
                        <li><a href="{{ route('features') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 font-bold transition-colors group"><i class="fa-solid fa-layer-group w-5 text-center text-slate-400 group-hover:text-indigo-500"></i> Platform Features</a></li>
                        @endif
                        @if(Route::has('pricing'))
                        <li><a href="{{ route('pricing') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 font-bold transition-colors group"><i class="fa-solid fa-tags w-5 text-center text-slate-400 group-hover:text-indigo-500"></i> Pricing & Quotas</a></li>
                        @endif
                    </ul>
                </div>
                {{-- Group 2 --}}
                <div>
                    <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 px-4 mb-2">Legal & Compliance</h5>
                    <ul class="space-y-1">
                        @if(Route::has('privacy'))
                        <li><a href="{{ route('privacy') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-bold transition-colors group"><i class="fa-solid fa-shield-halved w-5 text-center text-slate-400 group-hover:text-slate-600"></i> Privacy Policy</a></li>
                        @endif
                        @if(Route::has('terms'))
                        <li><a href="{{ route('terms') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-bold transition-colors group"><i class="fa-solid fa-file-contract w-5 text-center text-slate-400 group-hover:text-slate-600"></i> Terms of Service</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" x-cloak class="lg:hidden fixed inset-0 z-[600] flex justify-end">
        {{-- Backdrop Overlay --}}
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false" 
             class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        
        {{-- Slide-out Drawer --}}
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-sm bg-white h-full shadow-2xl flex flex-col"
             @click.stop x-trap.noscroll="mobileMenuOpen">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-xl font-black text-slate-900 tracking-tight leading-none flex items-center gap-2"><i class="fa-solid fa-cube text-indigo-600"></i> FinanceAI</span>
                <button @click="mobileMenuOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-colors focus:outline-none shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-2">
                @foreach([
                    'features' => ['label' => 'Features', 'icon' => 'fa-layer-group'],
                    'pricing' => ['label' => 'Pricing', 'icon' => 'fa-tags'],
                    'about' => ['label' => 'Company', 'icon' => 'fa-building'],
                    'contact' => ['label' => 'Contact Support', 'icon' => 'fa-envelope']
                ] as $route => $data)
                    @if(Route::has($route))
                        <a href="{{ route($route) }}" class="flex items-center gap-4 px-4 py-4 rounded-2xl hover:bg-slate-50 text-slate-700 font-bold transition-colors border border-transparent hover:border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0"><i class="fa-solid {{ $data['icon'] }} text-xs"></i></div>
                            {{ $data['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50 space-y-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full flex items-center justify-center py-4 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md hover:bg-indigo-600 transition-colors">
                        Enter Application
                    </a>
                @else
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-3.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 shadow-sm">
                            Sign In
                        </a>
                    @endif
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center py-4 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-colors">
                            Start Free Trial
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <main class="flex-1 w-full flex flex-col relative z-10">
        {{-- Note: No top padding here so pages can have full-bleed hero sections. --}}
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 pt-20 pb-10 mt-auto relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10 lg:gap-8 mb-16">
                
                {{-- Brand Column --}}
                <div class="col-span-2 lg:col-span-2 pr-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-6 focus:outline-none">
                        <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20"><i class="fa-solid fa-cube text-base"></i></div>
                        <span class="text-2xl font-black text-slate-900 tracking-tight">Finance<span class="text-indigo-600">AI</span></span>
                    </a>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-sm mb-8">
                        Enterprise-grade financial intelligence platform engineered for modern professionals, strict compliance officers, and secure family hubs.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition-all shadow-sm focus:outline-none"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition-all shadow-sm focus:outline-none"><i class="fa-brands fa-github"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition-all shadow-sm focus:outline-none"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>

                {{-- Links 1 --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6 flex items-center gap-2"><i class="fa-solid fa-layer-group text-slate-300"></i> Product</h4>
                    <ul class="space-y-4 text-sm font-medium text-slate-500">
                        @if(Route::has('features'))<li><a href="{{ route('features') }}" class="hover:text-indigo-600 transition-colors">Features & Tools</a></li>@endif
                        @if(Route::has('pricing'))<li><a href="{{ route('pricing') }}" class="hover:text-indigo-600 transition-colors">Pricing & Quotas</a></li>@endif
                        @if(Route::has('changelog'))<li><a href="{{ route('changelog') }}" class="hover:text-indigo-600 transition-colors">System Changelog</a></li>@endif
                        <li><a href="#" class="hover:text-indigo-600 transition-colors flex items-center gap-2">API Documentation <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a></li>
                    </ul>
                </div>

                {{-- Links 2 --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6 flex items-center gap-2"><i class="fa-solid fa-building text-slate-300"></i> Company</h4>
                    <ul class="space-y-4 text-sm font-medium text-slate-500">
                        @if(Route::has('about'))<li><a href="{{ route('about') }}" class="hover:text-indigo-600 transition-colors">About FinanceAI</a></li>@endif
                        @if(Route::has('careers'))<li><a href="{{ route('careers') }}" class="hover:text-indigo-600 transition-colors">Careers & Hiring</a></li>@endif
                        @if(Route::has('blog'))<li><a href="{{ route('blog') }}" class="hover:text-indigo-600 transition-colors">Engineering Blog</a></li>@endif
                        @if(Route::has('contact'))<li><a href="{{ route('contact') }}" class="hover:text-indigo-600 transition-colors">Contact Architecture</a></li>@endif
                    </ul>
                </div>

                {{-- Links 3 --}}
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6 flex items-center gap-2"><i class="fa-solid fa-scale-balanced text-slate-300"></i> Legal</h4>
                    <ul class="space-y-4 text-sm font-medium text-slate-500">
                        @if(Route::has('privacy'))<li><a href="{{ route('privacy') }}" class="hover:text-indigo-600 transition-colors">Privacy Policy</a></li>@endif
                        @if(Route::has('terms'))<li><a href="{{ route('terms') }}" class="hover:text-indigo-600 transition-colors">Terms of Service</a></li>@endif
                        @if(Route::has('dpa'))<li><a href="{{ route('dpa') }}" class="hover:text-indigo-600 transition-colors">Data Processing Addendum</a></li>@endif
                        @if(Route::has('soc2'))<li><a href="{{ route('soc2') }}" class="hover:text-indigo-600 transition-colors">SOC2 Compliance</a></li>@endif
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs font-bold text-slate-400">
                    &copy; {{ date('Y') }} FinanceAI Technologies Inc. All rights mathematically reserved.
                </p>
                <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full shadow-inner">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">All Systems Operational</span>
                </div>
            </div>
        </div>
    </footer>

    <div x-show="showCookie" x-cloak class="fixed bottom-6 left-6 z-[2000] max-w-sm w-[calc(100%-3rem)]"
         x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10">
        <div class="bg-white/90 backdrop-blur-xl border border-slate-200 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.2)] p-5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100"><i class="fa-solid fa-cookie-bite"></i></div>
                <div>
                    <h5 class="text-sm font-black text-slate-900 mb-1">Telemetry Protocol</h5>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4">We deploy minimal session cookies strictly to enhance infrastructure routing and authenticated state. No third-party data selling.</p>
                    <div class="flex items-center gap-3">
                        <button @click="acceptCookies()" class="px-5 py-2 bg-slate-900 text-white rounded-lg font-black text-[10px] uppercase tracking-widest shadow-md hover:bg-indigo-600 transition-colors focus:outline-none">Acknowledge</button>
                        <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600 transition-colors">Read Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button @click="scrollToTop()" 
            x-show="showScrollTop" 
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10"
            class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-slate-900 text-white shadow-[0_10px_30px_rgba(0,0,0,0.2)] flex items-center justify-center hover:bg-indigo-600 hover:-translate-y-1 transition-all z-[90] focus:outline-none animate-float-subtle border border-slate-700"
            aria-label="Scroll to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[3000]" 
         x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         style="display: none;">
        <div class="bg-slate-900/95 backdrop-blur-md text-white px-6 py-3.5 rounded-full shadow-[0_20px_40px_-15px_rgba(0,0,0,0.3)] flex items-center gap-3 border border-slate-700 max-w-sm w-max">
            <i class="fa-solid" :class="toast.type === 'success' ? 'fa-circle-check text-emerald-400' : (toast.type === 'error' ? 'fa-triangle-exclamation text-rose-400' : 'fa-circle-info text-sky-400')"></i>
            <span class="text-sm font-bold tracking-wide leading-tight truncate" x-text="toast.message"></span>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landingEngine', () => ({
                isLoaded: false,
                isScrolled: false,
                scrollProgress: 0,
                mobileMenuOpen: false,
                showScrollTop: false,
                cmdKOpen: false,
                
                // localStorage states
                showPromo: true,
                showCookie: false,
                
                toast: { show: false, message: '', type: 'info' },

                init() {
                    // Check local storage for banner dismissals
                    if(localStorage.getItem('financeai_promo_dismissed')) {
                        this.showPromo = false;
                    }
                    if(!localStorage.getItem('financeai_cookie_accepted')) {
                        setTimeout(() => { this.showCookie = true; }, 2000); // Delayed entry
                    }

                    // Drop loader when DOM is ready
                    window.addEventListener('load', () => {
                        setTimeout(() => { this.isLoaded = true; }, 300);
                        this.handleScroll(); // Initial check
                    });
                },

                handleScroll() {
                    const scrollTop = window.scrollY || document.documentElement.scrollTop;
                    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                    
                    // Navbar glassmorphism trigger (Accounts for Promo Banner height)
                    this.isScrolled = scrollTop > (this.showPromo ? 40 : 10);
                    
                    // Show back-to-top button
                    this.showScrollTop = scrollTop > 500;

                    // Update Top Progress Bar
                    this.scrollProgress = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                showToast(message, type = 'info') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                dismissPromo() {
                    this.showPromo = false;
                    localStorage.setItem('financeai_promo_dismissed', 'true');
                    this.handleScroll(); // Recalculate nav state
                },

                acceptCookies() {
                    this.showCookie = false;
                    localStorage.setItem('financeai_cookie_accepted', 'true');
                }
            }));
        });
    </script>

    @stack('scripts')

</body>
</html>