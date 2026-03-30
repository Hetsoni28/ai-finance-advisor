{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: MASTER ENTERPRISE FOOTER & COMMAND CONSOLE              --}}
{{-- ====================================================================== --}}

<footer class="relative bg-white border-t border-slate-200 mt-40 pt-16 overflow-hidden" x-data="masterFooterEngine()">

    {{-- ================= 0. AMBIENT BACKGROUND TOPOLOGY ================= --}}
    <div class="absolute inset-0 pointer-events-none z-0">
        {{-- High-End Engineering Grid --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuNiIgZmlsbC1ydWxlPSJldmVub2RkIi8+PC9zdmc+')] opacity-60"></div>
        
        {{-- Abstract Global Map Nodes (SVG) --}}
        <svg class="absolute bottom-0 right-0 w-[1000px] h-[500px] text-slate-200 opacity-60" viewBox="0 0 1000 500" fill="currentColor">
            <circle cx="200" cy="150" r="4"/><circle cx="220" cy="160" r="2"/><circle cx="250" cy="140" r="6" class="animate-pulse text-indigo-300"/>
            <circle cx="800" cy="200" r="4"/><circle cx="750" cy="250" r="2"/><circle cx="820" cy="220" r="6" class="animate-pulse text-emerald-300"/>
            <circle cx="500" cy="450" r="4"/><circle cx="550" cy="430" r="2"/><circle cx="480" cy="470" r="6" class="animate-pulse text-sky-300"/>
            <path d="M250,140 Q 375,195 500,250" stroke="currentColor" stroke-width="1.5" fill="none" stroke-dasharray="4,4" class="opacity-50"/>
            <path d="M800,200 Q 650,225 500,250" stroke="currentColor" stroke-width="1.5" fill="none" stroke-dasharray="4,4" class="opacity-50"/>
        </svg>

        {{-- Top Gradient Fade to blend with previous sections --}}
        <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-white to-transparent"></div>
        
        {{-- Deep Glows --}}
        <div class="absolute -left-[10%] bottom-0 w-[800px] h-[800px] bg-indigo-600/5 rounded-full blur-[150px]"></div>
        <div class="absolute -right-[10%] bottom-[-10%] w-[900px] h-[900px] bg-sky-500/5 rounded-full blur-[120px]"></div>
    </div>

    {{-- ================= 1. DUAL-MODE NEWSLETTER PORTAL (UI vs CLI) ================= --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 -mt-40 mb-24 perspective-[2000px]">
        
        <div class="preserve-3d transition-transform duration-300 ease-out shadow-[0_40px_100px_-15px_rgba(0,0,0,0.1)] rounded-[3rem] bg-white border border-slate-200 overflow-hidden group"
             @mousemove="tiltPortal($event, $el)" 
             @mouseleave="resetTilt($el)">
            
            <div class="grid lg:grid-cols-2 relative">
                
                {{-- Left Side: Value Proposition & Art --}}
                <div class="p-10 md:p-14 relative overflow-hidden bg-slate-50 border-b lg:border-b-0 lg:border-r border-slate-200">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=1000&auto=format&fit=crop')] opacity-[0.03] mix-blend-luminosity group-hover:scale-105 transition-transform duration-1000 ease-out object-cover"></div>
                    <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-indigo-600 text-[10px] font-black uppercase tracking-widest mb-6 shadow-sm">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span></span>
                            Weekly Intelligence
                        </div>
                        <h5 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4 leading-tight">
                            Stay ahead of <br> the market.
                        </h5>
                        <p class="text-slate-500 text-lg font-medium leading-relaxed mb-8 max-w-md">
                            Receive our cryptographic engineering updates, AI financial forecasts, and feature drops directly to your secure inbox. No spam.
                        </p>

                        {{-- Subscriber Proof --}}
                        <div class="flex items-center gap-4 pt-6 border-t border-slate-200">
                            <div class="flex items-center -space-x-3">
                                <img src="https://i.pravatar.cc/100?img=12" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                                <img src="https://i.pravatar.cc/100?img=33" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                                <img src="https://i.pravatar.cc/100?img=45" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                                <div class="w-10 h-10 rounded-full border-2 border-white shadow-sm bg-slate-100 flex items-center justify-center text-[9px] font-black text-slate-500">+12k</div>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global Nodes Joined</p>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Dual-Mode Engine --}}
                <div class="p-10 md:p-14 relative bg-white flex flex-col justify-center">
                    
                    {{-- Mode Switcher --}}
                    <div class="flex items-center justify-between mb-8">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Interaction Vector</span>
                        <div class="flex bg-slate-100 p-1 rounded-xl shadow-inner border border-slate-200">
                            <button @click="setMode('ui')" @mouseenter="playHoverSound()" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all focus:outline-none" :class="inputMode === 'ui' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' : 'text-slate-400 hover:text-slate-600'">UI Mode</button>
                            <button @click="setMode('cli')" @mouseenter="playHoverSound()" class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all focus:outline-none" :class="inputMode === 'cli' ? 'bg-slate-900 text-emerald-400 shadow-sm' : 'text-slate-400 hover:text-slate-600'">CLI Mode</button>
                        </div>
                    </div>

                    {{-- ================= MODE: UI (Standard) ================= --}}
                    <div x-show="inputMode === 'ui'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="w-full">
                        <form @submit.prevent="submitNewsletter" class="relative">
                            <div x-show="subscribeState === 'idle'" class="flex flex-col gap-4 w-full">
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                    <input type="email" required x-model="email" placeholder="Enter your work email address..." 
                                           class="w-full pl-14 pr-4 py-5 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 font-bold placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 focus:bg-white outline-none transition-all shadow-inner text-lg">
                                </div>
                                <button type="submit" @mouseenter="playHoverSound()" class="w-full py-5 rounded-2xl bg-indigo-600 text-white font-black text-sm uppercase tracking-widest hover:bg-indigo-700 shadow-[0_10px_20px_rgba(79,70,229,0.2)] hover:shadow-[0_15px_30px_rgba(79,70,229,0.4)] hover:-translate-y-0.5 transition-all focus:outline-none flex items-center justify-center gap-3">
                                    Subscribe to Engine <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>

                            {{-- Loading State --}}
                            <div x-show="subscribeState === 'loading'" style="display: none;" class="w-full py-10 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col items-center justify-center gap-4 text-indigo-600 font-bold text-xs uppercase tracking-widest shadow-inner">
                                <i class="fa-solid fa-circle-notch fa-spin text-3xl"></i>
                                <span>Generating Cryptographic Key...</span>
                            </div>

                            {{-- Success State --}}
                            <div x-show="subscribeState === 'success'" style="display: none;" class="w-full py-10 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col items-center justify-center gap-4 text-emerald-600 font-bold text-xs uppercase tracking-widest shadow-sm">
                                <i class="fa-solid fa-check-circle text-4xl"></i>
                                <span>Payload Secured. Welcome.</span>
                            </div>
                        </form>
                    </div>

                    {{-- ================= MODE: CLI (Developer Easter Egg) ================= --}}
                    <div x-show="inputMode === 'cli'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="w-full">
                        <div class="w-full h-48 bg-[#020617] rounded-2xl border border-slate-700 shadow-2xl p-5 font-mono text-sm overflow-hidden relative flex flex-col cursor-text" @click="$refs.cliInput.focus()">
                            <div class="flex gap-2 mb-4">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div><div class="w-3 h-3 rounded-full bg-amber-500"></div><div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto space-y-2 text-slate-300" id="cli-output">
                                <div><span class="text-indigo-400">FinanceAI Engine v3.1.4</span></div>
                                <div>Type <span class="text-emerald-400">subscribe [your_email]</span> to initialize.</div>
                                
                                <template x-for="log in cliLogs" :key="log.id">
                                    <div x-html="log.html"></div>
                                </template>

                                <div x-show="subscribeState === 'idle'" class="flex items-center">
                                    <span class="text-emerald-400 mr-2">admin@node:~$</span>
                                    <input type="text" x-model="cliCommand" x-ref="cliInput" @keydown.enter="processCLI" @keydown="playKeySound()" class="flex-1 bg-transparent border-none outline-none text-white focus:ring-0 p-0" spellcheck="false" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ================= 2. DEEP SYSTEM TELEMETRY (NEW FUN) ================= --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pb-16 border-b border-slate-200">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full pointer-events-none -z-10"></div>
            
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-8 mb-8">
                <div>
                    <h4 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <i class="fa-solid fa-server text-indigo-500"></i> Global Infrastructure Status
                    </h4>
                    <p class="text-sm font-medium text-slate-500 mt-1">Live diagnostics of the FinanceAI Laravel & MySQL Engine.</p>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl shadow-sm">
                    <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">All Systems Nominal</span>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 border-t border-slate-100 pt-8">
                {{-- Metric 1 --}}
                <div class="group">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-database text-slate-300"></i> DB Latency</p>
                    <p class="text-2xl md:text-3xl font-black text-slate-900 font-mono group-hover:text-indigo-600 transition-colors"><span x-text="ping"></span><span class="text-sm text-slate-400">ms</span></p>
                </div>
                {{-- Metric 2 --}}
                <div class="group">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-memory text-slate-300"></i> Redis Hit Rate</p>
                    <p class="text-2xl md:text-3xl font-black text-slate-900 font-mono group-hover:text-indigo-600 transition-colors" x-text="cacheRate + '%'">99.8%</p>
                </div>
                {{-- Metric 3 --}}
                <div class="group">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-microchip text-slate-300"></i> Active ML Nodes</p>
                    <p class="text-2xl md:text-3xl font-black text-slate-900 font-mono group-hover:text-indigo-600 transition-colors" x-text="activeNodes.toLocaleString()">1,204</p>
                </div>
                {{-- Metric 4 --}}
                <div class="group">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-layer-group text-slate-300"></i> Queue Jobs</p>
                    <p class="text-2xl md:text-3xl font-black text-slate-900 font-mono group-hover:text-indigo-600 transition-colors" x-text="queuedJobs">42</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 3. MAIN MEGA-FOOTER NAVIGATION GRID ================= --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pb-20 border-b border-slate-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8">

            {{-- COLUMN 1: Brand Info --}}
            <div class="lg:col-span-2 pr-0 lg:pr-10">
                <a href="{{ route('home') ?? url('/') }}" class="flex items-center gap-3 mb-6 focus:outline-none group w-max">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform">
                        <i class="fa-solid fa-cube text-xl"></i>
                    </div>
                    <span class="text-3xl font-black text-slate-900 tracking-tight">Finance<span class="text-indigo-600">AI</span></span>
                </a>
                <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8 max-w-sm">
                    The deterministic financial engine. We process capital flows through strict Laravel architecture to guarantee zero data loss and absolute mathematical clarity.
                </p>
                <div class="flex gap-3">
                    @php
                        $socialClasses = "w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-md focus:outline-none";
                    @endphp
                    <a href="#" @mouseenter="playHoverSound()" class="{{ $socialClasses }}" aria-label="Twitter"><i class="fa-brands fa-twitter text-sm"></i></a>
                    <a href="#" @mouseenter="playHoverSound()" class="{{ $socialClasses }}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in text-sm"></i></a>
                    <a href="#" @mouseenter="playHoverSound()" class="{{ $socialClasses }}" aria-label="GitHub"><i class="fa-brands fa-github text-sm"></i></a>
                    <a href="#" @mouseenter="playHoverSound()" class="{{ $socialClasses }}" aria-label="Discord"><i class="fa-brands fa-discord text-sm"></i></a>
                </div>
            </div>

            {{-- COLUMN 2: Product (Rich Hover Links) --}}
            <div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6">Platform</h4>
                <ul class="space-y-4">
                    @if(Route::has('features'))
                        <li class="group">
                            <a href="{{ route('features') }}" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                                <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-indigo-400">&rarr;</span> The Core Engine</span>
                                <span class="block text-[10px] text-slate-400 font-medium mt-1 ml-5 hidden group-hover:block transition-all">Explore cryptographic ledgers.</span>
                            </a>
                        </li>
                    @endif
                    @if(Route::has('pricing'))
                        <li class="group">
                            <a href="{{ route('pricing') }}" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                                <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-indigo-400">&rarr;</span> Scalable Quotas</span>
                                <span class="block text-[10px] text-slate-400 font-medium mt-1 ml-5 hidden group-hover:block transition-all">Transparent architectural pricing.</span>
                            </a>
                        </li>
                    @endif
                    @if(Route::has('user.reports.index'))
                        <li class="group">
                            <a href="{{ route('user.reports.index') }}" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                                <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-indigo-400">&rarr;</span> Neural Analytics</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- COLUMN 3: Ecosystem --}}
            <div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6">Ecosystem</h4>
                <ul class="space-y-4">
                    <li class="group">
                        <a href="#" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-emerald-600 transition-colors">
                            <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-emerald-400">&rarr;</span> REST API</span>
                            <span class="block text-[10px] text-slate-400 font-medium mt-1 ml-5 hidden group-hover:block transition-all">JSON endpoints & Sanctum.</span>
                        </a>
                    </li>
                    <li class="group">
                        <a href="#" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-emerald-600 transition-colors">
                            <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-emerald-400">&rarr;</span> Webhook Listeners</span>
                        </a>
                    </li>
                    <li class="group">
                        <a href="#" @mouseenter="playHoverSound()" class="block text-sm font-bold text-slate-500 hover:text-emerald-600 transition-colors">
                            <span class="flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-emerald-400">&rarr;</span> System Changelog</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- COLUMN 4: Legal & Trust --}}
            <div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-900 mb-6">Legal & Trust</h4>
                <ul class="space-y-4">
                    @if(Route::has('privacy'))
                        <li class="group"><a href="{{ route('privacy') }}" @mouseenter="playHoverSound()" class="text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-slate-400">&rarr;</span> Privacy Policy</a></li>
                    @endif
                    @if(Route::has('terms'))
                        <li class="group"><a href="{{ route('terms') }}" @mouseenter="playHoverSound()" class="text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-slate-400">&rarr;</span> Terms of Service</a></li>
                    @endif
                    <li class="group"><a href="#" @mouseenter="playHoverSound()" class="text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2"><span class="w-0 overflow-hidden group-hover:w-3 transition-all text-slate-400">&rarr;</span> Data Processing Addendum</a></li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ================= 4. TECH STACK SHOWCASE (NEW FUN) ================= --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-12 border-b border-slate-200">
        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-6 text-center">Architected With Precision</p>
        <div class="flex flex-wrap justify-center gap-4 md:gap-8">
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" class="w-6 h-6 grayscale group-hover:grayscale-0 transition-all" alt="Laravel">
                <div>
                    <p class="text-xs font-black text-slate-800">Laravel 8</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Core Framework</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <i class="fa-solid fa-database text-2xl text-slate-300 group-hover:text-[#F29111] transition-colors"></i>
                <div>
                    <p class="text-xs font-black text-slate-800">MySQL Strict</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Data Integrity</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <i class="fa-brands fa-aws text-2xl text-slate-300 group-hover:text-[#FF9900] transition-colors"></i>
                <div>
                    <p class="text-xs font-black text-slate-800">AWS Cloud</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Global Nodes</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-3 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                <i class="fa-brands fa-stripe text-2xl text-slate-300 group-hover:text-[#6366f1] transition-colors"></i>
                <div>
                    <p class="text-xs font-black text-slate-800">Stripe API</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Payment Ops</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 5. DUAL INFINITE COMPLIANCE MARQUEE (NEW FUN) ================= --}}
    <div class="border-b border-slate-200 bg-white relative z-10 overflow-hidden py-6 flex flex-col gap-4">
        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-white to-transparent w-32 z-10 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 bg-gradient-to-l from-white to-transparent w-32 z-10 pointer-events-none"></div>
        
        {{-- Marquee 1 (Left) --}}
        <div class="flex whitespace-nowrap animate-[marquee_30s_linear_infinite] items-center gap-16 md:gap-24 opacity-60">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-2 text-sm font-black text-slate-800"><i class="fa-solid fa-shield-halved text-indigo-500"></i> SOC2 Type II Certified</div>
                <div class="flex items-center gap-2 text-sm font-black text-slate-800"><i class="fa-solid fa-lock text-emerald-500"></i> AES-256 Encrypted Vaults</div>
                <div class="flex items-center gap-2 text-sm font-black text-slate-800"><i class="fa-solid fa-file-contract text-sky-500"></i> Fully GDPR Compliant</div>
            @endfor
        </div>
        
        {{-- Marquee 2 (Right - Slower) --}}
        <div class="flex whitespace-nowrap animate-[marqueeReverse_40s_linear_infinite] items-center gap-16 md:gap-24 opacity-40">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-2 text-xs font-black text-slate-600 uppercase tracking-widest"><i class="fa-solid fa-microchip"></i> 99.99% Hardware SLA</div>
                <div class="flex items-center gap-2 text-xs font-black text-slate-600 uppercase tracking-widest"><i class="fa-solid fa-network-wired"></i> Zero-Knowledge Architecture</div>
                <div class="flex items-center gap-2 text-xs font-black text-slate-600 uppercase tracking-widest"><i class="fa-solid fa-brain"></i> Neural Heuristics</div>
            @endfor
        </div>
    </div>

    {{-- ================= 6. BOTTOM COPYRIGHT, REGION & ACCESSIBILITY ================= --}}
    <div class="bg-slate-50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row items-center justify-between gap-6">
            
            <p class="text-xs font-bold text-slate-400">
                © {{ now()->year }} FinanceAI Technologies Inc. <br class="md:hidden"> All mathematical models reserved.
            </p>

            <div class="flex items-center gap-4 sm:gap-6">
                
                {{-- Interactive Region Selector (Alpine) --}}
                <div class="relative" x-data="{ regionOpen: false }" @click.away="regionOpen = false">
                    <button @click="regionOpen = !regionOpen" @mouseenter="playHoverSound()" class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors focus:outline-none bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                        <i class="fa-solid fa-globe text-slate-400"></i>
                        <span x-text="selectedRegion">Global (EN)</span>
                        <i class="fa-solid fa-chevron-up text-[10px] transition-transform" :class="regionOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="regionOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute bottom-full right-0 mb-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden z-50 p-2">
                        <button @click="setRegion('Global (EN)')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Global (English)</button>
                        <button @click="setRegion('Europe (EN)')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Europe (English)</button>
                        <button @click="setRegion('India (EN)')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">India (English)</button>
                    </div>
                </div>

                {{-- UI Preferences / Accessibility Toggles --}}
                <div class="flex items-center gap-2 border-l border-slate-200 pl-4 sm:pl-6">
                    <button @click="toggleSound()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-300 transition-colors shadow-sm focus:outline-none" :title="soundEnabled ? 'Disable UI Sounds' : 'Enable UI Sounds'">
                        <i class="fa-solid" :class="soundEnabled ? 'fa-volume-high text-indigo-500' : 'fa-volume-xmark'"></i>
                    </button>
                    <button @click="triggerGlobalToast({message: 'High contrast mode requires page reload. Feature in Beta.', type: 'warning'})" @mouseenter="playHoverSound()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm focus:outline-none" title="High Contrast Mode">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>
                </div>
                
            </div>

        </div>
    </div>

</footer>

{{-- ================= ALPINE SCRIPT INJECTION ================= --}}
@push('scripts')
<style>
    /* Dual Marquee Animations */
    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    @keyframes marqueeReverse { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
    
    /* 3D Perspective Utility */
    .preserve-3d { transform-style: preserve-3d; perspective: 2000px;}
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('masterFooterEngine', () => ({
            
            // Core States
            email: '',
            subscribeState: 'idle', // idle, loading, success
            selectedRegion: 'Global (EN)',
            soundEnabled: false,
            
            // Interaction Modes
            inputMode: 'ui', // ui or cli
            cliCommand: '',
            cliLogs: [],
            logCounter: 0,
            
            // Live Telemetry
            ping: 12,
            cacheRate: 99.8,
            activeNodes: 1204,
            queuedJobs: 42,
            
            // Live Changelog
            activeLogIndex: 0,
            systemLogs: [
                { version: 'v3.1.4', title: 'Neural Engine optimization deployed.', time: '2m ago' },
                { version: 'v3.1.3', title: 'Stripe API Webhook latency reduced.', time: '1h ago' },
                { version: 'v3.1.2', title: 'New GTU Student pricing tier activated.', time: '5h ago' },
                { version: 'v3.1.1', title: 'AES-256 database rotation complete.', time: '1d ago' }
            ],
            
            init() {
                // Background Telemetry Simulation Engine
                setInterval(() => {
                    this.ping = Math.floor(Math.random() * (18 - 10 + 1) + 10);
                    if(Math.random() > 0.7) {
                        this.activeNodes += Math.floor(Math.random() * 3) - 1; // Fluctuate slightly
                        this.queuedJobs = Math.floor(Math.random() * 100);
                        this.cacheRate = (99.0 + Math.random()).toFixed(1);
                    }
                }, 2000);

                // Cycle through system logs
                setInterval(() => {
                    this.activeLogIndex = (this.activeLogIndex + 1) % this.systemLogs.length;
                }, 4000);
            },

            // 3D Mouse Tilt Logic for the Portal
            tiltPortal(e, el) {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                // Subtle tilt (max 4 degrees)
                const rotateX = ((y - centerY) / centerY) * -4;
                const rotateY = ((x - centerX) / centerX) * 4;
                el.children[0].style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            },
            resetTilt(el) {
                el.children[0].style.transform = `rotateX(0deg) rotateY(0deg)`;
            },

            setMode(mode) {
                this.inputMode = mode;
                if(mode === 'cli') {
                    setTimeout(() => {
                        this.$refs.cliInput.focus();
                    }, 300);
                }
            },

            // Standard UI Form Submission
            submitNewsletter() {
                if(this.email === '' || this.subscribeState !== 'idle') return;
                this.executeSubscription(this.email);
            },

            // CLI Command Parser
            processCLI() {
                if(this.cliCommand.trim() === '') return;
                
                const cmd = this.cliCommand.trim();
                this.addCliLog(`<span class="text-emerald-400">admin@node:~$</span> ${cmd}`);
                this.cliCommand = '';

                const parts = cmd.split(' ');
                
                if (parts[0] === 'subscribe' && parts[1]) {
                    this.addCliLog(`<span class="text-slate-400">Initiating secure handshake...</span>`);
                    this.executeSubscription(parts[1], true);
                } else if (parts[0] === 'help') {
                    this.addCliLog(`<span class="text-sky-400">Available commands: subscribe [email], clear, ping, status</span>`);
                } else if (parts[0] === 'clear') {
                    this.cliLogs = [];
                } else if (parts[0] === 'ping') {
                    this.addCliLog(`<span class="text-slate-400">Pinging core database... ${this.ping}ms. Response: OK.</span>`);
                } else if (parts[0] === 'status') {
                    this.addCliLog(`<span class="text-indigo-400">All systems nominal. AES-256 Active.</span>`);
                } else {
                    this.addCliLog(`<span class="text-rose-400">Command not found: ${parts[0]}</span>`);
                }
            },

            addCliLog(htmlContent) {
                this.cliLogs.push({ id: this.logCounter++, html: htmlContent });
                this.$nextTick(() => {
                    const container = document.getElementById('cli-output');
                    if(container) container.scrollTop = container.scrollHeight;
                });
            },

            // The Core Subscription Logic (Shared by UI and CLI)
            executeSubscription(emailAddress, isCli = false) {
                this.subscribeState = 'loading';
                
                // Simulate network latency
                setTimeout(() => {
                    this.subscribeState = 'success';
                    if(this.soundEnabled) this.playSuccessSound();
                    
                    if(isCli) {
                        this.addCliLog(`<span class="text-emerald-400">SUCCESS:</span> Payload encrypted. ${emailAddress} added to registry.`);
                    }

                    // Trigger global toast
                    this.$dispatch('notify', { 
                        message: 'Cryptographic subscription successful. Welcome aboard.', 
                        type: 'success' 
                    });

                    // Reset
                    setTimeout(() => {
                        this.subscribeState = 'idle';
                        this.email = '';
                    }, 4000);
                    
                }, 1500);
            },

            setRegion(region) {
                this.selectedRegion = region;
                this.regionOpen = false;
                if(this.soundEnabled) this.playClickSound();
                this.$dispatch('notify', { 
                    message: `Routing telemetry to ${region} edge network...`, 
                    type: 'info' 
                });
            },

            // Audio & Haptics Engine
            toggleSound() {
                this.soundEnabled = !this.soundEnabled;
                if(this.soundEnabled) this.playClickSound();
                this.$dispatch('notify', { 
                    message: this.soundEnabled ? 'UI Sound Effects Enabled.' : 'UI Sound Effects Muted.', 
                    type: 'info' 
                });
            },

            playClickSound() {
                if(!this.soundEnabled) return;
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                osc.start();
                osc.stop(ctx.currentTime + 0.1);
            },
            
            playHoverSound() {
                if(!this.soundEnabled) return;
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(400, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(600, ctx.currentTime + 0.05);
                gain.gain.setValueAtTime(0.02, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
                osc.start();
                osc.stop(ctx.currentTime + 0.05);
            },

            playKeySound() {
                if(!this.soundEnabled || this.inputMode !== 'cli') return;
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'square';
                osc.frequency.setValueAtTime(300, ctx.currentTime);
                gain.gain.setValueAtTime(0.05, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
                osc.start();
                osc.stop(ctx.currentTime + 0.05);
            },

            playSuccessSound() {
                if(!this.soundEnabled) return;
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(400, ctx.currentTime);
                osc.frequency.setValueAtTime(600, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            }
        }));
    });
</script>
@endpush