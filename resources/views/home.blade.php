@extends('layouts.landing')

@section('title', 'FinanceAI - Elite Financial Intelligence & Automation')
@section('meta_description', 'Enterprise-grade AI financial analytics natively built on Laravel 8 and strict MySQL. Automate your wealth scaling today.')

@section('content')

{{-- ================= 0. CUSTOM CURSOR, CMD+K & TOASTS ================= --}}
<div id="custom-cursor" class="fixed w-8 h-8 border-2 border-indigo-500 rounded-full pointer-events-none z-[9999] transition-transform duration-100 ease-out transform -translate-x-1/2 -translate-y-1/2 flex items-center justify-center mix-blend-screen opacity-0 hidden md:flex">
    <div class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></div>
</div>

<div id="cmdk-modal" class="fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-xl hidden flex-col items-center pt-32 px-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-3xl bg-slate-900 border border-slate-700 rounded-3xl shadow-[0_0_150px_rgba(79,70,229,0.3)] overflow-hidden transform scale-95 transition-transform duration-300" id="cmdk-content">
        <div class="flex items-center px-6 py-5 border-b border-slate-800 bg-slate-900/50">
            <svg class="w-6 h-6 text-indigo-500 mr-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="cmdk-input" placeholder="Search docs, execute commands, or find transactions..." class="w-full bg-transparent text-white focus:outline-none text-xl placeholder-slate-600 font-mono">
            <button onclick="closeCmdK()" class="px-3 py-1.5 bg-slate-800 rounded-lg text-xs font-bold text-slate-400 border border-slate-700 hover:bg-slate-700 hover:text-white transition-colors tracking-widest">ESC</button>
        </div>
        <div class="p-4 max-h-[60vh] overflow-y-auto" id="cmdk-results">
            <div class="px-4 py-3 text-xs font-black text-indigo-500 uppercase tracking-widest">System Actions</div>
            <a href="{{ route('register') ?? '#' }}" class="flex items-center justify-between px-6 py-4 hover:bg-indigo-600/20 border border-transparent hover:border-indigo-500/50 rounded-2xl group transition-all mb-2">
                <div class="flex items-center gap-4"><div class="p-2 bg-slate-800 group-hover:bg-indigo-500 rounded-lg transition-colors"><svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></div><span class="text-slate-300 group-hover:text-white font-bold text-lg">Initialize New Account</span></div>
                <kbd class="hidden md:block px-3 py-1 bg-slate-800 rounded-md text-xs text-slate-400 font-mono">↵ Enter</kbd>
            </a>
            <button onclick="triggerKonami()" class="w-full flex items-center justify-between px-6 py-4 hover:bg-emerald-600/20 border border-transparent hover:border-emerald-500/50 rounded-2xl group transition-all mb-4">
                <div class="flex items-center gap-4"><div class="p-2 bg-slate-800 group-hover:bg-emerald-500 rounded-lg transition-colors"><svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg></div><span class="text-slate-300 group-hover:text-emerald-400 font-bold text-lg">Execute Override Protocol (Easter Egg)</span></div>
                <span class="text-xs text-slate-500 group-hover:text-emerald-400 font-mono">System Command</span>
            </button>

            <div class="px-4 py-3 text-xs font-black text-indigo-500 uppercase tracking-widest border-t border-slate-800/50 mt-2 pt-6">Recent Context Searches</div>
            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-800/50 rounded-2xl cursor-pointer transition-colors">
                <div class="flex items-center gap-4"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-slate-400 font-medium">GTU Student Pro Tier Requirements</span></div>
            </div>
            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-800/50 rounded-2xl cursor-pointer transition-colors">
                <div class="flex items-center gap-4"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-slate-400 font-medium">BCA Sem 6 Laravel MVC Structure</span></div>
            </div>
        </div>
    </div>
</div>

{{-- ================= 1. HERO SECTION (Dynamic Greeting + Particles + 3D) ================= --}}
<section class="relative min-h-[100vh] flex items-center pt-32 pb-20 overflow-hidden bg-slate-950 text-white" id="hero" onmouseenter="showCustomCursor()" onmouseleave="hideCustomCursor()">
    {{-- Dynamic Ambient Deep Space Glow --}}
    <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[1000px] h-[1000px] bg-indigo-600/20 rounded-full blur-[180px] animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[1000px] h-[1000px] bg-pink-600/10 rounded-full blur-[180px] animate-float delay-2000"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-[0.15] mix-blend-overlay"></div>
        {{-- Scanline overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiLz4KPC9zdmc+')] opacity-20"></div>
    </div>

    {{-- Interactive AI Particle Network (Canvas) --}}
    <canvas id="particleCanvas" class="absolute inset-0 z-0 opacity-50 pointer-events-none"></canvas>

    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-12 gap-16 items-center relative z-10">
        
        {{-- Left Content --}}
        <div class="lg:col-span-6 space-y-8" data-aos="fade-right" data-aos-duration="1000">
            <button onclick="openCmdK()" class="inline-flex items-center gap-3 px-5 py-2.5 bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 rounded-full text-sm font-bold shadow-[0_0_30px_rgba(79,70,229,0.2)] backdrop-blur-xl hover:bg-indigo-500/20 transition-all cursor-none group">
                <svg class="w-4 h-4 text-indigo-400 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span id="dynamic-greeting">System Online</span> — Press <kbd class="px-2 py-0.5 bg-indigo-900/50 rounded text-xs border border-indigo-500/30 ml-1 font-mono tracking-widest text-white">⌘K</kbd>
            </button>

            <h1 class="text-6xl md:text-7xl lg:text-8xl font-black leading-[1.02] tracking-tighter">
                Financial Logic, <br>
                <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent typewriter-effect relative pb-2 inline-block">
                    Automated.
                    <span class="absolute right-[-16px] top-2 bottom-3 w-[6px] bg-pink-400 animate-blink rounded-full"></span>
                </span>
            </h1>

            <p class="text-xl text-slate-400 font-medium max-w-xl leading-relaxed">
                Engineered for Ahmedabad's fastest-growing startups and families. Let our Machine Learning models strictly categorize, forecast, and map your financial future.
            </p>

            <div class="flex flex-col sm:flex-row flex-wrap gap-5 pt-6 relative z-20">
                <a href="{{ route('register') ?? '#' }}" class="magnetic-target group relative px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-[0_0_40px_rgba(79,70,229,0.5)] hover:shadow-[0_0_60px_rgba(79,70,229,0.8)] transition-all duration-300 flex items-center justify-center gap-3 overflow-hidden cursor-none">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    Initialize Engine
                    <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                
                <button onclick="openDemoModal()" class="magnetic-target px-10 py-5 border-2 border-slate-700 bg-slate-800/50 backdrop-blur-md text-white rounded-2xl font-bold text-lg hover:bg-slate-800 hover:border-indigo-500 transition-all flex items-center justify-center gap-4 group cursor-none">
                    <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center group-hover:bg-indigo-500 transition-colors shadow-[0_0_15px_rgba(79,70,229,0.3)] group-hover:shadow-[0_0_30px_rgba(79,70,229,0.8)]">
                        <svg class="w-5 h-5 text-indigo-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                    </div>
                    Watch Architecture Tour
                </button>
            </div>
            
            <div class="flex items-center gap-8 pt-8 text-sm font-bold text-slate-500">
                <span class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 shadow-inner"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> MySQL 8 Strict Mode</span>
                <span class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 shadow-inner"><img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-5 h-5 opacity-80" alt="Laravel"> Laravel Native</span>
            </div>
        </div>

        {{-- Right Content (3D Complex Mouse Tracking Mockup) --}}
        <div class="lg:col-span-6 relative h-[600px] md:h-[750px] perspective-1000" id="hero3D" data-aos="zoom-in-up" data-aos-duration="1400">
            
            
            {{-- Main App Image Mockup --}}
            <div class="tilt-card absolute right-0 top-1/2 -translate-y-1/2 w-full max-w-2xl bg-[#0a0f1c] border border-slate-700/80 rounded-[2.5rem] shadow-[0_40px_100px_rgba(0,0,0,0.9)] overflow-hidden z-10 transition-transform duration-200 ease-out cursor-none">
                <div class="bg-[#1e293b] px-6 py-4 flex items-center gap-3 border-b border-slate-700 shadow-md">
                    <div class="flex gap-2.5">
                        <div class="w-3.5 h-3.5 rounded-full bg-[#ff5f56] shadow-inner"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-[#ffbd2e] shadow-inner"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-[#27c93f] shadow-inner"></div>
                    </div>
                    <div class="mx-auto text-xs text-slate-400 font-mono tracking-widest flex items-center gap-2 bg-slate-900 px-4 py-1.5 rounded-md border border-slate-700">
                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        finance-ai.engine/analytics
                    </div>
                </div>
                
                {{-- Decorative App Image --}}
                <div class="relative h-64 w-full border-b border-slate-800">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover opacity-40 mix-blend-luminosity pointer-events-none" alt="Financial Software">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-[#0a0f1c]/80 to-transparent"></div>
                </div>

                {{-- Integrated Glowing Chart --}}
                <div class="p-8 bg-[#0a0f1c] relative -mt-24">
                    <div class="flex justify-between items-end mb-6 relative z-10">
                        <div>
                            <h3 class="text-slate-400 font-bold text-sm uppercase tracking-widest mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.8)]"></span> 
                                Live System Output
                            </h3>
                            <div class="text-6xl font-black text-white tracking-tighter">₹1,240,500</div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm font-black shadow-[0_0_25px_rgba(52,211,153,0.2)] flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                +23.5%
                            </span>
                        </div>
                    </div>
                    <div class="h-56 w-full relative z-10 mt-6 pointer-events-none">
                        <canvas id="heroNeonChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Floating UI Pill 1 (Expense Mapping) --}}
            <div class="absolute top-[18%] -left-16 z-20 bg-slate-900/95 backdrop-blur-2xl border border-slate-700 p-5 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.8)] animate-floatSlow flex items-center gap-5 cursor-none">
                <div class="w-14 h-14 rounded-2xl bg-pink-500/20 flex items-center justify-center text-pink-400 border border-pink-500/30 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Crazy Chat Corner</p>
                    <p class="text-white font-black text-2xl">-₹150</p>
                </div>
            </div>

            {{-- Floating UI Pill 2 (Security/User Context) --}}
            <div class="absolute bottom-[12%] -right-12 z-30 bg-slate-900/95 backdrop-blur-2xl border border-slate-700 p-5 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.8)] animate-floatSlow cursor-none" style="animation-delay: 2.5s;">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">GTU Database</p>
                        <p class="text-emerald-400 font-black text-base drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]">Encrypted & Validated</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 2. TRIPLE INFINITE MARQUEE (TRUSTED STACK) ================= --}}
<div class="border-y border-slate-800 bg-[#020617] py-14 overflow-hidden flex flex-col items-center relative z-20">
    <div class="absolute inset-0 bg-gradient-to-r from-[#020617] via-transparent to-[#020617] w-full z-10 pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-[300px] bg-indigo-600/5 rounded-full blur-[100px] pointer-events-none"></div>
    
    {{-- Row 1: Moving Left (Backend) --}}
    <div class="relative w-full max-w-[100vw] overflow-hidden mb-10 flex">
        <div class="flex whitespace-nowrap animate-marquee items-center gap-20 md:gap-40 opacity-40 hover:opacity-100 transition-opacity duration-500">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-4 group">
                    <svg class="w-12 h-12 text-slate-400 group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L1.604 6v12L12 24l10.396-6V6L12 0zm0 2.227l8.473 4.89v9.766L12 21.773l-8.473-4.89V7.117L12 2.227z"/></svg>
                    <h3 class="text-4xl font-black text-slate-400 group-hover:text-white transition-colors">AWS CLOUD</h3>
                </div>
                <div class="flex items-center gap-4 group">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-12 h-12 grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all" alt="Laravel">
                    <h3 class="text-4xl font-black text-slate-400 group-hover:text-white transition-colors">LARAVEL 8</h3>
                </div>
                <div class="flex items-center gap-4 group">
                    <svg class="w-12 h-12 text-slate-400 group-hover:text-[#6366f1] transition-colors" viewBox="0 0 24 24" fill="currentColor"><path d="M11.998 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm-.213 6.945c1.92 0 3.398 1.258 3.525 3.036h-2.148c-.068-.69-.738-1.258-1.572-1.258-.87 0-1.536.488-1.536 1.15 0 .61.43 1.01 1.488 1.258l1.413.333c1.782.42 2.766 1.422 2.766 3.047 0 1.953-1.636 3.35-3.805 3.35-2.106 0-3.766-1.378-3.92-3.32h2.24c.092.83.843 1.488 1.83 1.488 1.056 0 1.688-.518 1.688-1.218 0-.66-.452-1.1-1.635-1.35l-1.25-.264c-1.802-.38-2.628-1.423-2.628-2.95 0-1.896 1.583-3.292 3.544-3.292z"/></svg>
                    <h3 class="text-4xl font-black text-slate-400 group-hover:text-white transition-colors">STRIPE API</h3>
                </div>
                <div class="flex items-center gap-4 group">
                    <h3 class="text-4xl font-black text-slate-400 group-hover:text-[#F29111] transition-colors">MYSQL 8.0 STRICT</h3>
                </div>
            @endfor
        </div>
    </div>

    {{-- Row 2: Moving Right (Frontend & Infra) --}}
    <div class="relative w-full max-w-[100vw] overflow-hidden mb-10 flex">
        <div class="flex whitespace-nowrap animate-marquee-reverse items-center gap-20 md:gap-40 opacity-30 hover:opacity-100 transition-opacity duration-500">
            @for ($i = 0; $i < 4; $i++)
                <h3 class="text-4xl font-black text-slate-500 hover:text-white transition-colors">REACT FRONTEND</h3>
                <h3 class="text-4xl font-black text-slate-500 hover:text-[#38bdf8] transition-colors">TAILWINDCSS V3</h3>
                <h3 class="text-4xl font-black text-slate-500 hover:text-[#777BB4] transition-colors">PHP 8.2 ENGINE</h3>
                <h3 class="text-4xl font-black text-slate-500 hover:text-[#2496ED] transition-colors">DOCKER CONTAINERS</h3>
            @endfor
        </div>
    </div>
    
    {{-- Row 3: Moving Left Fast (Data Science) --}}
    <div class="relative w-full max-w-[100vw] overflow-hidden flex">
        <div class="flex whitespace-nowrap animate-marquee-fast items-center gap-20 md:gap-40 opacity-20 hover:opacity-100 transition-opacity duration-500">
            @for ($i = 0; $i < 4; $i++)
                <h3 class="text-2xl font-bold text-slate-600 hover:text-emerald-400 transition-colors tracking-widest uppercase">Machine Learning</h3>
                <h3 class="text-2xl font-bold text-slate-600 hover:text-emerald-400 transition-colors tracking-widest uppercase">Predictive Models</h3>
                <h3 class="text-2xl font-bold text-slate-600 hover:text-emerald-400 transition-colors tracking-widest uppercase">Anomaly Detection</h3>
                <h3 class="text-2xl font-bold text-slate-600 hover:text-emerald-400 transition-colors tracking-widest uppercase">Data Cleansing</h3>
            @endfor
        </div>
    </div>
</div>

{{-- ================= 3. DEVELOPER API EXPERIENCE (Working Tabs) ================= --}}
<section class="py-32 bg-[#050b14] relative border-b border-slate-800 overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[1000px] bg-indigo-600/5 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-20 items-center relative z-10">
        
        <div data-aos="fade-right">
            <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-8 leading-[1.1]">Engineered for <br><span class="text-indigo-400">Developers.</span></h2>
            <p class="text-slate-400 text-xl font-medium leading-relaxed mb-12">
                Push data to your financial dashboard directly from your own applications. Our REST API is built on Laravel, protected by strict Sanctum token authentication.
            </p>
            
            <div class="space-y-8">
                <div class="flex items-start gap-5 group">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800/50 border border-slate-700 flex items-center justify-center text-indigo-400 flex-shrink-0 mt-1 shadow-lg group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-white mb-2">RESTful Architecture</h4>
                        <p class="text-slate-400 text-lg leading-relaxed">Standardized JSON responses, predictable resource URIs, and comprehensive pagination out of the box.</p>
                    </div>
                </div>
                <div class="flex items-start gap-5 group">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800/50 border border-slate-700 flex items-center justify-center text-emerald-400 flex-shrink-0 mt-1 shadow-lg group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-white mb-2">Strict Data Validation</h4>
                        <p class="text-slate-400 text-lg leading-relaxed">Every API request is scrubbed through Laravel FormRequests to prevent bad data from reaching the MySQL kernel.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Interactive Code Editor Mockup --}}
        <div class="bg-[#020617] rounded-[2.5rem] border border-slate-700 shadow-[0_40px_100px_rgba(0,0,0,0.8)] overflow-hidden" data-aos="fade-left">
            <div class="flex items-center justify-between px-6 bg-[#0f172a] border-b border-slate-800">
                <div class="flex gap-2.5 py-5">
                    <div class="w-3.5 h-3.5 rounded-full bg-rose-500"></div>
                    <div class="w-3.5 h-3.5 rounded-full bg-amber-500"></div>
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500"></div>
                </div>
                {{-- Dynamic Tabs --}}
                <div class="flex h-full mt-2">
                    <button class="api-tab active px-8 py-4 text-sm font-bold font-mono text-indigo-400 bg-[#020617] border-t-2 border-indigo-500 transition-all rounded-t-lg" data-target="code-php">PHP (Laravel)</button>
                    <button class="api-tab px-8 py-4 text-sm font-bold font-mono text-slate-500 hover:text-slate-300 transition-all border-t-2 border-transparent" data-target="code-curl">cURL</button>
                    <button class="api-tab px-8 py-4 text-sm font-bold font-mono text-slate-500 hover:text-slate-300 transition-all border-t-2 border-transparent" data-target="code-python">Python</button>
                </div>
            </div>
            
            <div class="p-8 overflow-x-auto text-base font-mono leading-loose relative min-h-[380px]">
                {{-- PHP Snippet --}}
                <div id="code-php" class="api-code-block block transition-opacity duration-300 opacity-100">
                    <span class="text-pink-400">$response</span> <span class="text-white">=</span> <span class="text-indigo-300">Http</span><span class="text-white">::</span><span class="text-emerald-300">withToken</span><span class="text-white">(</span><span class="text-amber-300">'API_SECRET'</span><span class="text-white">)</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-white">-></span><span class="text-emerald-300">post</span><span class="text-white">(</span><span class="text-amber-300">'https://api.financeai.com/v1/transactions'</span><span class="text-white">, [</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">'amount'</span> <span class="text-pink-400">=></span> <span class="text-purple-300">380.00</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">'type'</span> <span class="text-pink-400">=></span> <span class="text-amber-300">'expense'</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">'description'</span> <span class="text-pink-400">=></span> <span class="text-amber-300">'BGMI UC Sync'</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">'auto_categorize'</span> <span class="text-pink-400">=></span> <span class="text-indigo-400">true</span><br>
                    <span class="text-white">]);</span><br><br>
                    <span class="text-slate-500">// Returns:</span><br>
                    <span class="text-white">{</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-emerald-300">"status"</span><span class="text-white">: </span><span class="text-amber-300">"success"</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-emerald-300">"category_assigned"</span><span class="text-white">: </span><span class="text-amber-300">"Gaming & Entertainment"</span><br>
                    <span class="text-white">}</span>
                </div>

                {{-- cURL Snippet --}}
                <div id="code-curl" class="api-code-block hidden opacity-0 transition-opacity duration-300 absolute top-8 left-8">
                    <span class="text-indigo-300">curl</span> <span class="text-pink-400">-X</span> POST https://api.financeai.com/v1/transactions \<br>
                    &nbsp;&nbsp;<span class="text-pink-400">-H</span> <span class="text-amber-300">"Authorization: Bearer API_SECRET"</span> \<br>
                    &nbsp;&nbsp;<span class="text-pink-400">-H</span> <span class="text-amber-300">"Content-Type: application/json"</span> \<br>
                    &nbsp;&nbsp;<span class="text-pink-400">-d</span> <span class="text-amber-300">'{</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"amount": 150.00,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"type": "expense",</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"description": "Crazy Chat Corner - Snacks"</span><br>
                    &nbsp;&nbsp;<span class="text-amber-300">}'</span>
                </div>

                {{-- Python Snippet --}}
                <div id="code-python" class="api-code-block hidden opacity-0 transition-opacity duration-300 absolute top-8 left-8">
                    <span class="text-pink-400">import</span> <span class="text-white">requests</span><br><br>
                    <span class="text-white">url = </span><span class="text-amber-300">"https://api.financeai.com/v1/transactions"</span><br>
                    <span class="text-white">headers = {</span><span class="text-amber-300">"Authorization"</span><span class="text-white">: </span><span class="text-amber-300">"Bearer API_SECRET"</span><span class="text-white">}</span><br>
                    <span class="text-white">data = {</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"amount"</span><span class="text-white">: </span><span class="text-purple-300">15000</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"type"</span><span class="text-white">: </span><span class="text-amber-300">"income"</span><span class="text-white">,</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-amber-300">"description"</span><span class="text-white">: </span><span class="text-amber-300">"GTU Scholarship"</span><br>
                    <span class="text-white">}</span><br><br>
                    <span class="text-white">response = requests.</span><span class="text-emerald-300">post</span><span class="text-white">(url, json=data, headers=headers)</span><br>
                    <span class="text-indigo-300">print</span><span class="text-white">(response.</span><span class="text-emerald-300">json</span><span class="text-white">())</span>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ================= 4. SMART KPI SNAPSHOT ================= --}}
<section class="py-28 bg-slate-50 dark:bg-[#0b1121] relative border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight">Your Command Center</h2>
            <p class="text-slate-500 mt-6 text-xl max-w-2xl mx-auto">Live data feeds driven by uncompromising strict-mode queries. Say goodbye to spreadsheet errors.</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            @php
                $kpis = [
                    ['label'=>'Total Inflow','value'=>$totalIncome ?? 1450000,'color'=>'text-emerald-600 dark:text-emerald-400', 'bg'=>'bg-emerald-50 dark:bg-emerald-900/20', 'border'=>'border-emerald-100 dark:border-emerald-900/50', 'icon'=>'M12 4v16m8-8H4', 'trend'=>'+14.2%', 'sparkline'=>'10,40 20,30 30,50 40,20 50,30 60,10 70,20'],
                    ['label'=>'Total Outflow','value'=>$totalExpense ?? 480000,'color'=>'text-rose-600 dark:text-rose-400', 'bg'=>'bg-rose-50 dark:bg-rose-900/20', 'border'=>'border-rose-100 dark:border-rose-900/50', 'icon'=>'M20 12H4', 'trend'=>'-2.1%', 'sparkline'=>'10,20 20,10 30,30 40,20 50,40 60,30 70,50'],
                    ['label'=>'Net Asset Value','value'=>$netBalance ?? 970000,'color'=>'text-indigo-600 dark:text-indigo-400', 'bg'=>'bg-indigo-50 dark:bg-indigo-900/20', 'border'=>'border-indigo-100 dark:border-indigo-900/50', 'icon'=>'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3', 'trend'=>'Stable', 'sparkline'=>'10,40 20,35 30,30 40,25 50,20 60,15 70,10'],
                ];
            @endphp

            @foreach($kpis as $kpi)
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border {{ $kpi['border'] }} shadow-[0_10px_40px_rgba(0,0,0,0.05)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] hover:-translate-y-3 transition-all duration-500 group kpi-card-trigger relative overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                
                {{-- Background Sparkline SVG --}}
                <svg class="absolute bottom-0 right-0 w-full h-32 opacity-[0.04] group-hover:opacity-[0.12] transition-opacity duration-500 {{ $kpi['color'] }}" viewBox="0 0 100 60" preserveAspectRatio="none">
                    <polyline fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $kpi['sparkline'] }}"></polyline>
                </svg>

                <div class="flex justify-between items-start mb-8 relative z-10">
                    <div class="w-16 h-16 {{ $kpi['bg'] }} {{ $kpi['color'] }} rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"></path>
                        </svg>
                    </div>
                    <span class="px-4 py-1.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">{{ $kpi['trend'] }}</span>
                </div>
                <h3 class="text-sm font-bold uppercase text-slate-500 dark:text-slate-400 tracking-widest mb-3 relative z-10">{{ $kpi['label'] }}</h3>
                <p class="text-6xl font-black text-slate-900 dark:text-white flex items-center gap-1 relative z-10">
                    <span class="text-4xl text-slate-300 dark:text-slate-600">₹</span>
                    <span class="counter" data-target="{{ $kpi['value'] }}">0</span>
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= 5. INTERACTIVE VANILLA JS ROI CALCULATOR ================= --}}
<section class="py-32 bg-white dark:bg-slate-950 relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-600/5 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-20 items-center relative z-10">
        
        <div data-aos="fade-right">
            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">Calculate your <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">AI Advantage.</span></h2>
            <p class="text-xl text-slate-500 mb-10 font-medium leading-relaxed">Stop guessing your runway. Use our predictive engine to see exactly how much money and time our AI auto-categorization saves you annually.</p>
            
            <div class="space-y-12 bg-slate-50 dark:bg-slate-900/50 p-10 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-inner">
                {{-- Input 1 --}}
                <div class="relative">
                    <div class="flex justify-between text-base font-bold text-slate-700 dark:text-slate-300 mb-6">
                        <label>Monthly Transactions</label>
                        <span class="text-indigo-600 dark:text-indigo-400 text-xl px-4 py-1.5 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700" id="txValue">150</span>
                    </div>
                    <input type="range" id="txSlider" min="10" max="1000" value="150" class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-indigo-600 outline-none hover:bg-indigo-100 dark:hover:bg-slate-600 transition-colors">
                </div>
                {{-- Input 2 --}}
                <div class="relative">
                    <div class="flex justify-between text-base font-bold text-slate-700 dark:text-slate-300 mb-6">
                        <label>Current Monthly Expense</label>
                        <span class="text-indigo-600 dark:text-indigo-400 text-xl px-4 py-1.5 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700" id="expValue">₹45,000</span>
                    </div>
                    <input type="range" id="expSlider" min="10000" max="500000" step="5000" value="45000" class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-indigo-600 outline-none hover:bg-indigo-100 dark:hover:bg-slate-600 transition-colors">
                </div>
            </div>
        </div>

        {{-- Calculator Output --}}
        <div class="bg-slate-900 rounded-[3rem] p-14 border border-slate-800 shadow-[0_30px_100px_rgba(0,0,0,0.6)] relative overflow-hidden" data-aos="fade-left">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-transparent pointer-events-none"></div>
            
            <h3 class="text-white font-bold text-2xl mb-12 border-b border-slate-800 pb-8">Projected Annual Impact</h3>
            
            <div class="space-y-12">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-emerald-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">Manual Hours Saved</p>
                    <div class="flex items-end gap-3 relative">
                        <p class="text-7xl font-black text-emerald-400 transition-all duration-300 drop-shadow-[0_0_15px_rgba(52,211,153,0.4)]" id="hoursSaved">54</p>
                        <p class="text-slate-500 mb-3 font-medium text-xl">hrs/year</p>
                    </div>
                </div>

                <div class="relative group">
                    <div class="absolute -inset-4 bg-indigo-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mb-4">AI Optimized Savings</p>
                    <div class="flex items-end gap-3 relative">
                        <p class="text-7xl font-black text-indigo-400 transition-all duration-300 drop-shadow-[0_0_15px_rgba(79,70,229,0.4)]" id="moneySaved">₹81,000</p>
                        <p class="text-slate-500 mb-3 font-medium text-xl">/year</p>
                    </div>
                </div>
            </div>

            <div class="mt-16 pt-10 border-t border-slate-800">
                <a href="{{ route('register') ?? '#' }}" class="magnetic-target w-full flex items-center justify-center gap-3 py-6 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-[0_10px_30px_rgba(79,70,229,0.5)] hover:shadow-[0_20px_40px_rgba(79,70,229,0.8)] hover:-translate-y-1 transition-all cursor-none overflow-hidden group relative">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    Start Saving Now
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ================= 6. ARCHITECTURE / SECURITY DEEP DIVE (NEW MVC SECTION) ================= --}}

<section class="py-32 bg-slate-50 dark:bg-[#050b14] relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-4xl mx-auto mb-24" data-aos="fade-up">
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">Security without compromise.</h2>
            <p class="text-xl text-slate-500 leading-relaxed">Your financial data is your most sensitive asset. We built an architecture that treats it like one, adhering strictly to Model-View-Controller paradigms.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 auto-rows-[380px]">
            
            {{-- Box 1: Storage --}}
            <div class="bento-card md:col-span-2 bg-white dark:bg-slate-900 rounded-[3rem] p-12 border border-slate-200 dark:border-slate-800 relative overflow-hidden group shadow-xl" data-aos="fade-up">
                <div class="glow-effect absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-8 border border-indigo-200 dark:border-indigo-800">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-4">Isolated Storage Clusters</h3>
                    <p class="text-slate-600 dark:text-slate-400 font-medium text-lg leading-relaxed w-5/6">Each account is strictly partitioned. We enforce row-level security so your data physically cannot mix with others.</p>
                </div>
                {{-- Interactive Canvas inside Bento (Data Flow) --}}
                <canvas id="dataFlowCanvas" class="absolute -right-10 -bottom-10 w-96 h-96 opacity-60 mix-blend-screen pointer-events-none transition-transform duration-700 group-hover:scale-110"></canvas>
            </div>

            {{-- Box 2: Encryption --}}
            <div class="md:col-span-2 bg-slate-900 rounded-[3rem] text-white relative overflow-hidden group shadow-[0_20px_60px_rgba(0,0,0,0.6)]" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1639322537228-f710d846310a?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-110 group-hover:rotate-1 transition-transform duration-1000 mix-blend-overlay" alt="Security Lock">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full p-12 z-10 transform group-hover:-translate-y-2 transition-transform duration-500">
                    <h3 class="text-4xl font-black mb-4 text-white">SHA-256 Vaulting</h3>
                    <p class="text-slate-300 font-medium text-xl leading-relaxed">Every transaction is hashed and salted natively via Laravel before it even touches the MySQL disk.</p>
                </div>
            </div>

            {{-- Box 3: API --}}
            <div class="bg-indigo-600 rounded-[3rem] p-12 text-white relative overflow-hidden group shadow-2xl shadow-indigo-500/30" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                <h3 class="text-3xl font-black mb-4 relative z-10">Sanctum Auth</h3>
                <p class="text-indigo-100 font-medium text-lg leading-relaxed relative z-10">API token generation with strict scope limitations.</p>
                <div class="absolute bottom-10 right-10 w-20 h-20 bg-white/20 backdrop-blur-xl rounded-3xl flex items-center justify-center animate-spin-slow shadow-inner">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                </div>
            </div>

            {{-- Box 4: Architecture Skewed Mobile (Scroll Velocity Target) --}}
            <div class="bento-card md:col-span-3 bg-white dark:bg-slate-900 rounded-[3rem] p-12 border border-slate-200 dark:border-slate-800 relative overflow-hidden group flex items-center shadow-xl scroll-skew" data-aos="fade-up" data-aos-delay="300">
                <div class="glow-effect absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                <div class="w-full md:w-[45%] relative z-10">
                    <h3 class="text-4xl font-black text-slate-900 dark:text-white mb-8 leading-tight">100% Mobile Responsive.</h3>
                    <ul class="space-y-6">
                        <li class="flex items-center gap-5 text-slate-600 dark:text-slate-400 font-bold text-lg"><span class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-500 flex items-center justify-center text-base shadow-sm">✓</span> TailwindCSS Layouts</li>
                        <li class="flex items-center gap-5 text-slate-600 dark:text-slate-400 font-bold text-lg"><span class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-500 flex items-center justify-center text-base shadow-sm">✓</span> Vanilla JS Interactivity</li>
                        <li class="flex items-center gap-5 text-slate-600 dark:text-slate-400 font-bold text-lg"><span class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-500 flex items-center justify-center text-base shadow-sm">✓</span> Dynamic Dark Theme</li>
                    </ul>
                </div>
                <div class="hidden md:block absolute -right-12 top-1/2 -translate-y-1/2 w-[60%] h-[140%] perspective-1000">
                    <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover rounded-l-[3rem] shadow-[-30px_0_80px_rgba(0,0,0,0.4)] border-l-[12px] border-y-[12px] border-slate-100 dark:border-slate-800 transform rotate-[-10deg] group-hover:rotate-[-5deg] group-hover:scale-105 transition-all duration-1000 ease-out" alt="Mobile App">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ================= 7. SCROLL SPY FEATURE SHOWCASE ================= --}}
<section class="py-40 bg-white dark:bg-[#030712] relative border-b border-slate-200 dark:border-slate-800 hidden lg:block">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-12 gap-20">
            
            {{-- Sticky Text Left --}}
            <div class="col-span-5 relative">
                <div class="sticky top-40 space-y-40 py-20" id="feature-text-container">
                    
                    {{-- Feature 1 Text --}}
                    <div class="feature-text transition-all duration-500 opacity-100 translate-x-0" data-index="0">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">Database Architecture that never fails.</h3>
                        <p class="text-lg text-slate-500 leading-relaxed font-medium">Built strictly on Laravel 8's Eloquent ORM. We enforce MySQL strict mode to guarantee that every single transaction is accounted for without <code class="text-pink-500 bg-pink-50 dark:bg-pink-500/10 px-2 py-1 rounded text-sm">ONLY_FULL_GROUP_BY</code> data loss.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300 font-bold"><span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-500 flex items-center justify-center text-xs">✓</span> Clean Controller-Service architecture</li>
                            <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300 font-bold"><span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-500 flex items-center justify-center text-xs">✓</span> SHA-256 Encrypted payload storage</li>
                        </ul>
                    </div>

                    {{-- Feature 2 Text --}}
                    <div class="feature-text transition-all duration-500 opacity-20 blur-md translate-x-10" data-index="1">
                        <div class="w-16 h-16 bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400 rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">Predictive AI Modeling.</h3>
                        <p class="text-lg text-slate-500 leading-relaxed font-medium">Stop looking at what you spent yesterday. Our machine learning core analyzes your specific spending habits to project your exact month-end balance. Identify cashflow shortages weeks before they happen.</p>
                    </div>

                    {{-- Feature 3 Text --}}
                    <div class="feature-text transition-all duration-500 opacity-20 blur-md translate-x-10" data-index="2">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">Scale with your family.</h3>
                        <p class="text-lg text-slate-500 leading-relaxed font-medium">Invite your spouse, your accountant, or your business partners. Set specific permission roles, share modular budgets, and build your financial empire collaboratively.</p>
                    </div>

                </div>
            </div>

            {{-- Scrolling Images Right --}}
            <div class="col-span-7 space-y-40 py-20" id="feature-image-container">
                {{-- Image 1 --}}
                <div class="feature-image w-full h-[700px] bg-slate-900 rounded-[3rem] p-6 border border-slate-800 shadow-[0_40px_100px_rgba(0,0,0,0.6)] transform transition-all duration-700 ease-out origin-center" data-index="0">
                    <div class="w-full h-full rounded-[2.5rem] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover opacity-70 mix-blend-luminosity" alt="Code Architecture">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                        <div class="absolute bottom-12 left-12 bg-black/60 backdrop-blur-xl border border-white/10 p-8 rounded-3xl font-mono text-base text-emerald-400 shadow-2xl">
                            <span class="text-slate-500">➜</span> php artisan migrate --seed<br>
                            <span class="text-slate-500">➜</span> MySQL Connection <span class="text-emerald-400 font-bold">OK</span><br>
                            <span class="text-slate-500">➜</span> Strict Mode: <span class="text-indigo-400 font-bold">ENABLED</span><br>
                            <span class="text-slate-500">➜</span> Database secured.
                        </div>
                    </div>
                </div>

                {{-- Image 2 --}}
                <div class="feature-image w-full h-[700px] bg-indigo-600 rounded-[3rem] p-6 border border-indigo-500 shadow-[0_40px_100px_rgba(79,70,229,0.3)] transform transition-all duration-700 ease-out origin-center" data-index="1">
                    <div class="w-full h-full rounded-[2.5rem] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 mix-blend-overlay" alt="AI Dashboard">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-2xl border border-white/20 p-12 rounded-[2.5rem] shadow-2xl text-center w-[85%]">
                            <div class="w-20 h-20 rounded-full bg-indigo-500/30 flex items-center justify-center mx-auto mb-8">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h4 class="text-white font-bold text-xl mb-4 uppercase tracking-widest">Month End Projection</h4>
                            <p class="text-7xl font-black text-white">+₹42,500</p>
                            <p class="text-indigo-200 mt-6 text-base font-medium">Based on your historical burn rate over 90 days.</p>
                        </div>
                    </div>
                </div>

                {{-- Image 3 --}}
                <div class="feature-image w-full h-[700px] bg-slate-100 dark:bg-slate-800 rounded-[3rem] p-6 border border-slate-300 dark:border-slate-700 shadow-2xl transform transition-all duration-700 ease-out origin-center" data-index="2">
                    <div class="w-full h-full rounded-[2.5rem] overflow-hidden relative bg-white dark:bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover opacity-50 grayscale" alt="Team Collaboration">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 to-transparent"></div>
                        <div class="absolute bottom-16 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-2xl border border-white/20 p-10 rounded-[2.5rem] shadow-2xl w-[85%]">
                            <p class="text-white font-bold text-lg text-center mb-8 uppercase tracking-widest">Active Finance Members</p>
                            <div class="flex justify-center -space-x-6">
                                <img class="w-24 h-24 rounded-full border-4 border-slate-800 shadow-xl object-cover hover:-translate-y-3 transition-transform" src="https://i.pravatar.cc/150?img=11" alt="User 1">
                                <img class="w-24 h-24 rounded-full border-4 border-slate-800 shadow-xl object-cover hover:-translate-y-3 transition-transform" src="https://i.pravatar.cc/150?img=32" alt="User 2">
                                <img class="w-24 h-24 rounded-full border-4 border-slate-800 shadow-xl object-cover hover:-translate-y-3 transition-transform" src="https://i.pravatar.cc/150?img=47" alt="User 3">
                                <div class="w-24 h-24 rounded-full border-4 border-slate-800 shadow-xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl z-10">+2</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ================= 8. GLOBAL MAP / INTEGRATION NODE ================= --}}
<section class="py-32 bg-white dark:bg-[#020617] relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white mb-6 tracking-tight" data-aos="fade-up">The Center of your Financial Universe.</h2>
        <p class="text-xl text-slate-500 max-w-3xl mx-auto mb-24" data-aos="fade-up" data-aos-delay="100">Connect everything. FinanceAI acts as the central brain, processing streams from all your existing international and domestic accounts seamlessly via secure API.</p>

        <div class="relative h-[600px] w-full max-w-6xl mx-auto flex items-center justify-center" data-aos="zoom-in" data-aos-duration="1000">
            
            {{-- Abstract Map Background --}}
            <svg class="absolute inset-0 w-full h-full text-slate-200 dark:text-slate-800/60" viewBox="0 0 1000 500" fill="currentColor">
                <circle cx="200" cy="150" r="4" /><circle cx="220" cy="160" r="2" /><circle cx="250" cy="140" r="5" />
                <circle cx="800" cy="200" r="4" /><circle cx="750" cy="250" r="2" /><circle cx="820" cy="220" r="5" />
                <circle cx="500" cy="450" r="4" /><circle cx="550" cy="430" r="2" /><circle cx="480" cy="470" r="5" />
                <circle cx="300" cy="350" r="4" /><circle cx="320" cy="330" r="2" /><circle cx="350" cy="370" r="5" />
                <circle cx="650" cy="150" r="4" /><circle cx="680" cy="120" r="2" /><circle cx="620" cy="180" r="5" />
                
                {{-- SVG connection lines drawing inward --}}
                <path d="M250,140 Q 375,195 500,250" stroke="currentColor" stroke-width="1.5" fill="none" stroke-dasharray="5,5" class="text-indigo-200 dark:text-indigo-900/60 animate-pulse"/>
                <path d="M800,200 Q 650,225 500,250" stroke="currentColor" stroke-width="1.5" fill="none" stroke-dasharray="5,5" class="text-indigo-200 dark:text-indigo-900/60 animate-pulse" style="animation-delay: 0.5s;"/>
                <path d="M500,450 Q 500,350 500,250" stroke="currentColor" stroke-width="1.5" fill="none" stroke-dasharray="5,5" class="text-indigo-200 dark:text-indigo-900/60 animate-pulse" style="animation-delay: 1s;"/>
            </svg>

            {{-- Center Hub --}}
            <div class="absolute z-20 w-40 h-40 bg-indigo-600 rounded-[3rem] shadow-[0_0_100px_rgba(79,70,229,0.8)] flex items-center justify-center rotate-45 transform hover:scale-125 transition-transform duration-500 cursor-pointer">
                <div class="-rotate-45">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>

            {{-- Pulsing Rings --}}
            <div class="absolute z-0 w-80 h-80 border-2 border-indigo-500/30 rounded-full animate-ping" style="animation-duration: 3s;"></div>
            <div class="absolute z-0 w-[500px] h-[500px] border border-indigo-500/20 rounded-full animate-ping" style="animation-duration: 4s; animation-delay: 1s;"></div>
            <div class="absolute z-0 w-[700px] h-[700px] border border-indigo-500/5 rounded-full"></div>

            {{-- Satellite Nodes --}}
            <div class="absolute top-10 left-[15%] w-24 h-24 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-2xl flex items-center justify-center z-10 animate-floatSlow">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-12 h-12" alt="Laravel">
            </div>
            
            {{-- Stripe Node --}}
            <div class="absolute bottom-16 right-[15%] w-28 h-28 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full shadow-2xl flex items-center justify-center z-10 animate-floatSlow" style="animation-delay: 1s;">
                <svg class="w-14 h-14 text-[#6366f1]" viewBox="0 0 24 24" fill="currentColor"><path d="M11.998 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm-.213 6.945c1.92 0 3.398 1.258 3.525 3.036h-2.148c-.068-.69-.738-1.258-1.572-1.258-.87 0-1.536.488-1.536 1.15 0 .61.43 1.01 1.488 1.258l1.413.333c1.782.42 2.766 1.422 2.766 3.047 0 1.953-1.636 3.35-3.805 3.35-2.106 0-3.766-1.378-3.92-3.32h2.24c.092.83.843 1.488 1.83 1.488 1.056 0 1.688-.518 1.688-1.218 0-.66-.452-1.1-1.635-1.35l-1.25-.264c-1.802-.38-2.628-1.423-2.628-2.95 0-1.896 1.583-3.292 3.544-3.292z"/></svg>
            </div>
            
            {{-- Personal User Context Node --}}
            <div class="absolute top-[20%] right-[10%] w-24 h-24 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-2xl flex items-center justify-center z-10 animate-floatSlow" style="animation-delay: 2s;">
                <div class="text-center">
                    <span class="font-black text-slate-800 dark:text-white text-sm block">BGMI</span>
                    <span class="text-xs text-emerald-500 font-bold">UC Sync</span>
                </div>
            </div>
            
            {{-- MySQL Node --}}
            <div class="absolute bottom-[20%] left-[10%] w-28 h-28 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full shadow-2xl flex items-center justify-center z-10 animate-floatSlow" style="animation-delay: 0.5s;">
                <span class="font-black text-3xl text-[#F29111] border-b-4 border-slate-800 dark:border-white">MySQL</span>
            </div>
        </div>
    </div>
</section>

{{-- ================= 9. INTERACTIVE PRICING (Vanilla JS Toggle + EXHAUSTIVE Matrix) ================= --}}
<section id="pricing" class="py-32 bg-slate-50 dark:bg-[#0b1121] relative border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-6xl font-black mb-6 text-slate-900 dark:text-white tracking-tight" data-aos="fade-up">Scaleable Infrastructure.</h2>
        <p class="text-slate-500 text-xl mb-16" data-aos="fade-up">Invest in your financial command center today. Special rates available for students.</p>

        {{-- Pure Vanilla JS Pricing Toggle --}}
        <div class="mb-24" data-aos="fade-up">
            <div class="inline-flex bg-white dark:bg-slate-900 rounded-full p-2 shadow-inner border border-slate-200 dark:border-slate-700 relative w-[360px] h-16 cursor-none" id="pricingToggleBtn">
                {{-- Physical Sliding Pill --}}
                <div id="pricingSlider" class="absolute top-2 bottom-2 left-2 w-[calc(50%-0.5rem)] bg-indigo-600 rounded-full shadow-lg transition-transform duration-400 ease-out z-0 transform translate-x-0"></div>
                
                <button id="btnMonthly" class="relative z-10 w-1/2 flex items-center justify-center font-bold text-base transition-colors duration-300 text-white cursor-none">Pay Monthly</button>
                <button id="btnYearly" class="relative z-10 w-1/2 flex items-center justify-center font-bold text-base transition-colors duration-300 text-slate-500 hover:text-slate-800 dark:hover:text-white cursor-none">Pay Yearly <span class="ml-2 px-2 py-0.5 bg-emerald-500/20 text-emerald-500 rounded-md text-[10px] uppercase tracking-wider hidden sm:block">Save 20%</span></button>
            </div>

            {{-- The Pricing Cards --}}
            <div class="grid md:grid-cols-3 gap-8 mt-12 text-left">
                @php
                    $plans = [
                        ['name'=>'Core','desc'=>'Basic individual tracking','monthly'=>'0','yearly'=>'0','btn'=>'Current Plan', 'features'=>['Standard Dashboard','MySQL 8 Sync','Community Support', 'Basic PDF Reports']],
                        ['name'=>'Pro','desc'=>'Full AI & Family control','monthly'=>'99','yearly'=>'950','btn'=>'Upgrade to Pro', 'features'=>['Everything in Core','ML Auto-Categorization','Predictive Forecasting','Priority Email Support', 'Family Collaboration (Up to 5)']],
                        ['name'=>'Enterprise','desc'=>'Custom architecture','monthly'=>'499','yearly'=>'4990','btn'=>'Contact Sales', 'features'=>['Dedicated DB Instance','White-label Interface','Custom Controller Logic','99.99% SLA', 'Dedicated Account Manager']],
                    ];
                @endphp

                @foreach($plans as $plan)
                <div class="bg-white dark:bg-slate-800 p-12 rounded-[3rem] shadow-xl border border-slate-200 dark:border-slate-700 relative transition-all duration-500 hover:-translate-y-3 {{ $loop->index==1 ? 'border-indigo-500 shadow-[0_30px_60px_rgba(79,70,229,0.2)] transform md:-translate-y-6 hover:-translate-y-8 z-10' : '' }}">
                    @if($loop->index == 1)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest shadow-lg whitespace-nowrap">Most Popular</div>
                    @endif
                    <h3 class="text-3xl font-black mb-3 text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                    <p class="text-slate-500 text-base mb-10 pb-10 border-b border-slate-100 dark:border-slate-700">{{ $plan['desc'] }}</p>
                    
                    <div class="mb-12 flex items-end h-16">
                        <span class="text-6xl font-black text-slate-900 dark:text-white transition-all price-value tracking-tighter" data-monthly="{{ $plan['monthly'] }}" data-yearly="{{ $plan['yearly'] }}">
                            ₹{{ $plan['monthly'] }}
                        </span>
                        <span class="text-slate-500 font-medium ml-2 mb-2 transition-all price-period text-lg">/mo</span>
                    </div>

                    <ul class="text-left space-y-6 mb-12 font-medium text-slate-600 dark:text-slate-400 text-lg">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-start gap-4">
                            <div class="w-7 h-7 rounded-full {{ $loop->parent->index == 1 ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="leading-tight">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    
                    <a href="{{ route('register') ?? '#' }}" class="magnetic-target block w-full py-5 text-center rounded-2xl font-bold text-lg transition-all {{ $loop->index==1 ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-xl shadow-indigo-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white hover:bg-slate-200 dark:hover:bg-slate-600' }} cursor-none">
                        {{ $plan['btn'] }}
                    </a>
                </div>
                @endforeach
            </div>

            {{-- Contextual GTU Easter Egg --}}
            <div class="mt-12 p-6 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/50 rounded-3xl max-w-3xl mx-auto flex items-center justify-center gap-4 shadow-sm" data-aos="fade-up">
                <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center shadow">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <div class="text-left">
                    <span class="text-lg font-bold text-indigo-900 dark:text-indigo-200 block">GTU Student Benefit</span>
                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Register with your Gujarat Technological University `.edu.in` email to unlock Pro entirely for free.</span>
                </div>
            </div>
        </div>

        {{-- Enterprise Feature Comparison Matrix (Massive Depth) --}}
        <div class="mt-40 hidden lg:block overflow-x-auto pb-10" data-aos="fade-up">
            <h3 class="text-4xl font-black text-slate-900 dark:text-white mb-10 tracking-tight">Technical Comparison Matrix</h3>
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden min-w-[1000px]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#0f172a] border-b border-slate-200 dark:border-slate-800">
                            <th class="p-8 text-xl font-black text-slate-900 dark:text-white w-[40%]">Technical & UX Specs</th>
                            <th class="p-8 text-xl font-bold text-slate-900 dark:text-white text-center w-[20%]">Core</th>
                            <th class="p-8 text-xl font-bold text-indigo-600 dark:text-indigo-400 text-center w-[20%] bg-indigo-50/50 dark:bg-indigo-900/20 shadow-[inset_0_-2px_0_rgba(79,70,229,1)] relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/10 to-transparent"></div>
                                <span class="relative z-10">Pro</span>
                            </th>
                            <th class="p-8 text-xl font-bold text-slate-900 dark:text-white text-center w-[20%]">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600 dark:text-slate-400 font-medium text-lg">
                        
                        {{-- Group: Infrastructure --}}
                        <tr class="bg-slate-100 dark:bg-slate-800/80"><td colspan="4" class="p-5 font-bold text-sm uppercase tracking-widest text-slate-500">Architecture & Security</td></tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">MySQL 8 Strict Sync</td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><svg class="w-6 h-6 mx-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Laravel SHA-256 Encryption</td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><svg class="w-6 h-6 mx-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Dedicated Database Instance</td>
                            <td class="p-6 text-center text-slate-300 dark:text-slate-600">—</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10 text-slate-400">—</td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Daily Automated Backups</td>
                            <td class="p-6 text-center text-slate-300 dark:text-slate-600">—</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><svg class="w-6 h-6 mx-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>

                        {{-- Group: AI Engine --}}
                        <tr class="bg-slate-100 dark:bg-slate-800/80"><td colspan="4" class="p-5 font-bold text-sm uppercase tracking-widest text-slate-500">AI Intelligence Core</td></tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Transaction Categorization</td>
                            <td class="p-6 text-center">Manual</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><span class="font-bold text-indigo-600 dark:text-indigo-400">ML Auto-Tagging</span></td>
                            <td class="p-6 text-center"><span class="font-bold text-emerald-500">ML Auto-Tagging</span></td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Predictive Forecasting</td>
                            <td class="p-6 text-center text-slate-300 dark:text-slate-600">—</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><svg class="w-6 h-6 mx-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Anomaly Detection Alerts</td>
                            <td class="p-6 text-center text-slate-300 dark:text-slate-600">—</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10"><svg class="w-6 h-6 mx-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="p-6 text-center"><svg class="w-6 h-6 mx-auto text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>

                        {{-- Group: Limits --}}
                        <tr class="bg-slate-100 dark:bg-slate-800/80"><td colspan="4" class="p-5 font-bold text-sm uppercase tracking-widest text-slate-500">Usage Limits</td></tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Family Collaboration</td>
                            <td class="p-6 text-center">1 User</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10 text-slate-900 dark:text-white font-bold">Up to 5 Users</td>
                            <td class="p-6 text-center text-slate-900 dark:text-white font-bold">Unlimited</td>
                        </tr>
                        <tr class="border-b border-slate-100 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">Monthly Transactions</td>
                            <td class="p-6 text-center">500</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10 text-slate-900 dark:text-white font-bold">Unlimited</td>
                            <td class="p-6 text-center text-slate-900 dark:text-white font-bold">Unlimited</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <td class="p-6 pl-8">API Rate Limit</td>
                            <td class="p-6 text-center text-slate-300 dark:text-slate-600">—</td>
                            <td class="p-6 text-center bg-indigo-50/30 dark:bg-indigo-900/10 font-bold text-indigo-600 dark:text-indigo-400">100 / minute</td>
                            <td class="p-6 text-center text-slate-900 dark:text-white font-bold">10,000 / minute</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{-- ================= 10. FAQ (Vanilla JS Accordion) ================= --}}
<section class="py-28 bg-slate-50 dark:bg-[#050b14] border-t border-slate-200 dark:border-slate-800 relative overflow-hidden">
    <div class="absolute -left-40 bottom-0 w-[600px] h-[600px] bg-pink-500/5 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-6 relative z-10">
        <h2 class="text-4xl md:text-6xl font-black text-center mb-20 text-slate-900 dark:text-white tracking-tight" data-aos="fade-up">Frequently Asked Questions</h2>

        <div class="space-y-6" id="faqAccordion">
            @php
                $faqs = [
                    ['q'=>'Is my financial data secure?', 'a'=>'Absolutely. We use industry-standard AES-256 encryption via Laravel. Your database connections are completely isolated and we never store plaintext credentials on our servers.'],
                    ['q'=>'Does this fix ONLY_FULL_GROUP_BY SQL errors?', 'a'=>'Yes. The entire Laravel repository is written with explicit, strict-mode compliant Eloquent queries and Raw selects to ensure zero aggregation errors. It is foolproof.'],
                    ['q'=>'Can I access the dashboard on mobile?', 'a'=>'Yes. The entire UI is built with TailwindCSS grid and flex utilities, meaning the dashboard, charts, and tables map perfectly to any smartphone size.'],
                    ['q'=>'How does the AI prediction work?', 'a'=>'Our ML engine takes your last 90 days of transactions, identifies recurring burn rates, and mathematically forecasts your likely balance at the end of the current month with high accuracy.'],
                    ['q'=>'Do you offer discounts for GTU students?', 'a'=>'Yes! We offer a 100% free Pro tier for students at Gujarat Technological University. Just sign up with your valid `.edu.in` email address and contact support to claim your upgrade.'],
                ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="faq-item bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] overflow-hidden transition-all duration-300 shadow-lg hover:shadow-xl hover:border-indigo-200 dark:hover:border-indigo-900" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <button class="faq-btn w-full px-10 py-8 text-left flex justify-between items-center focus:outline-none group cursor-none magnetic-target">
                    <span class="font-bold text-2xl text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $faq['q'] }}</span>
                    <span class="faq-icon w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center transform transition-transform duration-500 text-slate-500 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                    <div class="px-10 pb-10 text-slate-500 dark:text-slate-400 font-medium leading-relaxed text-lg border-t border-slate-100 dark:border-slate-800 pt-8 mt-2">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= 11. LIVE PULSE NOTIFICATIONS (Bottom Left Toast) ================= --}}
<div id="liveToast" class="fixed bottom-8 left-8 z-[90] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-[0_20px_50px_rgba(0,0,0,0.5)] rounded-2xl p-5 flex items-center gap-5 transform translate-y-40 opacity-0 transition-all duration-700 pointer-events-none max-w-sm backdrop-blur-xl">
    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 flex-shrink-0 border border-emerald-200 dark:border-emerald-800/50 shadow-[0_0_15px_rgba(52,211,153,0.3)]">
        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div>
        <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider mb-0.5" id="toastTitle">Live Sync</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium" id="toastMessage">Just synced a new database.</p>
    </div>
</div>

{{-- ================= 12. REAL VIDEO MODAL (Interactive iframe) ================= --}}
<div id="demoModal" class="fixed inset-0 z-[100] bg-slate-900/95 backdrop-blur-3xl hidden flex items-center justify-center opacity-0 transition-opacity duration-300 px-6 cursor-none">
    <div class="relative w-full max-w-6xl aspect-video bg-black rounded-[2rem] shadow-[0_0_150px_rgba(79,70,229,0.6)] overflow-hidden border border-slate-700 transform scale-95 transition-transform duration-500" id="demoModalContent">
        {{-- Close Button --}}
        <button onclick="closeDemoModal()" class="magnetic-target absolute -top-16 right-0 z-20 w-14 h-14 bg-white/10 hover:bg-rose-600 backdrop-blur-md rounded-full flex items-center justify-center text-white transition-colors shadow-2xl cursor-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        {{-- Working Iframe container (SRC is injected via JS on open to prevent background loading) --}}
        <iframe id="youtubePlayer" class="w-full h-full absolute inset-0 z-10" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="absolute inset-0 z-0" onclick="closeDemoModal()"></div>
</div>

{{-- ================= 13. MATRIX KONAMI OVERLAY (Easter Egg) ================= --}}
<canvas id="matrixCanvas" class="fixed inset-0 z-[99999] pointer-events-none hidden opacity-0 transition-opacity duration-1000"></canvas>

@endsection

{{-- ================= SCRIPTS & STYLES ================= --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    /* Hide Default Cursor where custom is applied */
    .cursor-none { cursor: none !important; }

    /* Premium Typewriter Effect */
    .typewriter-effect {
        display: inline-block;
        white-space: nowrap;
        margin: 0 auto;
        letter-spacing: .05em;
        animation: typing 4s steps(40, end) infinite alternate;
        overflow: hidden;
    }
    @keyframes typing { 0%, 20% { width: 0 } 80%, 100% { width: 100% } }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    .animate-blink { animation: blink 1s step-end infinite; }

    /* Floating & Shimmer */
    @keyframes float { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-20px);} }
    .animate-float { animation: float 8s ease-in-out infinite; }
    @keyframes floatSlow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .animate-floatSlow { animation: floatSlow 6s ease-in-out infinite; }
    @keyframes shimmer { 100% { transform: translateX(100%); } }
    .animate-shimmer { animation: shimmer 2.5s infinite; }

    /* Scroll Skewing effect class applied via JS */
    .scroll-skew { transition: transform 0.1s ease-out; }

    /* Marquees */
    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    .animate-marquee { animation: marquee 40s linear infinite; }
    .animate-marquee:hover { animation-play-state: paused; }
    @keyframes marquee-reverse { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
    .animate-marquee-reverse { animation: marquee-reverse 40s linear infinite; }
    .animate-marquee-reverse:hover { animation-play-state: paused; }
    @keyframes marquee-fast { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    .animate-marquee-fast { animation: marquee-fast 25s linear infinite; }
    .animate-marquee-fast:hover { animation-play-state: paused; }

    /* Custom Swiper Styles */
    .swiper-pagination-bullet { background: #cbd5e1; opacity: 1; }
    .dark .swiper-pagination-bullet { background: #475569; }
    .swiper-pagination-bullet-active { background: #4f46e5 !important; width: 40px; border-radius: 12px; transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

    /* 3D Perspective */
    .perspective-1000 { perspective: 1000px; }

    /* Matrix Mode Override classes */
    .matrix-mode {
        --tw-bg-opacity: 1 !important;
        background-color: #000 !important;
        color: #0f0 !important;
    }
    .matrix-mode * { border-color: #0f0 !important; box-shadow: none !important; }
    .matrix-mode .bg-indigo-600, .matrix-mode .bg-emerald-500, .matrix-mode .bg-rose-500 { background-color: #0f0 !important; color: #000 !important; }
    .matrix-mode .text-slate-900, .matrix-mode .text-white, .matrix-mode .text-slate-500 { color: #0f0 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 0. AOS Initialization
    AOS.init({ duration: 1000, once: true, offset: 100, easing: 'ease-out-cubic' });

    // 1. CUSTOM CURSOR LOGIC (Magnetic Effect)
    const cursor = document.getElementById('custom-cursor');
    const heroSection = document.getElementById('hero');
    const magneticTargets = document.querySelectorAll('.magnetic-target');

    if (cursor) {
        document.addEventListener('mousemove', (e) => {
            // Only show cursor on dark mode or hero section to avoid hiding it on white backgrounds without contrast
            if(document.documentElement.classList.contains('dark') || e.target.closest('#hero')) {
                cursor.style.opacity = '1';
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';
            } else {
                cursor.style.opacity = '0';
            }
        });

        magneticTargets.forEach(target => {
            target.addEventListener('mousemove', (e) => {
                const rect = target.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                target.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
                cursor.style.transform = `translate(-50%, -50%) scale(3)`;
                cursor.style.backgroundColor = 'rgba(236, 72, 153, 0.4)'; 
                cursor.style.borderColor = 'rgba(236, 72, 153, 0.8)';
            });

            target.addEventListener('mouseleave', () => {
                target.style.transform = `translate(0px, 0px)`;
                cursor.style.transform = `translate(-50%, -50%) scale(1)`;
                cursor.style.backgroundColor = 'transparent'; 
                cursor.style.borderColor = '#6366f1'; 
            });
        });
    }

    // 2. DYNAMIC GREETING BASED ON TIME (Ahmedabad Context)
    const greetingEl = document.getElementById('dynamic-greeting');
    if(greetingEl) {
        const hour = new Date().getHours();
        let greeting = "System Online";
        if (hour >= 5 && hour < 12) greeting = "Good Morning";
        else if (hour >= 12 && hour < 18) greeting = "Good Afternoon";
        else greeting = "Good Evening";
        greetingEl.innerText = greeting;
    }

    // 3. CMD+K MODAL LOGIC
    const cmdkModal = document.getElementById('cmdk-modal');
    const cmdkInput = document.getElementById('cmdk-input');

    window.openCmdK = function() {
        cmdkModal.classList.remove('hidden');
        setTimeout(() => {
            cmdkModal.classList.remove('opacity-0');
            cmdkModal.classList.add('opacity-100');
            document.getElementById('cmdk-content').classList.remove('scale-95');
            document.getElementById('cmdk-content').classList.add('scale-100');
            cmdkInput.focus();
        }, 10);
    }

    window.closeCmdK = function() {
        cmdkModal.classList.remove('opacity-100');
        cmdkModal.classList.add('opacity-0');
        document.getElementById('cmdk-content').classList.remove('scale-100');
        document.getElementById('cmdk-content').classList.add('scale-95');
        setTimeout(() => {
            cmdkModal.classList.add('hidden');
            cmdkInput.value = '';
        }, 300);
    }

    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            cmdkModal.classList.contains('hidden') ? openCmdK() : closeCmdK();
        }
        if (e.key === 'Escape' && !cmdkModal.classList.contains('hidden')) {
            closeCmdK();
        }
    });

    // 4. Interactive AI Particle Network (Hero Canvas)
    const particleCanvas = document.getElementById('particleCanvas');
    if(particleCanvas) {
        const pCtx = particleCanvas.getContext('2d');
        let particlesArray;
        
        particleCanvas.width = window.innerWidth;
        particleCanvas.height = window.innerHeight;

        class Particle {
            constructor(x, y, directionX, directionY, size, color) {
                this.x = x; this.y = y;
                this.directionX = directionX; this.directionY = directionY;
                this.size = size; this.color = color;
            }
            draw() {
                pCtx.beginPath(); pCtx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
                pCtx.fillStyle = '#6366f1'; pCtx.fill();
            }
            update() {
                if (this.x > particleCanvas.width || this.x < 0) this.directionX = -this.directionX;
                if (this.y > particleCanvas.height || this.y < 0) this.directionY = -this.directionY;
                this.x += this.directionX; this.y += this.directionY;
                this.draw();
            }
        }

        function initParticles() {
            particlesArray = [];
            let numberOfParticles = (particleCanvas.height * particleCanvas.width) / 10000;
            for (let i = 0; i < numberOfParticles; i++) {
                let size = (Math.random() * 2.5) + 1;
                let x = (Math.random() * ((innerWidth - size * 2) - (size * 2)) + size * 2);
                let y = (Math.random() * ((innerHeight - size * 2) - (size * 2)) + size * 2);
                let directionX = (Math.random() * 1.5) - 0.75;
                let directionY = (Math.random() * 1.5) - 0.75;
                let color = '#6366f1';
                particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
            }
        }

        function animateParticles() {
            requestAnimationFrame(animateParticles);
            pCtx.clearRect(0, 0, innerWidth, innerHeight);
            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
            }
            connectParticles();
        }

        function connectParticles() {
            let opacityValue = 1;
            for (let a = 0; a < particlesArray.length; a++) {
                for (let b = a; b < particlesArray.length; b++) {
                    let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x)) + ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));
                    if (distance < (particleCanvas.width / 7) * (particleCanvas.height / 7)) {
                        opacityValue = 1 - (distance / 15000);
                        pCtx.strokeStyle = 'rgba(99, 102, 241,' + opacityValue + ')';
                        pCtx.lineWidth = 1;
                        pCtx.beginPath();
                        pCtx.moveTo(particlesArray[a].x, particlesArray[a].y);
                        pCtx.lineTo(particlesArray[b].x, particlesArray[b].y);
                        pCtx.stroke();
                    }
                }
            }
        }

        window.addEventListener('resize', function() {
            particleCanvas.width = innerWidth; particleCanvas.height = innerHeight;
            initParticles();
        });

        initParticles();
        animateParticles();
    }

    // 5. DATA FLOW CANVAS (Security Section)
    const flowCanvas = document.getElementById('dataFlowCanvas');
    if(flowCanvas) {
        const fCtx = flowCanvas.getContext('2d');
        flowCanvas.width = 400; flowCanvas.height = 400;
        let packets = [];

        function createPacket() {
            packets.push({ x: 200, y: 200, angle: Math.random() * Math.PI * 2, speed: Math.random() * 2 + 1, size: Math.random() * 3 + 2, life: 100 });
        }

        function animateFlow() {
            requestAnimationFrame(animateFlow);
            fCtx.fillStyle = 'rgba(15, 23, 42, 0.2)'; // fade trail
            fCtx.fillRect(0, 0, flowCanvas.width, flowCanvas.height);

            if(Math.random() < 0.3) createPacket(); // generate new packets

            for (let i = 0; i < packets.length; i++) {
                let p = packets[i];
                p.x += Math.cos(p.angle) * p.speed;
                p.y += Math.sin(p.angle) * p.speed;
                p.life--;

                fCtx.beginPath();
                fCtx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                fCtx.fillStyle = `rgba(52, 211, 153, ${p.life / 100})`; // emerald glow
                fCtx.fill();

                if(p.life <= 0) { packets.splice(i, 1); i--; }
            }
        }
        animateFlow();
    }

    // 6. API TABS SWITCHER (Vanilla JS)
    const apiTabs = document.querySelectorAll('.api-tab');
    const apiCodes = document.querySelectorAll('.api-code-block');

    apiTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset Tabs
            apiTabs.forEach(t => {
                t.classList.remove('text-indigo-400', 'border-indigo-500');
                t.classList.add('text-slate-500', 'border-transparent');
            });
            // Set Active
            tab.classList.remove('text-slate-500', 'border-transparent');
            tab.classList.add('text-indigo-400', 'border-indigo-500');

            // Hide Blocks
            apiCodes.forEach(code => {
                code.classList.remove('opacity-100');
                code.classList.add('opacity-0');
                setTimeout(() => { code.classList.add('hidden'); code.classList.remove('block'); }, 300);
            });

            // Show Target
            const targetId = tab.getAttribute('data-target');
            const targetCode = document.getElementById(targetId);
            setTimeout(() => {
                targetCode.classList.remove('hidden');
                targetCode.classList.add('block');
                setTimeout(() => { targetCode.classList.remove('opacity-0'); targetCode.classList.add('opacity-100'); }, 10);
            }, 300);
        });
    });

    // 7. PURE VANILLA JS ROI CALCULATOR ENGINE
    const txSlider = document.getElementById('txSlider');
    const expSlider = document.getElementById('expSlider');
    const txValue = document.getElementById('txValue');
    const expValue = document.getElementById('expValue');
    const hoursSaved = document.getElementById('hoursSaved');
    const moneySaved = document.getElementById('moneySaved');

    function calculateROI() {
        if(!txSlider || !expSlider) return;
        const tx = parseInt(txSlider.value);
        const exp = parseInt(expSlider.value);
        
        txValue.innerText = tx;
        expValue.innerText = '₹' + exp.toLocaleString('en-IN');

        const hours = Math.round((tx * 3 * 12) / 60); 
        const savings = Math.round((exp * 12) * 0.15);

        hoursSaved.innerText = hours;
        moneySaved.innerText = '₹' + savings.toLocaleString('en-IN');
    }

    if(txSlider) txSlider.addEventListener('input', calculateROI);
    if(expSlider) expSlider.addEventListener('input', calculateROI);

    // 8. TERMINAL TYPING SIMULATOR
    const terminalOutput = document.getElementById('terminal-output');
    if(terminalOutput) {
        const commands = [
            { text: "php artisan migrate:fresh --seed", color: "text-slate-300" },
            { text: "Dropping all tables...", color: "text-emerald-400" },
            { text: "Migration table created successfully.", color: "text-emerald-400" },
            { text: "Seeding: Database\\Seeders\\FinanceSeeder", color: "text-indigo-400" },
            { text: "Verifying Strict Mode: SET sql_mode='ONLY_FULL_GROUP_BY'", color: "text-amber-400" },
            { text: "Validation Passed. Zero Errors.", color: "text-emerald-400" },
            { text: "System Ready. Starting Queue Worker...", color: "text-slate-300" }
        ];
        
        let cmdIndex = 0;
        function typeLine() {
            if(cmdIndex >= commands.length) return;
            const line = document.createElement('div');
            line.className = commands[cmdIndex].color;
            line.innerHTML = `<span class="text-slate-500 mr-2">></span>${commands[cmdIndex].text}`;
            terminalOutput.appendChild(line);
            
            // Auto scroll
            const container = document.getElementById('terminal-container');
            container.scrollTop = container.scrollHeight;
            
            cmdIndex++;
            setTimeout(typeLine, Math.random() * 600 + 300); 
        }
        
        // Start typing when section is visible
        const termObserver = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) {
                setTimeout(typeLine, 800);
                termObserver.disconnect();
            }
        });
        termObserver.observe(terminalOutput);
    }

    // 9. SCROLL VELOCITY SKEW EFFECT
    let lastScroll = window.scrollY;
    const skewElements = document.querySelectorAll('.scroll-skew');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.scrollY;
        const velocity = currentScroll - lastScroll;
        lastScroll = currentScroll;
        
        // Limit skew amount
        const skewAmount = Math.max(Math.min(velocity * 0.05, 5), -5);
        
        skewElements.forEach(el => {
            el.style.transform = `skewY(${skewAmount}deg)`;
            // Reset back to 0
            setTimeout(() => {
                el.style.transform = `skewY(0deg)`;
            }, 150);
        });
    });

    // 10. Advanced Scroll-Spy Feature Section (Vanilla JS)
    const featureTexts = document.querySelectorAll('.feature-text');
    const featureImages = document.querySelectorAll('.feature-image');
    
    if(featureTexts.length > 0) {
        window.addEventListener('scroll', () => {
            featureTexts.forEach((text, index) => {
                const textTop = text.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (textTop < windowHeight * 0.6 && textTop > windowHeight * 0.2) {
                    text.classList.remove('opacity-20', 'blur-md', 'translate-x-10');
                    text.classList.add('opacity-100', 'translate-x-0');
                    
                    featureImages.forEach((img, imgIndex) => {
                        if(index === imgIndex) {
                            img.style.display = 'block';
                            setTimeout(() => { img.style.opacity = '1'; img.style.transform = 'translateY(0) scale(1)'; }, 50);
                        } else {
                            img.style.opacity = '0'; img.style.transform = 'translateY(40px) scale(0.95)';
                            setTimeout(() => { img.style.display = 'none'; }, 700);
                        }
                    });
                } else {
                    text.classList.add('opacity-20', 'blur-md', 'translate-x-10');
                    text.classList.remove('opacity-100', 'translate-x-0');
                }
            });
        });
        
        featureImages.forEach((img, index) => {
            if(index !== 0) {
                img.style.display = 'none';
                img.style.opacity = '0';
            }
        });
    }

    // 11. Bento Box Mouse Glow Effect
    document.querySelectorAll('.bento-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const glow = card.querySelector('.glow-effect');
            if(!glow) return;
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            let color = document.documentElement.classList.contains('dark') ? 'rgba(79,70,229,0.12)' : 'rgba(79,70,229,0.06)';
            glow.style.background = `radial-gradient(1000px circle at ${x}px ${y}px, ${color}, transparent 40%)`;
        });
    });

    // 12. PURE VANILLA JS PRICING TOGGLE
    const toggleBtn = document.getElementById('pricingToggleBtn');
    if (toggleBtn) {
        let isAnnual = false;
        const slider = document.getElementById('pricingSlider');
        const btnMonthly = document.getElementById('btnMonthly');
        const btnYearly = document.getElementById('btnYearly');
        const prices = document.querySelectorAll('.price-value');
        const periods = document.querySelectorAll('.price-period');

        toggleBtn.addEventListener('click', (e) => {
            const rect = toggleBtn.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            isAnnual = clickX > rect.width / 2;

            if (isAnnual) {
                slider.classList.replace('translate-x-0', 'translate-x-full');
                btnYearly.classList.replace('text-slate-500', 'text-white');
                btnMonthly.classList.replace('text-white', 'text-slate-500');
                
                prices.forEach(p => p.innerText = '₹' + p.getAttribute('data-yearly'));
                periods.forEach(p => p.innerText = '/yr');
            } else {
                slider.classList.replace('translate-x-full', 'translate-x-0');
                btnMonthly.classList.replace('text-slate-500', 'text-white');
                btnYearly.classList.replace('text-white', 'text-slate-500');
                
                prices.forEach(p => p.innerText = '₹' + p.getAttribute('data-monthly'));
                periods.forEach(p => p.innerText = '/mo');
            }
        });
    }

    // 13. PURE VANILLA JS FAQ ACCORDION
    const faqBtns = document.querySelectorAll('.faq-btn');
    faqBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.faq-icon');
            
            // Close all others
            faqBtns.forEach(otherBtn => {
                if(otherBtn !== btn) {
                    otherBtn.nextElementSibling.style.maxHeight = null;
                    otherBtn.querySelector('.faq-icon').classList.remove('rotate-180', 'bg-indigo-100', 'text-indigo-600', 'dark:bg-indigo-900/50', 'dark:text-indigo-400');
                }
            });

            // Toggle current
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                icon.classList.remove('rotate-180', 'bg-indigo-100', 'text-indigo-600', 'dark:bg-indigo-900/50', 'dark:text-indigo-400');
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.classList.add('rotate-180', 'bg-indigo-100', 'text-indigo-600', 'dark:bg-indigo-900/50', 'dark:text-indigo-400');
            }
        });
    });

    // 14. Swiper Setup
    new Swiper('.mySwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: { delay: 6000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-next', prevEl: '.swiper-prev' },
        breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
    });

    // 15. Hero 3D Mouse Tilt Effect
    const hero3D = document.getElementById('hero3D');
    const tiltCard = hero3D?.querySelector('.tilt-card');
    if(hero3D && tiltCard) {
        hero3D.addEventListener('mousemove', (e) => {
            const rect = hero3D.getBoundingClientRect();
            const x = e.clientX - rect.left; 
            const y = e.clientY - rect.top;  
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -5;
            const rotateY = ((x - centerX) / centerX) * 5;
            tiltCard.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
        hero3D.addEventListener('mouseleave', () => {
            tiltCard.style.transform = `rotateX(0deg) rotateY(0deg)`;
            tiltCard.style.transition = `transform 0.5s ease-out`; 
        });
        hero3D.addEventListener('mouseenter', () => {
            tiltCard.style.transition = `transform 0.1s ease-out`; 
        });
    }

    // 16. BEAST CHART (Neon Glow)
    const canvas = document.getElementById('heroNeonChart');
    if(canvas){
        const ctx = canvas.getContext('2d');
        const gradIncome = ctx.createLinearGradient(0, 0, 0, 200);
        gradIncome.addColorStop(0, 'rgba(52, 211, 153, 0.6)'); 
        gradIncome.addColorStop(1, 'rgba(52, 211, 153, 0)');

        new Chart(ctx, {
            type:'line',
            data:{
                labels:['W1','W2','W3','W4','W5','W6'],
                datasets:[{
                    label: 'Portfolio',
                    data: [820000, 940000, 910000, 1050000, 1120000, 1240500], 
                    borderColor: '#34d399', 
                    backgroundColor: gradIncome,
                    borderWidth: 5,
                    fill: true,
                    tension: 0.5,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#34d399',
                    pointBorderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 10
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                interaction: { mode: 'index', intersect: false },
                plugins:{
                    legend:{display:false},
                    tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#34d399', padding: 16, cornerRadius: 12, titleFont:{size:16}, bodyFont:{weight:'900', size:20} }
                },
                scales:{
                    y:{display:false},
                    x:{grid:{display:false}, ticks:{color:'#64748b', font:{family:'monospace', size: 14}}}
                }
            }
        });
    }

    // 17. LIVE PULSE NOTIFICATIONS (Social Proof Toast)
    const toastMessages = [
        { title: 'Secure Sync', msg: 'New bank connection established via Stripe.' },
        { title: 'AI Categorized', msg: 'Expense: Crazy Chat Corner - ₹150' },
        { title: 'Pro Upgrade', msg: 'GTU Student claimed Free Pro Tier.' },
        { title: 'Payment Mapped', msg: 'Expense: BGMI UC Purchase - ₹380' },
        { title: 'AI Automation', msg: 'Generated 90-day forecast for user #8491.' }
    ];
    let currentToast = 0;
    const toast = document.getElementById('liveToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMsg = document.getElementById('toastMessage');

    if(toast) {
        setInterval(() => {
            toastTitle.innerText = toastMessages[currentToast].title;
            toastMsg.innerText = toastMessages[currentToast].msg;
            
            toast.classList.remove('translate-y-40', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-40', 'opacity-0');
            }, 5000);

            currentToast = (currentToast + 1) % toastMessages.length;
        }, 15000); 
    }

    // 18. Observer-Based Counter Animation
    const animateCounters = () => {
        document.querySelectorAll('.counter').forEach(el => {
            if(el.dataset.animated === "true") return;
            el.dataset.animated = "true";
            
            const target = parseInt(el.dataset.target) || 0;
            const duration = 3000;
            let startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const easedValue = Math.floor(progress === 1 ? target : target * (1 - Math.pow(2, -10 * progress)));
                el.innerText = easedValue.toLocaleString('en-IN');
                if (progress < 1) window.requestAnimationFrame(step);
            }
            window.requestAnimationFrame(step);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    const kpiSection = document.querySelector('.kpi-card-trigger');
    if(kpiSection) observer.observe(kpiSection);

});

// 19. WORKING YOUTUBE VIDEO MODAL LOGIC
window.openDemoModal = function() {
    const modal = document.getElementById('demoModal');
    const content = document.getElementById('demoModalContent');
    const player = document.getElementById('youtubePlayer');
    
    // Inject real YouTube video URL with autoplay
    player.src = "https://www.youtube.com/embed/gQ-7E6l_Z50?autoplay=1&rel=0&modestbranding=1";

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

window.closeDemoModal = function() {
    const modal = document.getElementById('demoModal');
    const content = document.getElementById('demoModalContent');
    const player = document.getElementById('youtubePlayer');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        player.src = ""; // Kill iframe stream to stop audio
    }, 500); 
}

// 20. KONAMI CODE EASTER EGG (The Ultimate Flex)
window.triggerKonami = function() {
    closeCmdK();
    document.body.classList.add('matrix-mode');
    
    // Matrix Rain Canvas
    const mCanvas = document.getElementById('matrixCanvas');
    mCanvas.classList.remove('hidden');
    setTimeout(() => mCanvas.classList.remove('opacity-0'), 100);
    
    const ctx = mCanvas.getContext('2d');
    mCanvas.width = window.innerWidth;
    mCanvas.height = window.innerHeight;
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789$+-*/=%\"'#&_(),.;:?!\\|{}<>[]^~";
    const fontSize = 16;
    const columns = mCanvas.width / fontSize;
    const drops = [];
    for(let x = 0; x < columns; x++) drops[x] = 1; 

    function drawMatrix() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        ctx.fillRect(0, 0, mCanvas.width, mCanvas.height);
        ctx.fillStyle = '#0F0';
        ctx.font = fontSize + 'px monospace';
        for(let i = 0; i < drops.length; i++) {
            const text = letters.charAt(Math.floor(Math.random() * letters.length));
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            if(drops[i] * fontSize > mCanvas.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }
    const matrixInterval = setInterval(drawMatrix, 33);

    // Stop after 10 seconds
    setTimeout(() => {
        clearInterval(matrixInterval);
        mCanvas.classList.add('opacity-0');
        document.body.classList.remove('matrix-mode');
        setTimeout(() => mCanvas.classList.add('hidden'), 1000);
    }, 10000);
}

// Listen for actual keyboard Konami Code
const konamiCode = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
let konamiIndex = 0;
document.addEventListener('keydown', (e) => {
    if (e.key === konamiCode[konamiIndex]) {
        konamiIndex++;
        if (konamiIndex === konamiCode.length) {
            triggerKonami();
            konamiIndex = 0;
        }
    } else {
        konamiIndex = 0;
    }
});
</script>
@endpush