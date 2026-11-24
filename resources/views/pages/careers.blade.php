@extends('layouts.landing')
@include('partials.site-type')

@section('title', 'Careers & Culture | FinanceAI Enterprise')
@section('meta_description', 'Join the FinanceAI architecture team. Build the future of financial intelligence with world-class engineers.')

@section('content')
@php
    $benefits = [
        ['icon' => 'fa-globe', 'title' => 'Remote-First Protocol', 'desc' => 'Work asynchronously from anywhere on Earth. We optimize for deep work, not desk time.'],
        ['icon' => 'fa-heart-pulse', 'title' => 'Comprehensive Coverage', 'desc' => 'Top-tier medical, dental, and vision insurance with 100% premium coverage for dependents.'],
        ['icon' => 'fa-chart-pie', 'title' => 'Meaningful Equity', 'desc' => 'We want owners. Every role includes a generous, transparent stock option grant.'],
        ['icon' => 'fa-laptop-code', 'title' => 'Command Center Setup', 'desc' => '₹250,000 upfront stipend to architect your perfect home office and development rig.'],
        ['icon' => 'fa-graduation-cap', 'title' => 'Continuous Learning', 'desc' => '₹50,000 annual budget for AWS certifications, engineering conferences, and literature.'],
        ['icon' => 'fa-plane', 'title' => 'Mandatory Downtime', 'desc' => 'Unlimited PTO, with a strictly enforced minimum of 20 days per year to prevent burnout.'],
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
<div class="paper" x-data="careersEngine()">

    <x-site.hero eyebrow="We're Hiring — {{ count($jobs) }} Open Roles" lead="Engineer the future of" accent="financial infrastructure."
        lede="We are a globally distributed collective of cryptographers, heuristic researchers, and software architects building the world's most secure financial intelligence platform.">
        <x-slot name="actions">
            <button type="button" @click="scrollToJobs()" @mouseenter="playHover()" class="group inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl text-white font-bold bg-gradient-to-br from-indigo-500 to-violet-600 shadow-[0_10px_30px_-8px_rgba(99,102,241,.7)] hover:-translate-y-0.5 transition-transform focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-400/40">
                View Open Nodes <i class="fa-solid fa-arrow-down text-sm" aria-hidden="true"></i>
            </button>
        </x-slot>
        <x-slot name="meta">
            <dl class="grid grid-cols-3 max-w-xl">
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Active Nodes</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">10,000+</dd></div>
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Capital Processed</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">₹1.2B</dd></div>
                <div><dt class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Core Uptime</dt><dd class="mt-1 text-xl sm:text-2xl font-black text-white">99.99%</dd></div>
            </dl>
        </x-slot>
    </x-site.hero>

    {{-- Principles --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="01 — Culture" title="Our Operating" accent="Principles"
                lede="The architectural tenets that define how we build, ship, and scale." />

            <div class="mt-14 grid md:grid-cols-3 gap-5">
                <article class="md:col-span-2 card-site p-8 sm:p-10 min-h-[240px] flex flex-col justify-between" data-reveal>
                    <i class="fa-solid fa-rocket text-slate-400" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-3xl font-black tracking-tight text-slate-900">Ship Fast, Ship Safe.</h3>
                        <p class="mt-3 text-lg text-slate-600 leading-relaxed max-w-xl">We deploy multiple times per day utilizing zero-downtime CI/CD pipelines and automated algorithmic rollback safety nets.</p>
                    </div>
                </article>
                <article class="bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-10 min-h-[240px] flex flex-col justify-between" data-reveal style="--d: 80ms">
                    <i class="fa-solid fa-brain text-slate-500" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">Intellectual Rigor</h3>
                        <p class="mt-3 text-slate-400 leading-relaxed">We challenge assumptions with data. Every architectural decision is backed by telemetry.</p>
                    </div>
                </article>
                <article class="card-site p-8 sm:p-10 min-h-[240px] flex flex-col justify-between" data-reveal>
                    <i class="fa-solid fa-users text-slate-400" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-2xl font-black tracking-tight text-slate-900">Radical Ownership</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">Engineers own features end-to-end — from database migration to production monitoring.</p>
                    </div>
                </article>
                <article class="md:col-span-2 card-site p-8 sm:p-10 min-h-[240px] grid sm:grid-cols-5 gap-8 items-end" data-reveal style="--d: 80ms">
                    <div class="sm:col-span-3">
                        <i class="fa-solid fa-globe text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-10 text-3xl font-black tracking-tight text-slate-900">Remote-First DNA.</h3>
                        <p class="mt-3 text-lg text-slate-600 leading-relaxed">We optimize for asynchronous communication and deep work sessions. Your timezone doesn't matter; your code does.</p>
                    </div>
                    <div class="sm:col-span-2 border-t border-slate-200 font-mono text-xs text-slate-500" aria-hidden="true">
                        @foreach([['UTC−5', 'US'], ['UTC+1', 'EMEA'], ['UTC+5:30', 'IN']] as [$tz, $r])
                            <div class="flex justify-between py-2.5 border-b border-slate-200"><span>{{ $r }}</span><span>{{ $tz }}</span></div>
                        @endforeach
                        <p class="mt-3 uppercase tracking-wider text-emerald-700"><span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Global Sync Active</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- Compensation --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200" x-data="compCalculator()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-14 items-center">
            <div data-reveal>
                <p class="eyebrow text-indigo-600">02 — Total Rewards</p>
                <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">We pay for <span class="font-serif-i text-indigo-600">top-tier</span> architecture.</h2>
                <p class="mt-5 text-lg text-slate-600 leading-relaxed">We believe in radical transparency. Calculate your projected total compensation package based on your engineering level. Everyone gets equity.</p>

                <div class="mt-10">
                    <div class="flex items-center justify-between">
                        <label for="eng-level" class="eyebrow text-slate-500">Engineering Level</label>
                        <span class="font-mono text-sm font-semibold text-slate-900" x-text="'Level ' + level"></span>
                    </div>
                    <input id="eng-level" type="range" x-model="level" min="3" max="6" step="1" @input="playKeySound()" class="comp-range mt-4 w-full" :style="`--p: ${(level - 3) / 3 * 100}%`">
                    <div class="mt-3 flex justify-between font-mono text-[10px] uppercase tracking-wider text-slate-400">
                        <span>L3 (Mid)</span><span>L4 (Senior)</span><span>L5 (Staff)</span><span>L6 (Principal)</span>
                    </div>
                </div>
            </div>

            <div class="bg-[#0b0b1a] text-white grain rounded-2xl p-8 sm:p-12" data-reveal style="--d: 120ms">
                <p class="font-mono text-[11px] uppercase tracking-wider text-slate-400">Projected Compensation</p>
                <dl class="mt-8">
                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Base Salary (INR)</dt>
                    <dd class="mt-2 text-5xl sm:text-6xl font-black tracking-tight tabular-nums"><span class="text-2xl text-slate-500 mr-1">₹</span><span x-text="basePay"></span></dd>
                    <div class="mt-8 pt-6 border-t border-white/10 grid grid-cols-2 gap-6">
                        <div>
                            <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Equity Grant</dt>
                            <dd class="mt-2 text-3xl font-black tabular-nums text-indigo-300"><span x-text="equity"></span> <span class="text-xs font-mono uppercase text-slate-500">Shares</span></dd>
                        </div>
                        <div>
                            <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">Annual Bonus</dt>
                            <dd class="mt-2 text-3xl font-black tabular-nums text-emerald-400"><span x-text="bonus"></span>%</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="03 — Benefits" title="The Node" accent="Benefits"
                lede="We provide the hardware, health, and freedom required to do your life's best work." />

            <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 border-t border-l border-slate-200">
                @foreach($benefits as $idx => $perk)
                    <article class="p-7 border-b border-r border-slate-200" data-reveal style="--d: {{ $idx * 50 }}ms">
                        <i class="fa-solid {{ $perk['icon'] }} text-slate-400" aria-hidden="true"></i>
                        <h3 class="mt-8 text-lg font-black tracking-tight text-slate-900">{{ $perk['title'] }}</h3>
                        <p class="mt-2 text-[15px] text-slate-600 leading-relaxed">{{ $perk['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Hiring process --}}
    <section class="py-20 sm:py-28 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.section-head label="04 — Process" title="The Deployment" accent="Pipeline"
                lede="A transparent, async-heavy interview process designed to respect your time." />

            <ol class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-x-8 border-t border-slate-900">
                @foreach([
                    ['step' => '01', 'title' => 'Application Review', 'desc' => 'We review your GitHub, past projects, and core architectural decisions.'],
                    ['step' => '02', 'title' => 'Async Tech Screen', 'desc' => 'A practical, 2-hour take-home challenge relevant to actual platform problems.'],
                    ['step' => '03', 'title' => 'Architecture Deep Dive', 'desc' => 'A 60-min technical discussion with our principal engineers. No whiteboard riddles.'],
                    ['step' => '04', 'title' => 'Offer & Node Init', 'desc' => 'We make a competitive offer with transparent equity, ready for deployment.'],
                ] as $idx => $step)
                    <li class="pt-8 pb-6" data-reveal style="--d: {{ $idx * 100 }}ms">
                        <span class="font-serif-i text-5xl text-indigo-600">{{ $step['step'] }}</span>
                        <h3 class="mt-4 text-lg font-black tracking-tight text-slate-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-[15px] text-slate-600 leading-relaxed">{{ $step['desc'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Jobs --}}
    <section class="py-20 sm:py-28 scroll-mt-20" id="positions">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8" data-reveal>
                <div>
                    <p class="eyebrow text-indigo-600">05 — Open roles</p>
                    <h2 class="mt-4 text-3xl sm:text-5xl font-black tracking-[-0.03em] leading-[1.05] text-slate-900">Open <span class="font-serif-i text-indigo-600">Nodes</span></h2>
                    <p class="mt-4 text-lg text-slate-600">Find your role in the FinanceAI architecture.</p>
                </div>
                <label class="relative md:w-72">
                    <span class="sr-only">Search roles</span>
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                    <input type="text" x-model="searchQuery" @input="playKeySound()" placeholder="Search roles..."
                           class="w-full h-11 pl-9 pr-3 rounded-lg border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                </label>
            </div>

            <div class="mt-10 flex gap-1 overflow-x-auto scrollbar-hide" role="tablist" aria-label="Departments">
                <button type="button" role="tab" @click="setDept('All')" @mouseenter="playHover()" :aria-selected="selectedDept === 'All'"
                        class="h-10 px-3.5 rounded-lg font-mono text-[11px] uppercase tracking-wider whitespace-nowrap transition-colors focus:outline-none"
                        :class="selectedDept === 'All' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">All Departments</button>
                @foreach($departments as $dept)
                    <button type="button" role="tab" @click="setDept('{{ $dept }}')" @mouseenter="playHover()" :aria-selected="selectedDept === '{{ $dept }}'"
                            class="h-10 px-3.5 rounded-lg font-mono text-[11px] uppercase tracking-wider whitespace-nowrap transition-colors focus:outline-none"
                            :class="selectedDept === '{{ $dept }}' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">{{ $dept }}</button>
                @endforeach
            </div>

            <ul class="mt-6 border-t border-slate-900">
                @foreach($jobs as $job)
                    <li id="{{ $job['id'] }}" class="job-card group grid md:grid-cols-12 gap-4 items-center py-6 border-b border-slate-200 scroll-mt-28"
                        data-title="{{ strtolower($job['title']) }}" data-dept="{{ $job['dept'] }}"
                        x-show="matchesFilter($el)" x-transition.opacity.duration.250ms>
                        <div class="md:col-span-6">
                            <h3 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $job['title'] }}</h3>
                            @if($job['hot'])
                                <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-rose-600">Urgent Priority</p>
                            @endif
                        </div>
                        <dl class="md:col-span-4 flex flex-wrap gap-x-5 gap-y-1 font-mono text-xs uppercase tracking-wider text-slate-500">
                            <div><dt class="sr-only">Department</dt><dd>{{ $job['dept'] }}</dd></div>
                            <div><dt class="sr-only">Location</dt><dd>{{ $job['loc'] }}</dd></div>
                            <div><dt class="sr-only">Type</dt><dd>{{ $job['type'] }}</dd></div>
                        </dl>
                        <div class="md:col-span-2 flex md:justify-end items-center gap-2">
                            <button type="button" @click.prevent="copyLink('{{ $job['id'] }}')" @mouseenter="playHover()" class="h-10 w-10 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-900 hover:border-slate-900 transition-colors focus:outline-none" title="Copy Job Link" aria-label="Copy link to {{ $job['title'] }}">
                                <i class="fa-solid fa-link text-xs" aria-hidden="true"></i>
                            </button>
                            <a href="{{ route('contact') }}" @mouseenter="playHover()" class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-indigo-600 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-200">
                                Apply <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div x-show="filteredCount === 0" x-cloak class="py-16 text-center">
                <p class="text-2xl font-black tracking-tight text-slate-900">No open nodes found.</p>
                <p class="mt-2 text-slate-500">Try adjusting your search or department filters.</p>
                <button type="button" @click="searchQuery = ''; selectedDept = 'All'; playClick()" class="mt-6 h-10 px-4 rounded-lg border border-slate-300 text-sm font-semibold text-slate-800 hover:border-slate-900 transition-colors focus:outline-none">Clear Filters</button>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 sm:py-28 bg-[#0b0b1a] text-white grain">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-8" data-reveal>
                <h2 class="text-4xl sm:text-6xl font-black tracking-[-0.035em] leading-[1.02]">Don't see a <span class="font-serif-i text-indigo-300">perfect fit?</span></h2>
                <p class="mt-6 text-lg text-slate-300 max-w-2xl">Send us your resume anyway. We're always looking for exceptional cryptographers, designers, and engineers who challenge our architecture.</p>
            </div>
            <div class="lg:col-span-4 lg:text-right" data-reveal style="--d: 120ms">
                <a href="{{ route('contact') }}" @mouseenter="playHover()" class="group inline-flex items-center gap-2 h-12 px-6 rounded-xl bg-white text-slate-900 font-bold hover:bg-indigo-100 transition-colors focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    Submit Open Application <i class="fa-solid fa-arrow-right text-sm transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .comp-range { -webkit-appearance: none; appearance: none; height: 4px; border-radius: 9999px; outline: none;
        background: linear-gradient(to right, #4f46e5 var(--p, 33%), #e2e8f0 var(--p, 33%)); }
    .comp-range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 9999px; background: #fff; border: 2px solid #4f46e5; box-shadow: 0 2px 8px rgba(79,70,229,.35); cursor: pointer; }
    .comp-range::-moz-range-thumb { width: 20px; height: 20px; border-radius: 9999px; background: #fff; border: 2px solid #4f46e5; cursor: pointer; }
    .comp-range:focus-visible::-webkit-slider-thumb { box-shadow: 0 0 0 6px rgba(79,70,229,.25); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('compCalculator', () => ({
        level: 4,
        get basePay() { return { 3: '18,50,000', 4: '28,00,000', 5: '45,00,000', 6: '65,00,000' }[this.level]; },
        get equity() { return { 3: '1,500', 4: '4,000', 5: '10,000', 6: '25,000' }[this.level]; },
        get bonus() { return { 3: '10', 4: '15', 5: '20', 6: '30' }[this.level]; },
        playKeySound() {
            if (window.audioEngine && (!this.lastTick || Date.now() - this.lastTick > 50)) { window.audioEngine.playClick(); this.lastTick = Date.now(); }
        },
    }));

    Alpine.data('careersEngine', () => ({
        selectedDept: 'All',
        searchQuery: '',
        filteredCount: -1,

        init() {
            this.$watch('searchQuery', () => this.updateFilteredCount());
            this.$watch('selectedDept', () => this.updateFilteredCount());
            this.$nextTick(() => this.updateFilteredCount());
        },

        playClick() { if (window.audioEngine) window.audioEngine.playClick(); },
        playHover() { if (window.audioEngine) window.audioEngine.playHover(); },
        playKeySound() {
            if (window.audioEngine && (!this.lastTick || Date.now() - this.lastTick > 50)) { window.audioEngine.playClick(); this.lastTick = Date.now(); }
        },

        setDept(dept) { this.playClick(); this.selectedDept = dept; },

        matchesFilter(el) {
            const title = el.getAttribute('data-title') || '';
            const dept = el.getAttribute('data-dept') || '';
            const matchDept = this.selectedDept === 'All' || dept === this.selectedDept;
            const q = this.searchQuery.trim().toLowerCase();
            const matchSearch = !q || title.includes(q) || dept.toLowerCase().includes(q);
            return matchDept && matchSearch;
        },

        updateFilteredCount() {
            setTimeout(() => {
                let count = 0;
                document.querySelectorAll('.job-card').forEach(item => { if (item.style.display !== 'none') count++; });
                this.filteredCount = count;
            }, 300);
        },

        scrollToJobs() {
            this.playClick();
            document.getElementById('positions').scrollIntoView({ behavior: 'smooth' });
        },

        copyLink(id) {
            this.playClick();
            const url = window.location.origin + window.location.pathname + '#' + id;
            navigator.clipboard.writeText(url).then(() => this.$dispatch('notify', { message: 'Job permalink copied to clipboard.', type: 'success' }));
        }
    }));
});
</script>
@endpush
