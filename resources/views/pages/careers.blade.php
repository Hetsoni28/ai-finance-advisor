@extends('layouts.landing')

@section('title', 'Careers & Culture | FinanceAI Enterprise')
@section('meta_description', 'Join the FinanceAI architecture team. Build the future of financial intelligence with world-class engineers.')

@section('content')

@php
    // ================= ENTERPRISE SSR PAYLOAD =================
    
    $benefits = [
        ['icon' => 'fa-globe', 'title' => 'Remote-First Protocol', 'desc' => 'Work asynchronously from anywhere on Earth. We optimize for deep work, not desk time.'],
        ['icon' => 'fa-heart-pulse', 'title' => 'Comprehensive Coverage', 'desc' => 'Top-tier medical, dental, and vision insurance with 100% premium coverage for dependents.'],
        ['icon' => 'fa-chart-pie', 'title' => 'Meaningful Equity', 'desc' => 'We want owners. Every role includes a generous, transparent stock option grant.'],
        ['icon' => 'fa-laptop-code', 'title' => 'Command Center Setup', 'desc' => '₹250,000 upfront stipend to architect your perfect home office and development rig.'],
        ['icon' => 'fa-graduation-cap', 'title' => 'Continuous Learning', 'desc' => '₹50,000 annual budget for AWS certifications, engineering conferences, and literature.'],
        ['icon' => 'fa-plane', 'title' => 'Mandatory Downtime', 'desc' => 'Unlimited PTO, with a strictly enforced minimum of 20 days per year to prevent burnout.']
    ];

    $jobs = [
        ['id' => 'role-1', 'dept' => 'Engineering', 'title' => 'Senior Backend Engineer (Laravel)', 'loc' => 'Remote / Global', 'type' => 'Full-time', 'hot' => true],
        ['id' => 'role-2', 'dept' => 'Engineering', 'title' => 'AI/ML Heuristics Researcher', 'loc' => 'Remote / EMEA', 'type' => 'Full-time', 'hot' => true],
        ['id' => 'role-3', 'dept' => 'Engineering', 'title' => 'Frontend Architect (Alpine.js / Vue)', 'loc' => 'Remote / APAC', 'type' => 'Full-time', 'hot' => false],
        ['id' => 'role-4', 'dept' => 'Security', 'title' => 'Cryptographic Security Analyst', 'loc' => 'Remote / Global', 'type' => 'Full-time', 'hot' => true],
        ['id' => 'role-5', 'dept' => 'Design', 'title' => 'Principal Product Designer (UI/UX)', 'loc' => 'Remote / US', 'type' => 'Full-time', 'hot' => false],
        ['id' => 'role-6', 'dept' => 'Operations', 'title' => 'Technical Support Engineer', 'loc' => 'Ahmedabad, IN', 'type' => 'Full-time', 'hot' => false],
        ['id' => 'role-7', 'dept' => 'Marketing', 'title' => 'Developer Advocate / Tech Writer', 'loc' => 'Remote / Global', 'type' => 'Contract', 'hot' => false],
    ];

    $departments = array_unique(array_column($jobs, 'dept'));
    sort($departments);
@endphp

<div class="bg-[#fcf9f2] font-sans selection:bg-[#bacdf3] selection:text-[#0f172a] relative overflow-hidden flex flex-col min-h-screen pt-24"
     x-data="careersEngine()">

    {{-- Ambient Backgrounds (Premium Light Fintech Palette) --}}
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[800px] h-[800px] bg-[#bacdf3]/30 blur-[150px] rounded-full animate-float"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-[#9fb2df]/20 blur-[120px] rounded-full animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
    </div>

    {{-- ================= 1. HERO SECTION ================= --}}
    <section class="relative pt-20 pb-20 lg:pt-32 lg:pb-28 overflow-hidden z-10 border-b border-[#bacdf3]/40 bg-white/60 backdrop-blur-2xl">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[10px] font-black uppercase tracking-widest mb-8 shadow-sm reveal-up">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#7284b5] opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-[#7284b5]"></span></span>
                We're Hiring — {{ count($jobs) }} Open Roles
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-[5rem] font-black text-slate-900 tracking-tight leading-[1.05] max-w-5xl mx-auto reveal-up" style="transition-delay: 100ms;">
                Engineer the future of <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#7284b5] via-[#879ac9] to-[#bacdf3] relative inline-block pb-2">
                    financial infrastructure.
                </span>
            </h1>

            <p class="mt-8 text-lg md:text-xl text-slate-500 font-medium leading-relaxed max-w-3xl mx-auto reveal-up" style="transition-delay: 200ms;">
                We are a globally distributed collective of cryptographers, heuristic researchers, and software architects building the world's most secure financial intelligence platform.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 reveal-up" style="transition-delay: 300ms;">
                <button @click="scrollToJobs()" @mouseenter="playHover()" class="magnetic-target px-10 py-5 bg-[#7284b5] text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-[0_15px_40px_rgba(114,132,181,0.4)] hover:bg-[#616dab] transition-all hover:-translate-y-1 focus:outline-none flex items-center gap-3 cursor-none">
                    View Open Nodes <i class="fa-solid fa-arrow-down"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- Scale Telemetry Ribbon --}}
    <div class="border-b border-[#bacdf3]/40 bg-white relative z-10 overflow-hidden py-5 shadow-sm">
        <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
        
        <div class="flex whitespace-nowrap animate-marquee items-center gap-16 px-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#fcf9f2] text-[#7284b5] border border-[#bacdf3] flex items-center justify-center"><i class="fa-solid fa-server text-[10px]"></i></div>
                    <span class="text-sm font-black text-slate-900 tracking-tight">10,000+ Active Nodes</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#fcf9f2] text-[#7284b5] border border-[#bacdf3] flex items-center justify-center"><i class="fa-solid fa-money-bill-transfer text-[10px]"></i></div>
                    <span class="text-sm font-black text-slate-900 tracking-tight">₹1.2B Capital Processed</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#fcf9f2] text-[#7284b5] border border-[#bacdf3] flex items-center justify-center"><i class="fa-solid fa-shield-check text-[10px]"></i></div>
                    <span class="text-sm font-black text-slate-900 tracking-tight">99.99% Core Uptime</span>
                </div>
            @endfor
        </div>
    </div>

    {{-- ================= 2. CULTURE VALUES (CSS BENTO BOX) ================= --}}
    <section class="py-32 bg-[#fcf9f2] relative z-10">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">Our Operating Principles</h2>
                <p class="text-slate-500 text-xl font-medium">The architectural tenets that define how we build, ship, and scale.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 auto-rows-[280px]">
                
                {{-- Principle 1: Large --}}
                <div class="md:col-span-2 bg-white rounded-[2.5rem] border border-[#bacdf3]/50 p-10 hover:shadow-[0_20px_50px_rgba(114,132,181,0.15)] transition-all duration-500 group relative overflow-hidden flex flex-col justify-between reveal-up">
                    <div class="absolute inset-0 pattern-grid-lg opacity-30 z-0"></div>
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-[#bacdf3]/20 rounded-full blur-[80px] pointer-events-none group-hover:bg-[#bacdf3]/40 transition-colors duration-700 z-0"></div>
                    
                    <div class="relative z-10 w-14 h-14 bg-[#fcf9f2] text-[#7284b5] rounded-2xl flex items-center justify-center border border-[#bacdf3] mb-6 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-rocket text-xl"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">Ship Fast, Ship Safe.</h3>
                        <p class="text-slate-500 font-medium text-lg max-w-md leading-relaxed">We deploy multiple times per day utilizing zero-downtime CI/CD pipelines and automated algorithmic rollback safety nets.</p>
                    </div>
                </div>

                {{-- Principle 2: Small --}}
                <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 p-10 hover:shadow-[0_20px_50px_rgba(15,23,42,0.4)] transition-all duration-500 group relative overflow-hidden flex flex-col justify-between reveal-up" style="transition-delay: 100ms;">
                    <div class="absolute inset-0 pattern-dots opacity-10 z-0"></div>
                    <div class="relative z-10 w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center border border-white/20 mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-brain text-lg"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Intellectual Rigor</h3>
                        <p class="text-slate-400 font-medium text-sm leading-relaxed">We challenge assumptions with data. Every architectural decision is backed by telemetry.</p>
                    </div>
                </div>

                {{-- Principle 3: Small --}}
                <div class="bg-white rounded-[2.5rem] border border-[#bacdf3]/50 p-10 hover:shadow-[0_20px_50px_rgba(114,132,181,0.15)] transition-all duration-500 group relative overflow-hidden flex flex-col justify-between reveal-up" style="transition-delay: 150ms;">
                    <div class="absolute inset-0 pattern-waves opacity-20 z-0"></div>
                    <div class="relative z-10 w-12 h-12 bg-[#fcf9f2] text-[#7284b5] rounded-xl flex items-center justify-center border border-[#bacdf3] mb-6 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">Radical Ownership</h3>
                        <p class="text-slate-500 font-medium text-sm leading-relaxed">Engineers own features end-to-end — from database migration to production monitoring.</p>
                    </div>
                </div>

                {{-- Principle 4: Large --}}
                <div class="md:col-span-2 bg-white rounded-[2.5rem] border border-[#bacdf3]/50 p-10 hover:shadow-[0_20px_50px_rgba(114,132,181,0.15)] transition-all duration-500 group relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-10 reveal-up" style="transition-delay: 200ms;">
                    <div class="absolute inset-0 pattern-isometric opacity-30 z-0"></div>
                    
                    <div class="relative z-10 flex-1">
                        <div class="w-14 h-14 bg-[#fcf9f2] text-[#7284b5] rounded-2xl flex items-center justify-center border border-[#bacdf3] mb-6 shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-globe text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-3 tracking-tight">Remote-First DNA.</h3>
                        <p class="text-slate-500 font-medium text-lg leading-relaxed">We optimize for asynchronous communication and deep work sessions. Your timezone doesn't matter; your code does.</p>
                    </div>
                    
                    {{-- Interactive Visual Node --}}
                    <div class="relative z-10 w-full md:w-64 h-48 bg-[#0f172a] rounded-2xl border border-slate-700 shadow-inner flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.8)] animate-pulse"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.8)] animate-pulse" style="animation-delay: 0.3s;"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)] animate-pulse" style="animation-delay: 0.6s;"></span>
                        </div>
                        <p class="absolute bottom-4 left-0 w-full text-center text-[9px] font-black uppercase tracking-widest text-slate-500 font-mono">Global Sync Active</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= 3. INTERACTIVE COMPENSATION CALCULATOR (BEAST MODE) ================= --}}
    <section class="py-32 bg-white relative z-10 border-y border-[#bacdf3]/40 overflow-hidden" x-data="compCalculator()">
        <div class="absolute top-0 left-0 w-[800px] h-[800px] bg-[#bacdf3]/20 rounded-full blur-[150px] pointer-events-none"></div>
        
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-20 items-center">
            
            <div class="reveal-up">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#fcf9f2] border border-[#bacdf3] text-[#7284b5] text-[10px] font-black uppercase tracking-widest mb-6 shadow-sm">
                    <i class="fa-solid fa-coins"></i> Total Rewards
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-6 leading-tight">We pay for top-tier architecture.</h2>
                <p class="text-lg text-slate-500 font-medium leading-relaxed mb-10">We believe in radical transparency. Calculate your projected total compensation package based on your engineering level. Everyone gets equity.</p>

                <div class="bg-[#fcf9f2] rounded-3xl p-8 border border-[#bacdf3]/50 shadow-inner">
                    <div class="flex justify-between items-center mb-6">
                        <label class="text-sm font-black text-slate-900 uppercase tracking-widest">Engineering Level</label>
                        <span class="px-4 py-2 bg-white rounded-xl border border-[#bacdf3] text-[#7284b5] font-black font-mono shadow-sm" x-text="'Level ' + level"></span>
                    </div>
                    
                    <div class="relative pt-2 pb-6">
                        <input type="range" x-model="level" min="3" max="6" step="1" @input="updateComp(); playKeySound()" 
                               class="w-full h-3 bg-[#bacdf3]/40 rounded-full appearance-none cursor-pointer accent-[#7284b5] outline-none">
                        
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-4 px-1">
                            <span>L3 (Mid)</span>
                            <span>L4 (Senior)</span>
                            <span>L5 (Staff)</span>
                            <span>L6 (Principal)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Results Card --}}
            <div class="bg-[#0f172a] rounded-[3rem] p-10 md:p-14 border border-slate-700 shadow-[0_30px_80px_-15px_rgba(114,132,181,0.3)] relative overflow-hidden group reveal-up" style="transition-delay: 200ms;">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#7284b5]/30 rounded-full blur-3xl pointer-events-none transition-all duration-700" :class="'scale-' + (level * 20)"></div>
                
                <h3 class="text-white font-black text-2xl mb-8 border-b border-slate-800 pb-6 relative z-10 tracking-tight">Projected Compensation</h3>
                
                <div class="space-y-8 relative z-10">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Base Salary (INR)</p>
                        <p class="text-5xl font-black text-white tracking-tighter font-mono flex items-center gap-1">
                            <span class="text-2xl text-slate-500">₹</span><span x-text="basePay"></span>
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-800">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-[#9fb2df] mb-2">Equity Grant</p>
                            <p class="text-3xl font-black text-[#bacdf3] tracking-tight font-mono flex items-end gap-2">
                                <span x-text="equity"></span><span class="text-xs text-slate-500 mb-1.5 uppercase">Shares</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-2">Annual Bonus</p>
                            <p class="text-3xl font-black text-emerald-400 tracking-tight font-mono flex items-end gap-1">
                                <span x-text="bonus"></span><span class="text-xl">%</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 4. PERKS & BENEFITS ================= --}}
    <section class="py-24 bg-[#fcf9f2] relative z-10 border-b border-[#bacdf3]/40">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">The Node Benefits</h2>
                <p class="text-slate-500 text-lg font-medium">We provide the hardware, health, and freedom required to do your life's best work.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($benefits as $idx => $perk)
                <div class="flex items-start gap-5 p-8 bg-white rounded-[2rem] border border-[#bacdf3]/40 hover:border-[#bacdf3] hover:shadow-[0_15px_40px_-10px_rgba(114,132,181,0.15)] hover:-translate-y-1 transition-all duration-300 reveal-up group" style="transition-delay: {{ $idx * 50 }}ms;">
                    <div class="w-12 h-12 rounded-xl bg-[#fcf9f2] text-[#7284b5] flex items-center justify-center shrink-0 border border-[#bacdf3] shadow-sm group-hover:bg-[#7284b5] group-hover:text-white transition-colors">
                        <i class="fa-solid {{ $perk['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black text-slate-900 mb-2">{{ $perk['title'] }}</h4>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ $perk['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= 5. THE HIRING PIPELINE ================= --}}
    <section class="py-32 bg-white relative z-10 border-b border-[#bacdf3]/40 overflow-hidden">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-6">The Deployment Pipeline</h2>
                <p class="text-slate-500 text-lg font-medium">A transparent, async-heavy interview process designed to respect your time.</p>
            </div>

            <div class="relative">
                {{-- Connecting Line (Desktop) --}}
                <div class="hidden lg:block absolute top-[45px] left-[10%] right-[10%] h-1 bg-[#fcf9f2] border-t border-b border-[#bacdf3]/30 z-0">
                    <div class="h-full bg-[#7284b5] w-1/3 animate-[marquee_4s_ease-in-out_infinite_alternate]"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 lg:gap-6 relative z-10">
                    @foreach([
                        ['step' => '01', 'title' => 'Application Review', 'desc' => 'We review your GitHub, past projects, and core architectural decisions.'],
                        ['step' => '02', 'title' => 'Async Tech Screen', 'desc' => 'A practical, 2-hour take-home challenge relevant to actual platform problems.'],
                        ['step' => '03', 'title' => 'Architecture Deep Dive', 'desc' => 'A 60-min technical discussion with our principal engineers. No whiteboard riddles.'],
                        ['step' => '04', 'title' => 'Offer & Node Init', 'desc' => 'We make a competitive offer with transparent equity, ready for deployment.']
                    ] as $idx => $step)
                    <div class="text-center group reveal-up" style="transition-delay: {{ $idx * 150 }}ms;">
                        <div class="w-24 h-24 mx-auto bg-white border-2 border-[#bacdf3] rounded-3xl flex items-center justify-center mb-6 shadow-md group-hover:-translate-y-2 group-hover:border-[#7284b5] transition-all duration-300 relative z-10">
                            <span class="text-2xl font-black text-[#7284b5] font-mono">{{ $step['step'] }}</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3 tracking-tight">{{ $step['title'] }}</h3>
                        <p class="text-sm text-slate-500 font-medium px-4">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ================= 6. LIVE JOB BOARD (ALPINE DRIVEN) ================= --}}
    <section class="py-32 bg-[#fcf9f2] relative z-10" id="positions">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 reveal-up">
                <div>
                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">Open Nodes</h2>
                    <p class="text-slate-500 text-xl font-medium">Find your role in the FinanceAI architecture.</p>
                </div>
                
                {{-- Search & Filter --}}
                <div class="flex flex-col sm:flex-row gap-4 shrink-0">
                    <div class="relative group/search">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[#9fb2df]"></i>
                        <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search roles..." 
                               class="w-full sm:w-64 pl-10 pr-4 py-3 bg-white border border-[#bacdf3] rounded-xl text-sm font-bold text-slate-900 placeholder-slate-400 outline-none transition-all focus:ring-4 focus:ring-[#bacdf3]/30 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Department Filter Tabs --}}
            <div class="flex overflow-x-auto w-full scrollbar-hide gap-3 mb-10 pb-2 reveal-up" style="transition-delay: 100ms;">
                <button @click="setDept('All')" @mouseenter="playHover()"
                        class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all focus:outline-none whitespace-nowrap flex-shrink-0"
                        :class="selectedDept === 'All' ? 'bg-[#7284b5] text-white border-[#7284b5] shadow-md' : 'bg-white text-slate-500 border-[#bacdf3] hover:bg-[#fcf9f2] hover:text-[#7284b5]'">
                    All Departments
                </button>
                @foreach($departments as $dept)
                    <button @click="setDept('{{ $dept }}')" @mouseenter="playHover()"
                            class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all focus:outline-none whitespace-nowrap flex-shrink-0"
                            :class="selectedDept === '{{ $dept }}' ? 'bg-[#7284b5] text-white border-[#7284b5] shadow-md' : 'bg-white text-slate-500 border-[#bacdf3] hover:bg-[#fcf9f2] hover:text-[#7284b5]'">
                        {{ $dept }}
                    </button>
                @endforeach
            </div>

            {{-- Job List --}}
            <div class="space-y-4">
                @foreach($jobs as $idx => $job)
                    <div class="job-card bg-white rounded-[2rem] border border-[#bacdf3]/50 shadow-sm p-6 sm:p-8 hover:shadow-[0_15px_40px_-10px_rgba(114,132,181,0.2)] hover:-translate-y-1 hover:border-[#bacdf3] transition-all duration-300 group reveal-up"
                         style="transition-delay: {{ $idx * 50 }}ms"
                         data-title="{{ strtolower($job['title']) }}"
                         data-dept="{{ $job['dept'] }}"
                         x-show="matchesFilter($el)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-3 flex-wrap">
                                    <h3 class="text-xl md:text-2xl font-black text-slate-900 group-hover:text-[#7284b5] transition-colors tracking-tight">{{ $job['title'] }}</h3>
                                    @if($job['hot'])
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-rose-200 animate-pulse shadow-sm">Urgent Priority</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 flex-wrap">
                                    <span class="text-xs font-bold text-slate-500 flex items-center gap-2 bg-[#fcf9f2] px-3 py-1.5 rounded-lg border border-[#bacdf3]/50">
                                        <i class="fa-solid fa-layer-group text-[#9fb2df]"></i> {{ $job['dept'] }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 flex items-center gap-2 bg-[#fcf9f2] px-3 py-1.5 rounded-lg border border-[#bacdf3]/50">
                                        <i class="fa-solid fa-location-dot text-[#9fb2df]"></i> {{ $job['loc'] }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500 flex items-center gap-2 bg-[#fcf9f2] px-3 py-1.5 rounded-lg border border-[#bacdf3]/50">
                                        <i class="fa-solid fa-clock text-[#9fb2df]"></i> {{ $job['type'] }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3 shrink-0 mt-4 md:mt-0">
                                <button @click.prevent="copyLink('{{ $job['id'] }}')" @mouseenter="playHover()" class="w-12 h-12 rounded-xl bg-white border border-[#bacdf3] text-[#7284b5] hover:bg-[#7284b5] hover:text-white transition-colors flex items-center justify-center shadow-sm focus:outline-none" title="Copy Job Link">
                                    <i class="fa-solid fa-link text-sm"></i>
                                </button>
                                <a href="{{ route('contact') }}" @mouseenter="playHover()" class="px-8 py-3.5 bg-[#7284b5] text-white rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-[#616dab] transition-all shadow-md focus:outline-none cursor-none flex items-center gap-2">
                                    Apply <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Empty State --}}
                <div x-show="filteredCount === 0" style="display: none;" class="py-16 text-center animate-fade-in-up">
                    <div class="w-20 h-20 bg-white border border-[#bacdf3] rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm text-[#9fb2df]">
                        <i class="fa-solid fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">No open nodes found.</h3>
                    <p class="text-sm font-medium text-slate-500 mb-6">Try adjusting your search or department filters.</p>
                    <button @click="searchQuery = ''; selectedDept = 'All'; playClick()" class="magnetic-target px-6 py-3 bg-white border border-[#bacdf3] text-[#7284b5] rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-[#fcf9f2] transition-colors focus:outline-none cursor-none">
                        Clear Filters
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- ================= 7. CTA ================= --}}
    <section class="py-24 px-4 sm:px-6 lg:px-8 relative z-10 border-t border-[#bacdf3]/40 bg-white">
        <div class="max-w-4xl mx-auto bg-[#0f172a] rounded-[3rem] border border-slate-700 p-10 md:p-20 text-center relative overflow-hidden shadow-[0_30px_80px_-15px_rgba(114,132,181,0.4)] reveal-up">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg h-64 bg-indigo-500/30 rounded-full blur-[120px] pointer-events-none"></div>
            
            <div class="w-20 h-20 bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner relative z-10 text-white">
                <i class="fa-solid fa-paper-plane text-3xl"></i>
            </div>
            <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-6 relative z-10">Don't see a perfect fit?</h2>
            <p class="text-[#bacdf3] font-medium text-lg relative z-10 mb-10 max-w-xl mx-auto leading-relaxed">
                Send us your resume anyway. We're always looking for exceptional cryptographers, designers, and engineers who challenge our architecture.
            </p>
            <a href="{{ route('contact') }}" @mouseenter="playHover()" class="magnetic-target inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 rounded-2xl font-black text-sm uppercase tracking-widest shadow-[0_10px_30px_rgba(255,255,255,0.1)] hover:bg-[#bacdf3] hover:text-[#0f172a] transition-all relative z-10 cursor-none focus:outline-none">
                Submit Open Application <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

</div>

@endsection

@push('styles')
<style>
    /* PURE CSS PATTERNS */
    .pattern-grid-lg { background-image: linear-gradient(#bacdf3 1px, transparent 1px), linear-gradient(90deg, #bacdf3 1px, transparent 1px); background-size: 40px 40px; }
    .pattern-dots { background-image: radial-gradient(#ffffff 2px, transparent 2px); background-size: 20px 20px; }
    .pattern-waves { background: repeating-radial-gradient(circle at 0 0, transparent 0, #fcf9f2 10px, transparent 10px, #bacdf3 20px); }
    .pattern-isometric { background-image: linear-gradient(30deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(150deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(30deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(150deg, #bacdf3 12%, transparent 12.5%, transparent 87%, #bacdf3 87.5%, #bacdf3), linear-gradient(60deg, #9fb2df77 25%, transparent 25.5%, transparent 75%, #9fb2df77 75%, #9fb2df77), linear-gradient(60deg, #9fb2df77 25%, transparent 25.5%, transparent 75%, #9fb2df77 75%, #9fb2df77); background-size: 40px 70px; background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0, 20px 35px; }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .reveal-up.is-visible { opacity: 1; transform: translateY(0); }
    
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
    .animate-float { animation: float 8s ease-in-out infinite; }

    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    .animate-marquee { animation: marquee 30s linear infinite; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    
    // Compensation Calculator Widget
    Alpine.data('compCalculator', () => ({
        level: 4,
        
        get basePay() {
            const salaries = { 3: '18,50,000', 4: '28,00,000', 5: '45,00,000', 6: '65,00,000' };
            return salaries[this.level];
        },
        get equity() {
            const shares = { 3: '1,500', 4: '4,000', 5: '10,000', 6: '25,000' };
            return shares[this.level];
        },
        get bonus() {
            const bonuses = { 3: '10', 4: '15', 5: '20', 6: '30' };
            return bonuses[this.level];
        },

        playKeySound() {
            if(window.audioEngine) {
                if(!this.lastTick || Date.now() - this.lastTick > 50) {
                    window.audioEngine.playClick(); 
                    this.lastTick = Date.now();
                }
            }
        },
        updateComp() {
            // Triggers reactivity. Sound is handled in input event.
        }
    }));

    // Job Board Engine
    Alpine.data('careersEngine', () => ({
        selectedDept: 'All',
        searchQuery: '',
        filteredCount: -1,

        init() {
            // Scroll Animation Observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('is-visible'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));

            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('selectedDept', () => this.updateFilteredCount());
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if(window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if(window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if(!window.audioEngine) return;
            if(!this.lastTick || Date.now() - this.lastTick > 50) {
                window.audioEngine.playClick(); 
                this.lastTick = Date.now();
            }
        },

        setDept(dept) {
            this.playClick();
            this.selectedDept = dept;
        },

        matchesFilter(el) {
            let matchDept = true;
            let matchSearch = true;

            const title = el.getAttribute('data-title') || '';
            const dept = el.getAttribute('data-dept') || '';

            if (this.selectedDept !== 'All') {
                matchDept = dept === this.selectedDept;
            }

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                matchSearch = title.includes(q) || dept.toLowerCase().includes(q);
            }

            return matchDept && matchSearch;
        },

        updateFilteredCount() {
            let count = 0;
            const items = document.querySelectorAll('.job-card');
            setTimeout(() => {
                items.forEach(item => { if (item.style.display !== 'none') count++; });
                this.filteredCount = count;
            }, 50);
        },

        scrollToJobs() {
            this.playClick();
            document.getElementById('positions').scrollIntoView({ behavior: 'smooth' });
        },

        copyLink(id) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + id;
            navigator.clipboard.writeText(url).then(() => {
                this.$dispatch('notify', { message: 'Job permalink copied to clipboard.', type: 'success' });
            });
        }
    }));
});
</script>
@endpush