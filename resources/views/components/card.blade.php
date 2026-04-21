{{-- ====================================================================== --}}
{{-- 🚀 FINANCE AI: ENTERPRISE MASTER KPI WIDGET & TELEMETRY NODE           --}}
{{-- ====================================================================== --}}

@props([
    'title'      => 'Metric Title',
    'subtitle'   => null,
    'icon'       => null,
    'value'      => 0,
    'trend'      => null,          // e.g., '+14.5%' or '-2.4%'
    'trendLabel' => null,          // e.g., 'vs last month'
    'padding'    => 'p-6 sm:p-8',
    'variant'    => 'default',     // default | soft | elevated | outline | glass
    'accent'     => 'indigo',      // indigo | emerald | rose | sky | amber | slate | fuchsia
    'hover'      => true,          // Enable 3D hover lift & glare
    'loading'    => false,         // Show skeleton state
    'animate'    => true,          // Animate the numbers
    'format'     => 'currency',    // number | currency | percent
    'chartType'  => null,          // null | 'line' | 'bar'
    'chartData'  => null,          // PHP Array, Collection, or JSON string
    'livePulse'  => false,         // Show a live recording dot
    'menu'       => true,          // Show the 3-dot context menu & Flip State
])

@php
/* ================= 1. SAFE DATA SERIALIZATION ================= */
// Prevents "Array to string conversion" Fatal Errors
$safeChartData = '[]';
if ($chartData) {
    if (is_string($chartData)) {
        $safeChartData = $chartData;
    } elseif (is_array($chartData)) {
        $safeChartData = json_encode($chartData);
    } elseif (method_exists($chartData, 'toArray')) {
        $safeChartData = json_encode($chartData->toArray());
    }
}

/* ================= 2. VARIANT STYLING (Strict Light Theme) ================= */
$variants = [
    'default'  => 'bg-white border border-slate-200 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)]',
    'soft'     => 'bg-slate-50 border border-slate-100 shadow-inner',
    'elevated' => 'bg-white border border-slate-100 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.08)]',
    'outline'  => 'bg-transparent border-2 border-slate-200',
    'glass'    => 'bg-white/80 backdrop-blur-2xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]',
];
$baseClass = $variants[$variant] ?? $variants['default'];

/* ================= 3. ACCENT COLOR MAPPING ================= */
$accents = [
    'indigo'  => ['iconBg' => 'bg-indigo-50',  'iconText' => 'text-indigo-600',  'hex' => '#4f46e5', 'glow' => 'rgba(79,70,229,0.15)'],
    'emerald' => ['iconBg' => 'bg-emerald-50', 'iconText' => 'text-emerald-600', 'hex' => '#10b981', 'glow' => 'rgba(16,185,129,0.15)'],
    'rose'    => ['iconBg' => 'bg-rose-50',    'iconText' => 'text-rose-600',    'hex' => '#f43f5e', 'glow' => 'rgba(244,63,94,0.15)'],
    'sky'     => ['iconBg' => 'bg-sky-50',     'iconText' => 'text-sky-600',     'hex' => '#0ea5e9', 'glow' => 'rgba(14,165,233,0.15)'],
    'amber'   => ['iconBg' => 'bg-amber-50',   'iconText' => 'text-amber-600',   'hex' => '#f59e0b', 'glow' => 'rgba(245,158,11,0.15)'],
    'slate'   => ['iconBg' => 'bg-slate-100',  'iconText' => 'text-slate-600',   'hex' => '#475569', 'glow' => 'rgba(71,85,105,0.15)'],
    'fuchsia' => ['iconBg' => 'bg-fuchsia-50', 'iconText' => 'text-fuchsia-600', 'hex' => '#d946ef', 'glow' => 'rgba(217,70,239,0.15)'],
];
$activeAccent = $accents[$accent] ?? $accents['indigo'];

/* ================= 4. TREND LOGIC ================= */
$trendColor = 'text-slate-400';
$trendBg = 'bg-slate-50 border-slate-200';
$trendIcon  = '';
$trendSign = '';

if ($trend !== null) {
    $numeric = floatval(str_replace(['%','+', ','], '', $trend));
    if ($numeric < 0) {
        $trendColor = 'text-rose-600';
        $trendBg = 'bg-rose-50 border-rose-100';
        $trendIcon  = 'fa-arrow-trend-down';
        $trendSign = '-';
    } elseif ($numeric > 0) {
        $trendColor = 'text-emerald-600';
        $trendBg = 'bg-emerald-50 border-emerald-100';
        $trendIcon  = 'fa-arrow-trend-up';
        $trendSign = '+';
    } else {
        $trendIcon  = 'fa-minus';
    }
}

/* ================= 5. PHP FALLBACK FORMATTING ================= */
$displayValue = $value;
if (is_numeric($value)) {
    if ($format === 'currency') {
        $displayValue = '₹' . number_format((float)$value);
    } elseif ($format === 'percent') {
        $displayValue = number_format((float)$value, 1) . '%';
    } else {
        $displayValue = number_format((float)$value);
    }
}
@endphp

{{-- ================= THE COMPONENT HTML ================= --}}
{{-- We wrap everything in a perspective container for true 3D --}}
<div class="relative w-full perspective-[1500px] group/widget" style="z-index: 1;">
    
    <div x-data="kpiWidgetEngine({ 
            targetValue: {{ is_numeric($value) ? $value : 0 }}, 
            format: '{{ $format }}',
            shouldAnimate: {{ $animate ? 'true' : 'false' }},
            chartType: '{{ $chartType }}',
            chartData: {{ $safeChartData }},
            themeColor: '{{ $activeAccent['hex'] }}',
            themeGlow: '{{ $activeAccent['glow'] }}'
         })" 
         @mousemove="handleTilt($event)" 
         @mouseleave="resetTilt()"
         x-ref="cardBody"
         class="relative w-full h-full transition-transform duration-300 ease-out transform-style-3d min-h-[220px]"
         :class="isFlipped ? 'rotate-y-180' : ''">

        {{-- ----------------------------------------------------------------- --}}
        {{-- FRONT FACE OF THE CARD                                            --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="absolute inset-0 w-full h-full backface-hidden flex flex-col rounded-[2.5rem] {{ $baseClass }} {{ $hover ? 'group-hover/widget:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.15)]' : '' }} transition-shadow duration-500">
            
            {{-- DECOUPLED BACKGROUND LAYER (Solves the clipping bug) --}}
            <div class="absolute inset-0 rounded-[2.5rem] overflow-hidden pointer-events-none z-0">
                {{-- Subtle SVG Noise Mesh --}}
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwVjB6bTEwIDEwaDEwdjEwSDEwaC0xMHptMCAwaC0xMHYtMTBoMTB2MTB6IiBmaWxsPSIjZThlYWVkIiBmaWxsLW9wYWNpdHk9IjAuMjUiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==')] opacity-50 mix-blend-multiply"></div>
                
                {{-- Dynamic Accent Glow Orb --}}
                <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full blur-[60px] opacity-60 transition-transform duration-700 group-hover/widget:scale-150" style="background-color: {{ $activeAccent['glow'] }};"></div>

                {{-- Interactive Glare Effect --}}
                @if($hover)
                    <div class="absolute w-[600px] h-[600px] bg-white opacity-0 mix-blend-overlay rounded-full blur-[60px] transition-opacity duration-300" x-ref="glare"></div>
                @endif

                {{-- 🔥 SPARKLINE / CHART AREA (Tucked neatly at the bottom of the overflow) --}}
                @if($chartType && $safeChartData !== '[]')
                    <div class="absolute bottom-0 left-0 right-0 h-28 opacity-80 group-hover/widget:opacity-100 transition-opacity duration-500 {{ $padding }} pt-0 pb-0 translate-z-10">
                        <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
                    </div>
                @endif
            </div>

            {{-- 🔄 SKELETON LOADING STATE --}}
            @if($loading)
                <div class="absolute inset-0 z-50 bg-white/95 backdrop-blur-md p-8 flex flex-col justify-between rounded-[2.5rem]">
                    <div class="flex justify-between items-start">
                        <div class="space-y-3">
                            <div class="w-24 h-3 bg-slate-200 rounded animate-pulse"></div>
                            <div class="w-12 h-10 bg-slate-100 rounded-xl animate-pulse"></div>
                        </div>
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl animate-pulse"></div>
                    </div>
                    <div class="mt-auto space-y-4">
                        <div class="w-48 h-12 bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="w-full h-16 bg-slate-50 rounded-xl animate-pulse mt-4"></div>
                    </div>
                </div>
            @endif

            {{-- INTERACTIVE FOREGROUND LAYER (Allows dropdowns to escape bounds) --}}
            <div class="relative z-10 flex-1 flex flex-col {{ $padding }} h-full translate-z-20">
                
                {{-- WIDGET HEADER --}}
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        {{-- Icon Block --}}
                        @if($icon)
                            <div class="w-12 h-12 rounded-[1rem] {{ $activeAccent['iconBg'] }} {{ $activeAccent['iconText'] }} flex items-center justify-center border border-white shadow-sm shrink-0 group-hover/widget:scale-110 group-hover/widget:rotate-3 transition-transform duration-500 translate-z-30">
                                <i class="fa-solid {{ $icon }} text-lg"></i>
                            </div>
                        @endif
                        
                        {{-- Titles & Telemetry --}}
                        <div>
                            <div class="flex items-center gap-2.5 mb-1">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $title }}</h4>
                                @if($livePulse)
                                    <div class="flex items-center gap-1.5 px-1.5 py-0.5 rounded bg-rose-50 border border-rose-100" title="Live WebSocket Telemetry">
                                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-rose-500"></span></span>
                                        <span class="text-[8px] font-black text-rose-600 uppercase tracking-widest">Live</span>
                                    </div>
                                @endif
                            </div>
                            @if($subtitle)
                                <p class="text-[9px] font-bold text-slate-400">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Context Menu (3 dots) --}}
                    @if($menu)
                        <div class="relative translate-z-30" x-data="{ menuOpen: false }" @click.away="menuOpen = false">
                            <button @click="menuOpen = !menuOpen" @mouseenter="if(typeof playHoverSound === 'function') playHoverSound()" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors focus:outline-none border border-transparent hover:border-slate-200 shadow-sm bg-white/50 backdrop-blur-sm">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            
                            {{-- Dropdown (Now safely escapes bounds) --}}
                            <div x-show="menuOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                 class="absolute top-full right-0 mt-2 w-48 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-[1.5rem] shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] z-[100] p-1.5">
                                
                                <button @click="menuOpen = false; if(typeof playClickSound === 'function') playClickSound(); $dispatch('notify', {message: 'Syncing telemetry...', type: 'info'})" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center gap-3">
                                    <div class="w-6 h-6 rounded bg-indigo-100/50 flex items-center justify-center"><i class="fa-solid fa-rotate text-[10px]"></i></div> Sync Node
                                </button>
                                
                                <button @click="menuOpen = false; if(typeof playClickSound === 'function') playClickSound(); isFlipped = true;" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors flex items-center gap-3">
                                    <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center"><i class="fa-solid fa-microscope text-[10px]"></i></div> Inspect Raw Data
                                </button>

                                <div class="border-t border-slate-100 my-1 mx-2"></div>
                                
                                <button @click="menuOpen = false; if(typeof playClickSound === 'function') playClickSound(); $dispatch('notify', {message: 'Exporting CSV...', type: 'success'})" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition-colors flex items-center gap-3">
                                    <div class="w-6 h-6 rounded bg-emerald-100/50 flex items-center justify-center"><i class="fa-solid fa-file-csv text-[10px]"></i></div> Export CSV
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- MAIN VALUE & TREND --}}
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mt-auto mb-[60px] translate-z-20">
                    
                    {{-- Big Number (With Robinhood Scrubbing) --}}
                    <div class="flex items-start">
                        @if($format === 'currency') <span class="text-3xl font-bold text-slate-300 mr-1 mt-1 transition-colors duration-300" :class="isScrubbing ? '{{ $activeAccent['iconText'] }}' : ''">₹</span> @endif
                        
                        <h3 class="text-5xl md:text-6xl font-black text-slate-900 tracking-tighter tabular-nums drop-shadow-sm transition-colors duration-300" 
                            :class="isScrubbing ? '{{ $activeAccent['iconText'] }}' : ''" 
                            x-text="displayValue">
                            {{ str_replace('₹', '', $displayValue) }}
                        </h3>
                        
                        @if($format === 'percent') <span class="text-3xl font-bold text-slate-300 ml-1 mt-1 transition-colors duration-300" :class="isScrubbing ? '{{ $activeAccent['iconText'] }}' : ''">%</span> @endif
                    </div>

                    {{-- Trend Pill --}}
                    @if($trend)
                        <div class="flex flex-col items-start sm:items-end transition-opacity duration-300" :class="isScrubbing ? 'opacity-0' : 'opacity-100'">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border shadow-sm text-xs font-black tracking-widest {{ $trendBg }} {{ $trendColor }}">
                                <i class="fa-solid {{ $trendIcon }} text-[10px]"></i> {{ $trendSign }}{{ abs($numeric) }}%
                            </div>
                            @if($trendLabel)
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">{{ $trendLabel }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ----------------------------------------------------------------- --}}
        {{-- BACK FACE OF THE CARD (Raw Data Inspector)                        --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-2xl overflow-hidden flex flex-col pointer-events-none" :class="isFlipped ? 'pointer-events-auto' : ''">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
            
            <div class="flex justify-between items-center p-6 border-b border-slate-800 relative z-10 bg-slate-800/50">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-microscope text-indigo-400"></i> Raw Node Data
                </span>
                <button @click="isFlipped = false; if(typeof playClickSound === 'function') playClickSound();" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800 text-slate-400 hover:text-white border border-slate-700 transition-colors focus:outline-none">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>

            <div class="flex-1 p-6 relative z-10 flex flex-col justify-center space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-slate-500">Node Identifier</span>
                    <span class="text-xs font-mono text-slate-300">WID-{{ strtoupper(substr(md5($title), 0, 8)) }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-slate-500">Current Value</span>
                    <span class="text-xs font-mono text-emerald-400">{{ $displayValue }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-slate-500">Data Points</span>
                    <span class="text-xs font-mono text-indigo-400" x-text="chartData ? chartData.length : 0"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">Variance</span>
                    <span class="text-xs font-mono text-rose-400">±2.4%</span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-950 text-center">
                <p class="text-[8px] font-mono text-slate-600 uppercase tracking-widest">End of Record</p>
            </div>
        </div>

    </div>
</div>

{{-- ====================================================================== --}}
{{-- 🛠️ ALPINE & CHART.JS MASTER ENGINE (Injected ONCE per page)            --}}
{{-- ====================================================================== --}}

@once
@push('styles')
<style>
    .perspective-\[1500px\] { perspective: 1500px; }
    .transform-style-3d { transform-style: preserve-3d; }
    .backface-hidden { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .rotate-y-180 { transform: rotateY(180deg); }
    .translate-z-10 { transform: translateZ(10px); }
    .translate-z-20 { transform: translateZ(20px); }
    .translate-z-30 { transform: translateZ(30px); }
</style>
@endpush

@push('scripts')
{{-- Ensure Chart.js is loaded if not already present in the layout --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        
        Alpine.data('kpiWidgetEngine', (config) => ({
            
            // Core Metric State
            targetValue: config.targetValue,
            currentValue: 0,
            displayValue: '0',
            format: config.format,
            themeColor: config.themeColor,
            themeGlow: config.themeGlow,
            
            // Interaction States
            isFlipped: false,
            isScrubbing: false,
            
            // Chart State
            chartInstance: null,
            chartType: config.chartType,
            chartData: config.chartData,

            init() {
                // 1. Setup Static Value if Animation Disabled
                if (!config.shouldAnimate) {
                    this.currentValue = this.targetValue;
                    this.formatValue();
                }

                // 2. Setup Intersection Observer for Number Roll
                if (config.shouldAnimate) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                this.animateNumber();
                                observer.disconnect(); 
                            }
                        });
                    }, { threshold: 0.1 });
                    
                    observer.observe(this.$el);
                }

                // 3. Initialize Advanced Chart
                if (this.chartType && this.chartType !== 'none' && Array.isArray(this.chartData) && this.chartData.length > 0) {
                    setTimeout(() => { this.initChart(); }, 150);
                }
            },

            // ----------------------------------------------------
            // FORMATTING ENGINE
            // ----------------------------------------------------
            formatValue() {
                if (this.format === 'currency') {
                    this.displayValue = Math.floor(this.currentValue).toLocaleString('en-IN');
                } else if (this.format === 'percent') {
                    this.displayValue = this.currentValue.toFixed(1);
                } else {
                    this.displayValue = Math.floor(this.currentValue).toLocaleString('en-US');
                }
            },

            // ----------------------------------------------------
            // ANIMATION ENGINE (requestAnimationFrame)
            // ----------------------------------------------------
            animateNumber() {
                const duration = 2500; 
                let startTimestamp = null;
                
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    
                    // Ease Out Expo Formula
                    const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    this.currentValue = ease * this.targetValue;
                    
                    this.formatValue();
                    
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        this.currentValue = this.targetValue;
                        this.formatValue();
                    }
                };
                window.requestAnimationFrame(step);
            },

            // ----------------------------------------------------
            // 3D MOUSE INTERACTION ENGINE (Throttled)
            // ----------------------------------------------------
            handleTilt(e) {
                // Ignore tilt if flipped
                if(this.isFlipped) return;

                const card = this.$refs.cardBody;
                const rect = this.$el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                // Max tilt 5 degrees for premium feel
                const rotateX = ((y - centerY) / centerY) * -5;
                const rotateY = ((x - centerX) / centerX) * 5;
                card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

                // Move Glare (Hardware Accelerated)
                if(this.$refs.glare) {
                    this.$refs.glare.style.opacity = '0.3';
                    this.$refs.glare.style.transform = `translate3d(${x - 300}px, ${y - 300}px, 0)`;
                }
            },
            
            resetTilt() {
                if(this.isFlipped) return;

                const card = this.$refs.cardBody;
                card.style.transform = `rotateX(0deg) rotateY(0deg)`;
                
                if(this.$refs.glare) {
                    this.$refs.glare.style.opacity = '0';
                }
            },

            // ----------------------------------------------------
            // CHART.JS FACTORY (ROBINHOOD CROSSHAIR)
            // ----------------------------------------------------
            initChart() {
                if(!this.$refs.chartCanvas || typeof Chart === 'undefined') return;

                const ctx = this.$refs.chartCanvas.getContext('2d');
                const self = this; // Capture Alpine context
                
                let gradient = null;
                if(this.chartType === 'line') {
                    gradient = ctx.createLinearGradient(0, 0, 0, 112); // H-28 = 112px
                    const r = parseInt(this.themeColor.slice(1, 3), 16);
                    const g = parseInt(this.themeColor.slice(3, 5), 16);
                    const b = parseInt(this.themeColor.slice(5, 7), 16);
                    gradient.addColorStop(0, `rgba(${r}, ${g}, ${b}, 0.25)`);
                    gradient.addColorStop(1, `rgba(${r}, ${g}, ${b}, 0)`);
                }

                const labels = this.chartData.map((_, i) => `T-${this.chartData.length - i}`);

                // Custom Plugin: Robinhood Crosshair & External Value Update
                const crosshairPlugin = {
                    id: 'crosshairPlugin',
                    afterDraw: (chart) => {
                        if (chart.tooltip?._active?.length) {
                            let x = chart.tooltip._active[0].element.x;
                            let yAxis = chart.scales.y;
                            let ctx = chart.ctx;
                            ctx.save();
                            ctx.beginPath();
                            ctx.moveTo(x, yAxis.top);
                            ctx.lineTo(x, yAxis.bottom);
                            ctx.lineWidth = 1;
                            ctx.strokeStyle = self.themeColor;
                            ctx.setLineDash([3, 3]);
                            ctx.stroke();
                            ctx.restore();
                        }
                    }
                };

                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: { 
                            enabled: false, // We use external HTML update
                            external: function(context) {
                                // Haptic Tick
                                if(context.tooltip.opacity !== 0 && typeof playClickSound === 'function') {
                                    // Throttle the sound to prevent ear-rape when scrubbing fast
                                    if(!self.lastTick || Date.now() - self.lastTick > 100) {
                                        playClickSound();
                                        self.lastTick = Date.now();
                                    }
                                }
                            }
                        } 
                    },
                    scales: {
                        x: { display: false },
                        y: { 
                            display: false, 
                            min: Math.min(...this.chartData) * 0.90, // Add bottom padding so it doesn't touch the edge
                            max: Math.max(...this.chartData) * 1.10  
                        }
                    },
                    interaction: { intersect: false, mode: 'index' },
                    onHover: (e, activeElements) => {
                        if (activeElements.length > 0) {
                            self.isScrubbing = true;
                            const idx = activeElements[0].index;
                            self.currentValue = self.chartData[idx];
                            self.formatValue();
                        } else {
                            self.isScrubbing = false;
                            self.currentValue = self.targetValue;
                            self.formatValue();
                        }
                    }
                };

                const lineConfig = {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: this.chartData,
                            borderColor: this.themeColor,
                            borderWidth: 2.5,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4, 
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: this.themeColor,
                            pointBorderWidth: 2
                        }]
                    },
                    options: commonOptions,
                    plugins: [crosshairPlugin]
                };

                const barConfig = {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: this.chartData,
                            backgroundColor: this.themeColor,
                            borderRadius: 4,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            hoverBackgroundColor: '#0f172a'
                        }]
                    },
                    options: commonOptions
                };

                if(this.chartInstance) this.chartInstance.destroy();

                this.chartInstance = new Chart(
                    ctx, 
                    this.chartType === 'bar' ? barConfig : lineConfig
                );
            }
        }));
        
    });
</script>
@endpush
@endonce