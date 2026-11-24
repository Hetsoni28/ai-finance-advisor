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
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    {{-- One request for every public-site typeface (Inter, Instrument Serif accents, JetBrains Mono labels). --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Compiled Tailwind (npm run css). Version query busts the CDN cache when the file changes. --}}
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ @filemtime(public_path('css/site.css')) }}">

    {{-- Alpine plugins MUST load before Alpine core, otherwise x-collapse / x-intersect / x-trap are never registered. --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

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
      :class="(mobileMenuOpen || cmdKOpen) ? 'overflow-hidden' : ''"
      @notify.window="showToast($event.detail.message, $event.detail.type)">

    @stack('before-content')

    @php
        $current = Route::currentRouteName();
        $productLinks = [
            ['fa-wallet', 'Income & expenses', 'Track every rupee by category', route('features')],
            ['fa-heart-pulse', 'Health score', 'See how stable your finances are', route('features')],
            ['fa-people-roof', 'Family hub', 'Share a budget with your family', route('features')],
            ['fa-robot', 'AI advisor', 'Answers based on your own numbers', route('home') . '#ai'],
            ['fa-chart-line', 'Market snapshot', 'Crypto and gold prices in ₹', route('features')],
            ['fa-file-pdf', 'Reports & PDFs', 'Trends, breakdowns and exports', route('features')],
        ];
        $companyLinks = array_filter([
            Route::has('about') ? ['About us', route('about')] : null,
            Route::has('careers') ? ['Careers', route('careers')] : null,
            Route::has('blog') ? ['Blog', route('blog')] : null,
            Route::has('changelog') ? ['Changelog', route('changelog')] : null,
        ]);
    @endphp

    {{-- Skip link for keyboard users --}}
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[2000] focus:px-4 focus:py-2 focus:rounded-lg focus:bg-white focus:text-indigo-700 focus:font-bold focus:shadow-lg">Skip to content</a>

    {{-- Announcement bar --}}
    <div x-show="showPromo" x-collapse class="relative z-[510] bg-gradient-to-r from-indigo-600 to-violet-600 text-white">
        <div class="max-w-7xl mx-auto px-10 sm:px-6 lg:px-8 py-2.5 flex items-center justify-center text-center">
            <p class="text-xs sm:text-sm font-medium">
                <span class="hidden sm:inline font-bold mr-1">New</span>
                <span class="hidden sm:inline opacity-50 mr-1">&middot;</span>
                Download your income &amp; expense reports as PDF.
                <a href="{{ route('features') }}" class="font-bold underline underline-offset-4 decoration-white/40 hover:decoration-white ml-1 whitespace-nowrap">Learn more <span aria-hidden="true">&rarr;</span></a>
            </p>
            <button @click="dismissPromo()" class="absolute right-2 sm:right-4 w-7 h-7 rounded-md flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Dismiss announcement">
                <i class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    {{-- Main navigation --}}
    <header class="sticky top-0 z-[500] w-full transition-all duration-300"
            :class="isScrolled ? 'bg-white/85 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_8px_30px_rgba(15,23,42,0.05)]' : 'bg-transparent border-b border-transparent'">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-[72px] flex items-center justify-between gap-4" aria-label="Main">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 rounded-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200" aria-label="FinanceAI home">
                <span class="h-9 w-9 rounded-[10px] flex items-center justify-center text-white bg-gradient-to-br from-indigo-500 to-violet-600 shadow-md shadow-indigo-500/30">
                    <i class="fa-solid fa-cube" aria-hidden="true"></i>
                </span>
                <span class="text-xl font-black text-slate-900 tracking-tight">Finance<span class="text-indigo-600">AI</span></span>
            </a>

            {{-- Desktop menu --}}
            <ul class="hidden lg:flex items-center gap-1">
                {{-- Product mega menu --}}
                <li class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="true"
                            class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 {{ $current === 'features' ? 'text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}"
                            :class="open && 'text-slate-900 bg-slate-100/70'">
                        Product
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open && 'rotate-180'" aria-hidden="true"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute left-1/2 -translate-x-1/2 top-full pt-3 w-[40rem]">
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-slate-900/10 overflow-hidden">
                            <div class="grid grid-cols-2 gap-1 p-3">
                                @foreach($productLinks as [$icon, $title, $desc, $href])
                                    <a href="{{ $href }}" class="group flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none focus-visible:bg-slate-50">
                                        <span class="w-10 h-10 shrink-0 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-gradient-to-br group-hover:from-indigo-600 group-hover:to-violet-600 group-hover:text-white flex items-center justify-center transition-colors">
                                            <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                                        </span>
                                        <span>
                                            <span class="block text-sm font-bold text-slate-900">{{ $title }}</span>
                                            <span class="block text-xs text-slate-500 mt-0.5">{{ $desc }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between px-6 py-3.5 bg-slate-50 border-t border-slate-100">
                                <span class="text-xs text-slate-500">Free Starter plan &middot; No card needed</span>
                                <a href="{{ route('features') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">All features <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <a href="{{ route('pricing') }}" class="block px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 {{ $current === 'pricing' ? 'text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}" @if($current === 'pricing') aria-current="page" @endif>Pricing</a>
                </li>

                {{-- Company dropdown --}}
                @if(count($companyLinks))
                <li class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false">
                    <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="true"
                            class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 {{ in_array($current, ['about','careers','blog','changelog']) ? 'text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}"
                            :class="open && 'text-slate-900 bg-slate-100/70'">
                        Company
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="open && 'rotate-180'" aria-hidden="true"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute left-0 top-full pt-3 w-56">
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-slate-900/10 p-2">
                            @foreach($companyLinks as [$label, $href])
                                <a href="{{ $href }}" class="block px-3.5 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-700 transition-colors focus:outline-none focus-visible:bg-slate-50">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                </li>
                @endif

                <li>
                    <a href="{{ route('contact') }}" class="block px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 {{ $current === 'contact' ? 'text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}" @if($current === 'contact') aria-current="page" @endif>Contact</a>
                </li>
            </ul>

            {{-- Desktop actions --}}
            <div class="hidden lg:flex items-center gap-2 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-md shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300">
                        Dashboard <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-100/70 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-md shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300">
                        Get started <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button type="button" @click="mobileMenuOpen = true" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300" aria-label="Open menu" :aria-expanded="mobileMenuOpen">
                <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
            </button>
        </nav>
    </header>

    {{-- Mobile menu --}}
    <div x-show="mobileMenuOpen" x-cloak class="lg:hidden fixed inset-0 z-[600]" role="dialog" aria-modal="true" aria-label="Menu" @keydown.escape.window="mobileMenuOpen = false">
        <div x-show="mobileMenuOpen" x-transition.opacity.duration.200ms @click="mobileMenuOpen = false" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute right-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col"
             x-trap.noscroll="mobileMenuOpen">

            <div class="h-16 px-4 flex items-center justify-between border-b border-slate-100">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="h-9 w-9 rounded-[10px] flex items-center justify-center text-white bg-gradient-to-br from-indigo-500 to-violet-600"><i class="fa-solid fa-cube" aria-hidden="true"></i></span>
                    <span class="text-xl font-black text-slate-900 tracking-tight">Finance<span class="text-indigo-600">AI</span></span>
                </a>
                <button type="button" @click="mobileMenuOpen = false" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300" aria-label="Close menu">
                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-4" x-data="{ section: 'product' }">
                {{-- Product --}}
                <button type="button" @click="section = section === 'product' ? null : 'product'" :aria-expanded="section === 'product'" class="w-full flex items-center justify-between px-3 py-3.5 rounded-xl text-base font-bold text-slate-900 hover:bg-slate-50">
                    Product <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="section === 'product' && 'rotate-180'" aria-hidden="true"></i>
                </button>
                <div x-show="section === 'product'" x-collapse>
                    <div class="pb-2 space-y-1">
                        @foreach($productLinks as [$icon, $title, $desc, $href])
                            <a href="{{ $href }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                                <span class="w-9 h-9 shrink-0 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid {{ $icon }} text-sm" aria-hidden="true"></i></span>
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">{{ $title }}</span>
                                    <span class="block text-xs text-slate-500">{{ $desc }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('pricing') }}" class="block px-3 py-3.5 rounded-xl text-base font-bold text-slate-900 hover:bg-slate-50">Pricing</a>

                @if(count($companyLinks))
                <button type="button" @click="section = section === 'company' ? null : 'company'" :aria-expanded="section === 'company'" class="w-full flex items-center justify-between px-3 py-3.5 rounded-xl text-base font-bold text-slate-900 hover:bg-slate-50">
                    Company <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="section === 'company' && 'rotate-180'" aria-hidden="true"></i>
                </button>
                <div x-show="section === 'company'" x-collapse>
                    <div class="pb-2 pl-3 space-y-1">
                        @foreach($companyLinks as [$label, $href])
                            <a href="{{ $href }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <a href="{{ route('contact') }}" class="block px-3 py-3.5 rounded-xl text-base font-bold text-slate-900 hover:bg-slate-50">Contact</a>
            </div>

            <div class="p-4 border-t border-slate-100 space-y-2.5">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-md shadow-indigo-500/30">
                        Go to dashboard <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-md shadow-indigo-500/30">
                        Get started free <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('login') }}" class="flex items-center justify-center w-full py-3.5 rounded-xl text-sm font-bold text-slate-800 bg-white border border-slate-200 hover:bg-slate-50">Sign in</a>
                @endauth
            </div>
        </div>
    </div>

    <main id="main" class="flex-1 w-full flex flex-col relative z-10">
        {{-- Note: No top padding here so pages can have full-bleed hero sections. --}}
        @yield('content')
    </main>

    <footer class="relative z-10 mt-auto bg-slate-950 text-slate-400 overflow-hidden" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/60 to-transparent" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- CTA strip (hidden on the home page, which ends with its own call to action) --}}
            @unless(request()->routeIs('home'))
            <div class="py-10 sm:py-12 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/10">
                <div>
                    <p class="text-xl sm:text-2xl font-black text-white tracking-tight">Ready to take control of your money?</p>
                    <p class="mt-1 text-sm text-slate-400">Start free. No card needed.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-900/40 hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/50">
                            Go to dashboard <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-br from-indigo-600 to-violet-600 shadow-lg shadow-indigo-900/40 hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/50">
                            Create free account <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex justify-center items-center px-5 py-3 rounded-xl text-sm font-bold text-white border border-white/15 hover:bg-white/5 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/20">
                            Talk to us
                        </a>
                    @endauth
                </div>
            </div>
            @endunless

            {{-- Main footer --}}
            <div class="py-14 sm:py-16 grid lg:grid-cols-12 gap-12 lg:gap-8">

                {{-- Brand + support --}}
                <div class="lg:col-span-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/40" aria-label="FinanceAI home">
                        <span class="h-10 w-10 rounded-[11px] flex items-center justify-center text-white bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-900/50"><i class="fa-solid fa-cube" aria-hidden="true"></i></span>
                        <span class="text-2xl font-black text-white tracking-tight">Finance<span class="text-indigo-400">AI</span></span>
                    </a>
                    <p class="mt-5 text-[15px] leading-relaxed max-w-sm">
                        Personal and family finance, made simple. Track every rupee, share a budget, and get answers that fit your numbers.
                    </p>

                    <a href="{{ route('contact') }}" class="group mt-8 flex items-center gap-4 max-w-sm rounded-2xl border border-white/10 bg-white/[0.03] p-4 hover:border-indigo-400/40 hover:bg-white/[0.05] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                        <span class="w-11 h-11 shrink-0 rounded-xl bg-indigo-500/15 text-indigo-300 flex items-center justify-center"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                        <span class="flex-1">
                            <span class="block text-sm font-bold text-white">Questions or feedback?</span>
                            <span class="block text-xs text-slate-400 mt-0.5">Send us a message through the contact form.</span>
                        </span>
                        <i class="fa-solid fa-arrow-right text-sm text-slate-500 group-hover:text-white group-hover:translate-x-0.5 transition-all" aria-hidden="true"></i>
                    </a>
                </div>

                {{-- Link columns --}}
                @php
                    $footerColumns = [
                        'Product' => [
                            ['Features', route('features')],
                            ['Pricing', route('pricing')],
                            ['How it works', route('home') . '#how'],
                            ['AI advisor', route('home') . '#ai'],
                            Route::has('changelog') ? ['Changelog', route('changelog')] : null,
                        ],
                        'Company' => [
                            Route::has('about') ? ['About us', route('about')] : null,
                            Route::has('careers') ? ['Careers', route('careers')] : null,
                            Route::has('blog') ? ['Blog', route('blog')] : null,
                            ['Contact', route('contact')],
                        ],
                        'Support' => array_merge(
                            [['FAQ', route('home') . '#faq'], ['Contact support', route('contact')]],
                            auth()->check()
                                ? [['Dashboard', route('dashboard')], ['Your profile', route('user.profile.index')]]
                                : [['Sign in', route('login')], ['Create account', route('register')]]
                        ),
                        'Legal' => [
                            Route::has('privacy') ? ['Privacy policy', route('privacy')] : null,
                            Route::has('terms') ? ['Terms of service', route('terms')] : null,
                            Route::has('dpa') ? ['Data processing', route('dpa')] : null,
                            Route::has('soc2') ? ['Security', route('soc2')] : null,
                        ],
                    ];
                @endphp
                <nav class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-10" aria-label="Footer">
                    @foreach($footerColumns as $heading => $links)
                        <div>
                            <h3 class="font-mono text-[11px] uppercase tracking-[0.18em] text-slate-500">{{ $heading }}</h3>
                            <ul class="mt-5 space-y-3.5 text-[15px]">
                                @foreach(array_filter($links) as [$label, $href])
                                    <li>
                                        <a href="{{ $href }}" class="group inline-flex items-center gap-1.5 text-slate-300 hover:text-white transition-colors focus:outline-none focus-visible:text-white focus-visible:underline">
                                            {{ $label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </div>

            {{-- Wordmark --}}
            <div class="relative select-none pointer-events-none" aria-hidden="true">
                <p class="text-center font-black tracking-[-0.06em] leading-[0.8] text-[22vw] lg:text-[17rem] bg-gradient-to-b from-white/[0.09] to-white/0 bg-clip-text text-transparent -mb-[0.12em]">FinanceAI</p>
            </div>

            {{-- Bottom bar --}}
            <div class="relative py-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
                <p class="text-slate-500">&copy; {{ date('Y') }} FinanceAI. All rights reserved.</p>
                <ul class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-slate-400">
                    @if(Route::has('privacy'))<li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy</a></li>@endif
                    @if(Route::has('terms'))<li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms</a></li>@endif
                    <li class="flex items-center gap-2 text-slate-500"><i class="fa-solid fa-indian-rupee-sign text-indigo-400 text-xs" aria-hidden="true"></i>Made for India</li>
                </ul>
                <button type="button" @click="scrollToTop()" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors focus:outline-none focus-visible:text-white">
                    Back to top <span class="w-8 h-8 rounded-full border border-white/15 flex items-center justify-center"><i class="fa-solid fa-arrow-up text-xs" aria-hidden="true"></i></span>
                </button>
            </div>
        </div>
    </footer>

    <div x-show="showCookie" x-cloak class="fixed bottom-3 left-3 right-3 sm:right-auto sm:bottom-6 sm:left-6 z-[2000] sm:max-w-sm" role="region" aria-label="Cookie notice"
         x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10">
        <div class="bg-white/95 backdrop-blur-xl border border-slate-200 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.25)] p-4 flex items-center gap-3">
            <i class="fa-solid fa-cookie-bite text-indigo-600 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-xs sm:text-sm text-slate-600 leading-snug">
                We only use essential cookies to keep you signed in.
                <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}" class="font-semibold text-indigo-600 hover:underline">Privacy</a>
            </p>
            <button @click="acceptCookies()" class="shrink-0 px-4 py-2 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 text-white text-xs font-bold hover:opacity-90 transition-opacity focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">OK</button>
        </div>
    </div>

    <button @click="scrollToTop()" 
            x-show="showScrollTop" 
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10"
            :class="showCookie ? 'bottom-24 sm:bottom-8' : 'bottom-5 sm:bottom-8'"
            class="fixed right-5 sm:right-8 w-12 h-12 rounded-full bg-slate-900 text-white shadow-[0_10px_30px_rgba(0,0,0,0.2)] flex items-center justify-center hover:bg-indigo-600 hover:-translate-y-1 transition-all z-[90] focus:outline-none animate-float-subtle border border-slate-700"
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