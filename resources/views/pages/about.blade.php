@extends('layouts.landing')

@section('title', 'The Company | FinanceAI Enterprise')
@section('meta_description', 'Discover the mission, global infrastructure, and the architectural team behind FinanceAI.')

@section('content')

<div class="bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden" x-data="aboutEngine()">

    {{-- ================= 1. HERO SHOWCASE & LIVE TICKER ================= --}}
    <section class="relative pt-40 pb-20 lg:pt-48 lg:pb-24 overflow-hidden bg-white border-b border-slate-200/60">
        
        {{-- Hero Ambient Glows --}}
        <div class="absolute top-[-20%] left-[-10%] w-[1000px] h-[1000px] bg-indigo-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[800px] bg-purple-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-8 reveal-up shadow-sm">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span></span>
                The FinanceAI Architecture
            </div>

            <h1 class="text-5xl sm:text-6xl md:text-8xl font-black text-slate-900 tracking-tighter leading-[1.05] max-w-5xl mx-auto reveal-up" style="transition-delay: 100ms;">
                We are building the future of <br class="hidden xl:block">
                <span class="relative inline-block mt-2">
                    <span class="relative z-10 bg-gradient-to-r from-indigo-600 via-brand to-purple-600 bg-clip-text text-transparent">financial intelligence.</span>
                    <span class="absolute bottom-2 left-0 w-full h-5 bg-indigo-100 -z-10 transform -rotate-1 rounded-full"></span>
                </span>
            </h1>

            <p class="mt-8 text-lg md:text-xl text-slate-500 font-medium leading-relaxed max-w-3xl mx-auto reveal-up" style="transition-delay: 200ms;">
                FinanceAI empowers organizational nodes and high-net-worth families with real-time telemetry, predictive AI heuristics, and cryptographic ledgers. We replace chaos with absolute mathematical truth.
            </p>

            {{-- 🔥 BEAST MODE: Live Global Ticker Engine --}}
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto reveal-up" style="transition-delay: 300ms;">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden group hover:border-indigo-300 transition-colors">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Live Nodes</p>
                    <p class="text-2xl font-black text-slate-900 font-mono" x-text="liveNodes.toLocaleString()"></p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden group hover:border-emerald-300 transition-colors col-span-2 md:col-span-2">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 via-emerald-500/5 to-emerald-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-1000 animate-[shimmer_2s_infinite]"></div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mb-2 flex items-center justify-center gap-1.5"><i class="fa-solid fa-lock"></i> Capital Secured (USD)</p>
                    <p class="text-3xl font-black text-slate-900 font-mono" x-text="'$' + capitalSecured.toLocaleString()"></p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden group hover:border-sky-300 transition-colors">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">AI Ops/Sec</p>
                    <p class="text-2xl font-black text-slate-900 font-mono" x-text="aiOps"></p>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 2. INSTITUTIONAL BACKERS (TRUST) ================= --}}
    <section class="py-12 bg-white relative z-10 border-b border-slate-200/60 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-up">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-8">Backed by Tier-1 Capital & Infrastructure</p>
            <div class="flex flex-wrap justify-center items-center gap-12 lg:gap-24 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <h3 class="text-2xl font-black text-slate-800 tracking-tighter">SEQUOIA<span class="text-rose-500">/</span></h3>
                <h3 class="text-2xl font-black text-slate-800 tracking-tighter">a16z</h3>
                <h3 class="text-2xl font-black text-slate-800 tracking-tighter"><i class="fa-brands fa-y-combinator text-orange-500 mr-1"></i> Combinator</h3>
                <h3 class="text-xl font-black text-slate-800 tracking-widest uppercase"><i class="fa-brands fa-aws"></i> Partner</h3>
            </div>
        </div>
    </section>

    {{-- ================= 3. MISSION & VISION (BENTO GRID) ================= --}}
    <section class="py-32 bg-[#f8fafc] relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid lg:grid-cols-2 gap-8 items-stretch">
                
                {{-- Mission --}}
                <div class="bg-white/80 backdrop-blur-2xl rounded-[3rem] border border-slate-200 p-10 md:p-14 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden reveal-up group hover:shadow-[0_20px_50px_rgb(0,0,0,0.08)] transition-all duration-500">
                    <div class="absolute top-0 left-0 w-2 h-full bg-indigo-500"></div>
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100 shadow-sm mb-8 relative z-10"><i class="fa-solid fa-bullseye text-2xl"></i></div>
                    
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-6 relative z-10">The Mission Directive</h2>
                    <p class="text-lg text-slate-500 font-medium leading-relaxed relative z-10">
                        To simplify complex financial systems using deterministic automation and artificial intelligence, allowing users to focus entirely on capital growth rather than manual tracking and spreadsheet maintenance.
                    </p>
                </div>

                {{-- Vision --}}
                <div class="bg-slate-900 rounded-[3rem] border border-slate-800 p-10 md:p-14 shadow-2xl relative overflow-hidden reveal-up group" style="transition-delay: 100ms;">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
                    <div class="absolute top-0 left-0 w-2 h-full bg-sky-400"></div>
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-sky-500/20 rounded-full blur-3xl pointer-events-none group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="w-16 h-16 bg-white/10 text-sky-400 rounded-2xl flex items-center justify-center border border-white/20 backdrop-blur-md mb-8 relative z-10"><i class="fa-regular fa-eye text-2xl"></i></div>
                    
                    <h2 class="text-4xl font-black text-white tracking-tight mb-6 relative z-10">The Global Vision</h2>
                    <p class="text-lg text-slate-400 font-medium leading-relaxed relative z-10">
                        A world where financial decisions are inherently intelligent, highly predictive, and effortlessly collaborative—powered by an infrastructure that understands economic behavior before it happens.
                    </p>
                </div>

            </div>

            {{-- Security & Trust --}}
            <div class="mt-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[3rem] p-10 md:p-14 shadow-xl shadow-indigo-500/20 relative overflow-hidden reveal-up text-white text-center">
                <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl h-32 bg-white/20 rounded-full blur-[100px] pointer-events-none"></div>
                
                <div class="relative z-10">
                    <i class="fa-solid fa-shield-halved text-5xl mb-6 text-indigo-200"></i>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight mb-4">Zero-Knowledge Architecture</h2>
                    <p class="text-indigo-100 font-medium max-w-3xl mx-auto leading-relaxed text-lg">
                        We believe financial privacy is a fundamental human right. FinanceAI employs 256-bit AES encryption. Our engineers cannot see your capital inflows, and our AI models process heuristics locally within isolated secure nodes.
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 4. ENGINEERING PHILOSOPHY (NEW FUN) ================= --}}
    <section class="py-32 bg-white border-y border-slate-200/60 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest mb-6">
                    <i class="fa-solid fa-code text-indigo-500"></i> How we build
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">Engineering First Principles</h2>
                <p class="text-slate-500 text-lg font-medium">We don't build software. We engineer robust financial systems designed to outlast legacy infrastructure.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Principle 1 --}}
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 hover:bg-white hover:shadow-[0_10px_40px_rgba(0,0,0,0.05)] hover:border-indigo-200 transition-all duration-300 reveal-up group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-bolt text-xl"></i></div>
                    <h3 class="text-lg font-black text-slate-900 mb-3">Sub-Millisecond Latency</h3>
                    <p class="text-sm text-slate-500 font-medium">Financial telemetry is useless if it's delayed. Our databases are indexed for instant retrieval.</p>
                </div>
                
                {{-- Principle 2 --}}
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 hover:bg-white hover:shadow-[0_10px_40px_rgba(0,0,0,0.05)] hover:border-emerald-200 transition-all duration-300 reveal-up group" style="transition-delay: 100ms;">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-lock text-xl"></i></div>
                    <h3 class="text-lg font-black text-slate-900 mb-3">Mathematical Truth</h3>
                    <p class="text-sm text-slate-500 font-medium">Ledgers are immutable. Once a transaction is hashed, it cannot be silently altered.</p>
                </div>

                {{-- Principle 3 --}}
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 hover:bg-white hover:shadow-[0_10px_40px_rgba(0,0,0,0.05)] hover:border-sky-200 transition-all duration-300 reveal-up group" style="transition-delay: 200ms;">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-wand-magic-sparkles text-xl"></i></div>
                    <h3 class="text-lg font-black text-slate-900 mb-3">Frictionless UX</h3>
                    <p class="text-sm text-slate-500 font-medium">Enterprise power should not require an enterprise manual. Design must be intuitive.</p>
                </div>

                {{-- Principle 4 --}}
                <div class="bg-slate-50 border border-slate-200 rounded-[2rem] p-8 hover:bg-white hover:shadow-[0_10px_40px_rgba(0,0,0,0.05)] hover:border-rose-200 transition-all duration-300 reveal-up group" style="transition-delay: 300ms;">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform"><i class="fa-solid fa-network-wired text-xl"></i></div>
                    <h3 class="text-lg font-black text-slate-900 mb-3">Distributed Redundancy</h3>
                    <p class="text-sm text-slate-500 font-medium">No single point of failure. Your data is sharded and backed up across multiple global nodes.</p>
                </div>

            </div>

        </div>
    </section>

    {{-- ================= 5. GLOBAL INFRASTRUCTURE TOPOLOGY (NEW FUN!) ================= --}}
    <section class="py-32 bg-slate-900 relative z-10 overflow-hidden text-white" x-data="{ activeRegion: 'us-east' }">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-full bg-indigo-500/10 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-6">Global Node Topology</h2>
                <p class="text-indigo-200 text-lg font-medium">FinanceAI operates on a multi-region deployment to guarantee localized latency and strict sovereign data compliance.</p>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 items-center reveal-up">
                
                {{-- Left: Region Selector --}}
                <div class="lg:col-span-4 space-y-3">
                    <button @click="activeRegion = 'us-east'" class="w-full flex items-center justify-between p-5 rounded-[1.5rem] border transition-all duration-300 focus:outline-none group" :class="activeRegion === 'us-east' ? 'bg-indigo-600/20 border-indigo-500 text-white' : 'bg-slate-800/50 border-slate-700 text-slate-400 hover:bg-slate-800'">
                        <div class="flex items-center gap-3"><span class="text-xl">🇺🇸</span> <span class="font-black">US-East (N. Virginia)</span></div>
                        <span class="w-2 h-2 rounded-full" :class="activeRegion === 'us-east' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"></span>
                    </button>
                    <button @click="activeRegion = 'eu-central'" class="w-full flex items-center justify-between p-5 rounded-[1.5rem] border transition-all duration-300 focus:outline-none group" :class="activeRegion === 'eu-central' ? 'bg-indigo-600/20 border-indigo-500 text-white' : 'bg-slate-800/50 border-slate-700 text-slate-400 hover:bg-slate-800'">
                        <div class="flex items-center gap-3"><span class="text-xl">🇪🇺</span> <span class="font-black">EU-Central (Frankfurt)</span></div>
                        <span class="w-2 h-2 rounded-full" :class="activeRegion === 'eu-central' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"></span>
                    </button>
                    <button @click="activeRegion = 'ap-south'" class="w-full flex items-center justify-between p-5 rounded-[1.5rem] border transition-all duration-300 focus:outline-none group" :class="activeRegion === 'ap-south' ? 'bg-indigo-600/20 border-indigo-500 text-white' : 'bg-slate-800/50 border-slate-700 text-slate-400 hover:bg-slate-800'">
                        <div class="flex items-center gap-3"><span class="text-xl">🇸🇬</span> <span class="font-black">AP-South (Singapore)</span></div>
                        <span class="w-2 h-2 rounded-full" :class="activeRegion === 'ap-south' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"></span>
                    </button>
                </div>

                {{-- Right: Live Region Stats --}}
                <div class="lg:col-span-8 bg-slate-800/50 backdrop-blur-xl border border-slate-700 rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="grid sm:grid-cols-2 gap-8 relative z-10">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Current Region</p>
                            <h3 class="text-3xl font-black text-white mb-6" x-text="activeRegion === 'us-east' ? 'US-East-1' : (activeRegion === 'eu-central' ? 'EU-Central-1' : 'AP-South-1')"></h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                                    <span class="text-sm font-bold text-slate-400">Database Status</span>
                                    <span class="text-xs font-black uppercase tracking-widest text-emerald-400 flex items-center gap-2"><i class="fa-solid fa-check"></i> Nominal</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                                    <span class="text-sm font-bold text-slate-400">AI Inference Engine</span>
                                    <span class="text-xs font-black uppercase tracking-widest text-emerald-400 flex items-center gap-2"><i class="fa-solid fa-check"></i> Nominal</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                                    <span class="text-sm font-bold text-slate-400">Data Sovereignty</span>
                                    <span class="text-xs font-black uppercase tracking-widest text-indigo-300 flex items-center gap-2" x-text="activeRegion === 'eu-central' ? 'GDPR Compliant' : 'Local Sandbox'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col justify-center items-center bg-slate-900 rounded-3xl border border-slate-800 p-8 shadow-inner">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Region Latency</p>
                            <div class="relative w-32 h-32 flex items-center justify-center">
                                <svg class="absolute inset-0 w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#1e293b" stroke-width="8"></circle>
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#10b981" stroke-width="8" stroke-dasharray="283" stroke-dashoffset="60" class="transition-all duration-1000"></circle>
                                </svg>
                                <span class="text-3xl font-black text-white font-mono" x-text="activeRegion === 'us-east' ? '12' : (activeRegion === 'eu-central' ? '18' : '24')"></span>
                                <span class="absolute bottom-6 text-[10px] font-bold text-slate-500">ms</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 6. LEADERSHIP PROTOCOL (TEAM) ================= --}}
    <section class="py-32 bg-white relative z-10 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">The Architect Protocol</h2>
                <p class="text-slate-500 text-lg font-medium">Engineered by specialists in cryptography, corporate finance, and machine learning.</p>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">

                {{-- Team Member 1 --}}
                <div class="bg-slate-50 rounded-[2.5rem] border border-slate-200 p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 group reveal-up relative overflow-hidden text-center">
                    <div class="w-32 h-32 mx-auto rounded-[2rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-4xl shadow-xl border-4 border-white mb-6 group-hover:scale-105 transition-transform duration-500 transform -rotate-3 group-hover:rotate-0">
                        F
                    </div>
                    <span class="absolute top-6 right-6 px-2.5 py-1 rounded-md bg-rose-100 text-rose-600 text-[8px] font-black uppercase tracking-widest border border-rose-200 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Level 5 Clearance</span>
                    
                    <h3 class="text-2xl font-black text-slate-900">Founder & CEO</h3>
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1 mb-4">Vision & Strategy</p>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed px-4">Directing the global architecture and defining the AI integration pathways.</p>
                    
                    <div class="mt-8 flex justify-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 flex items-center justify-center shadow-sm transition-all focus:outline-none"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>

                {{-- Team Member 2 --}}
                <div class="bg-slate-50 rounded-[2.5rem] border border-slate-200 p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 group reveal-up relative overflow-hidden text-center" style="transition-delay: 100ms;">
                    <div class="w-32 h-32 mx-auto rounded-[2rem] bg-gradient-to-br from-sky-400 to-indigo-500 flex items-center justify-center text-white font-black text-4xl shadow-xl border-4 border-white mb-6 group-hover:scale-105 transition-transform duration-500 transform -rotate-3 group-hover:rotate-0">
                        T
                    </div>
                    <span class="absolute top-6 right-6 px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-600 text-[8px] font-black uppercase tracking-widest border border-emerald-200 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Level 4 Clearance</span>
                    
                    <h3 class="text-2xl font-black text-slate-900">Head of Technology</h3>
                    <p class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mt-1 mb-4">Infrastructure</p>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed px-4">Securing the ledger cryptography and maintaining 99.9% network uptime.</p>
                    
                    <div class="mt-8 flex justify-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-sky-500 hover:border-sky-300 hover:bg-sky-50 flex items-center justify-center shadow-sm transition-all focus:outline-none"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>

                {{-- Team Member 3 --}}
                <div class="bg-slate-50 rounded-[2.5rem] border border-slate-200 p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-500 group reveal-up sm:col-span-2 md:col-span-1 relative overflow-hidden text-center" style="transition-delay: 200ms;">
                    <div class="w-32 h-32 mx-auto rounded-[2rem] bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-black text-4xl shadow-xl border-4 border-white mb-6 group-hover:scale-105 transition-transform duration-500 transform -rotate-3 group-hover:rotate-0">
                        G
                    </div>
                    <span class="absolute top-6 right-6 px-2.5 py-1 rounded-md bg-amber-100 text-amber-600 text-[8px] font-black uppercase tracking-widest border border-amber-200 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Level 3 Clearance</span>
                    
                    <h3 class="text-2xl font-black text-slate-900">Head of Growth</h3>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1 mb-4">Global Scaling</p>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed px-4">Deploying FinanceAI systems to organizational nodes and institutional partners.</p>
                    
                    <div class="mt-8 flex justify-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:border-emerald-300 hover:bg-emerald-50 flex items-center justify-center shadow-sm transition-all focus:outline-none"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 7. CRYPTOGRAPHIC TIMELINE (ROADMAP) ================= --}}
    <section class="py-32 bg-[#f8fafc] border-t border-slate-200/60 relative overflow-hidden">
        <div class="absolute -left-[20%] top-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center mb-24 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">Deployment Vector</h2>
                <p class="text-slate-500 font-medium text-lg">The sequence of deployment for the FinanceAI Master Engine.</p>
            </div>

            <div class="relative">
                {{-- Center Track --}}
                <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-1 bg-slate-200 transform md:-translate-x-1/2 rounded-full"></div>
                {{-- Highlight Track --}}
                <div class="absolute left-8 md:left-1/2 top-0 h-[45%] w-1 bg-gradient-to-b from-indigo-500 to-sky-400 transform md:-translate-x-1/2 rounded-full shadow-[0_0_12px_rgba(79,70,229,0.8)] z-0"></div>

                <div class="space-y-16 relative z-10">
                    
                    {{-- Node 1 --}}
                    <div class="relative flex items-center md:justify-end reveal-up">
                        <div class="hidden md:block w-1/2 pr-16 text-right">
                            <h3 class="text-2xl font-black text-slate-900">Alpha Genesis</h3>
                            <p class="text-sm text-slate-500 font-medium mt-3 leading-relaxed">Deployment of core cryptographic ledgers and user identity access management (IAM).</p>
                        </div>
                        <div class="absolute left-8 md:left-1/2 w-8 h-8 bg-indigo-600 rounded-full border-[6px] border-white shadow-md transform -translate-x-1/2 flex items-center justify-center ring-4 ring-indigo-500/20 z-10"><div class="w-2 h-2 bg-white rounded-full"></div></div>
                        <div class="pl-24 md:hidden w-full">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600">Phase 01</span>
                            <h3 class="text-xl font-black text-slate-900 mt-1">Alpha Genesis</h3>
                            <p class="text-sm text-slate-500 font-medium mt-3 border border-slate-200 bg-white p-5 rounded-2xl shadow-sm">Deployment of core cryptographic ledgers and user identity access management.</p>
                        </div>
                        <div class="hidden md:flex w-1/2 pl-16 justify-start"><span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-100 shadow-sm">Completed</span></div>
                    </div>

                    {{-- Node 2 --}}
                    <div class="relative flex items-center md:justify-start reveal-up">
                        <div class="hidden md:flex w-1/2 pr-16 justify-end"><span class="px-4 py-2 bg-sky-50 text-sky-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-sky-100 shadow-sm">Current Vector</span></div>
                        <div class="absolute left-8 md:left-1/2 w-8 h-8 bg-sky-500 rounded-full border-[6px] border-white shadow-md transform -translate-x-1/2 flex items-center justify-center ring-4 ring-sky-500/20 z-10 animate-pulse"><div class="w-2 h-2 bg-white rounded-full"></div></div>
                        <div class="pl-24 md:pl-16 md:w-1/2 w-full text-left">
                            <span class="md:hidden text-[10px] font-black uppercase tracking-widest text-sky-600">Phase 02 (Current)</span>
                            <h3 class="text-xl md:text-2xl font-black text-slate-900 md:mt-0 mt-1">Neural Integration</h3>
                            <p class="text-sm text-slate-500 font-medium mt-3 md:bg-transparent md:border-none md:p-0 md:shadow-none border border-slate-200 bg-white p-5 rounded-2xl shadow-sm leading-relaxed">Activation of AI heuristic engines for predictive burn-rate analysis and autonomous categorization.</p>
                        </div>
                    </div>

                    {{-- Node 3 --}}
                    <div class="relative flex items-center md:justify-end reveal-up">
                        <div class="hidden md:block w-1/2 pr-16 text-right opacity-50 hover:opacity-100 transition-opacity duration-300">
                            <h3 class="text-2xl font-black text-slate-900">Institutional Sync</h3>
                            <p class="text-sm text-slate-500 font-medium mt-3 leading-relaxed">API connections to global banking infrastructure for automated ledger synchronization.</p>
                        </div>
                        <div class="absolute left-8 md:left-1/2 w-8 h-8 bg-slate-200 rounded-full border-[6px] border-white shadow-sm transform -translate-x-1/2 z-10"></div>
                        <div class="pl-24 md:hidden w-full opacity-60">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Phase 03</span>
                            <h3 class="text-xl font-black text-slate-900 mt-1">Institutional Sync</h3>
                            <p class="text-sm text-slate-500 font-medium mt-3 border border-slate-200 bg-white p-5 rounded-2xl shadow-sm">API connections to global banking infrastructure for automated ledger synchronization.</p>
                        </div>
                        <div class="hidden md:flex w-1/2 pl-16 justify-start"><span class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200">Pending Authorization</span></div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ================= 8. PRE-FOOTER CTA ================= --}}
    <section class="py-32 px-4 sm:px-6 lg:px-8 relative z-10 border-t border-slate-200/60">
        <div class="max-w-5xl mx-auto bg-slate-900 rounded-[3rem] border border-slate-800 p-12 md:p-20 text-center relative overflow-hidden shadow-2xl reveal-up">
            
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl h-64 bg-indigo-500/30 rounded-full blur-[100px] pointer-events-none"></div>

            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight relative z-10">
                Ready to initialize <br class="hidden sm:block"> your financial node?
            </h2>
            <p class="mt-6 text-indigo-200 font-medium max-w-2xl mx-auto relative z-10 text-lg">
                Join thousands of users who have abandoned spreadsheets for absolute cryptographic clarity.
            </p>

            <div class="mt-12 relative z-10 flex justify-center">
                <a href="{{ route('register') ?? '#' }}" class="px-10 py-5 bg-white text-slate-900 rounded-2xl font-black text-sm uppercase tracking-widest shadow-[0_0_40px_rgba(255,255,255,0.2)] hover:shadow-[0_0_60px_rgba(255,255,255,0.4)] hover:scale-105 transition-all duration-300 focus:outline-none group">
                    Deploy Infrastructure <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            
            <p class="mt-8 text-[10px] font-black uppercase tracking-widest text-slate-500 relative z-10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500"></i> 256-Bit Encrypted Connection
            </p>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<style>
    /* Scroll Reveal Initial State */
    .reveal-up {
        opacity: 0;
        transform: translateY(40px);
        transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal-up.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aboutEngine', () => ({
        
        // Live Ticker State
        capitalSecured: 1425890500,
        liveNodes: 12458,
        aiOps: 450,

        init() {
            this.initScrollReveal();
            this.initLiveTickers();
        },

        // Real-time Simulation Engine
        initLiveTickers() {
            // Capital goes up by random amount every 3 seconds
            setInterval(() => {
                this.capitalSecured += Math.floor(Math.random() * 5000) + 1000;
            }, 3000);

            // Nodes randomly fluctuate slightly up
            setInterval(() => {
                if(Math.random() > 0.5) this.liveNodes += 1;
            }, 8000);

            // AI Ops fluctuate up and down rapidly
            setInterval(() => {
                this.aiOps = Math.floor(400 + Math.random() * 150);
            }, 1500);
        },

        initScrollReveal() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        
                        // Smart trigger for counters
                        if (entry.target.classList.contains('kpi-counter') && !entry.target.dataset.counted) {
                            this.animateValue(entry.target);
                            entry.target.dataset.counted = "true"; 
                        }
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.reveal-up, .kpi-counter').forEach(el => observer.observe(el));
        },

        // Flawless Counter Animation Engine
        animateValue(obj) {
            let startTimestamp = null;
            const duration = 2500;
            const targetStr = obj.dataset.target;
            
            const isNumeric = /^\d+$/.test(targetStr);
            const prefix = obj.dataset.prefix || '';
            const suffix = obj.dataset.suffix || '';

            if (!isNumeric) {
                obj.innerHTML = prefix + targetStr + suffix;
                return;
            }

            const target = parseInt(targetStr, 10);
            
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                
                const ease = 1 - Math.pow(1 - progress, 4); 
                const currentVal = Math.floor(ease * target);
                
                obj.innerHTML = prefix + currentVal + suffix;
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    obj.innerHTML = prefix + target + suffix;
                }
            };
            window.requestAnimationFrame(step);
        }
    }));
});
</script>
@endpush