@extends('layouts.app')

@section('title', 'API Reference | FinanceAI')

@section('content')

@php
    // ================= ENTERPRISE API PAYLOAD =================
    // Structured data makes the Blade file incredibly clean and maintainable.
    $navigation = [
        ['id' => 'introduction', 'label' => 'Introduction', 'is_header' => false],
        ['id' => 'authentication', 'label' => 'Authentication', 'is_header' => false],
        ['id' => 'errors', 'label' => 'Errors & Limits', 'is_header' => false],
        ['id' => 'core-resources', 'label' => 'Core Resources', 'is_header' => true],
        ['id' => 'ledgers', 'label' => 'Ledgers (Transactions)', 'is_header' => false],
        ['id' => 'forecasts', 'label' => 'AI Forecasts', 'is_header' => false],
        ['id' => 'nodes', 'label' => 'Identity Nodes', 'is_header' => false],
    ];

    $endpoints = [
        [
            'id' => 'introduction',
            'badge' => 'Getting Started',
            'title' => 'Introduction',
            'description' => 'The FinanceAI API is organized around REST. Our API has predictable resource-oriented URLs, accepts form-encoded request bodies, returns JSON-encoded responses, and uses standard HTTP response codes, authentication, and verbs.',
            'base_url' => 'https://api.financeai.com/v2',
            'has_code' => false,
        ],
        [
            'id' => 'authentication',
            'badge' => 'Security',
            'title' => 'Authentication',
            'description' => 'Authenticate your API requests using your account\'s secret key. You can manage your API keys in the Command Center. Do not share your secret API keys in publicly accessible areas such as GitHub or client-side code.',
            'base_url' => null,
            'has_code' => true,
            'req_curl' => "curl https://api.financeai.com/v2/nodes/me \\\n  -H \"Authorization: Bearer sk_live_••••••••••••••••••••••\"",
            'req_node' => "const financeai = require('financeai')('sk_live_...');\n\nconst node = await financeai.nodes.retrieve('me');\nconsole.log(node);",
            'req_php' => "\$financeai = new \FinanceAI\Client('sk_live_...');\n\n\$node = \$financeai->nodes->retrieve('me');\necho \$node->id;",
            'response' => "{\n  \"id\": \"NODE-9A8B7C\",\n  \"object\": \"node\",\n  \"role\": \"master\",\n  \"email\": \"admin@enterprise.com\",\n  \"status\": \"optimal\"\n}"
        ],
        [
            'id' => 'ledgers',
            'badge' => 'Endpoints',
            'title' => 'Create a Ledger Entry',
            'description' => 'Creates a new financial transaction in the global ledger. The AI Heuristic engine will automatically scan this entry for categorization and anomaly detection upon insertion.',
            'method' => 'POST',
            'endpoint' => '/v2/ledgers',
            'parameters' => [
                ['name' => 'amount', 'type' => 'integer', 'req' => true, 'desc' => 'The transaction amount in the smallest currency unit (e.g., 1000 for ₹10.00).'],
                ['name' => 'currency', 'type' => 'string', 'req' => true, 'desc' => 'Three-letter ISO currency code (e.g., inr, usd).'],
                ['name' => 'description', 'type' => 'string', 'req' => false, 'desc' => 'An arbitrary string attached to the object. Often used for vendor names.'],
            ],
            'has_code' => true,
            'req_curl' => "curl https://api.financeai.com/v2/ledgers \\\n  -X POST \\\n  -H \"Authorization: Bearer sk_live_...\" \\\n  -H \"Content-Type: application/json\" \\\n  -d '{\n    \"amount\": 450000,\n    \"currency\": \"inr\",\n    \"description\": \"AWS Cloud Infrastructure\"\n  }'",
            'req_node' => "const ledger = await financeai.ledgers.create({\n  amount: 450000,\n  currency: 'inr',\n  description: 'AWS Cloud Infrastructure'\n});",
            'req_php' => "\$ledger = \$financeai->ledgers->create([\n  'amount' => 450000,\n  'currency' => 'inr',\n  'description' => 'AWS Cloud Infrastructure'\n]);",
            'response' => "{\n  \"id\": \"LDG-5F3A19\",\n  \"object\": \"ledger\",\n  \"amount\": 450000,\n  \"currency\": \"inr\",\n  \"description\": \"AWS Cloud Infrastructure\",\n  \"ai_category\": \"infrastructure_cloud\",\n  \"anomaly_score\": 0.02,\n  \"created_at\": 1711478197\n}"
        ],
        [
            'id' => 'forecasts',
            'badge' => 'Endpoints',
            'title' => 'Generate AI Forecast',
            'description' => 'Triggers the predictive heuristic engine to generate a 6-month runway forecast based on trailing 30-day burn rates and capital inflows.',
            'method' => 'GET',
            'endpoint' => '/v2/forecasts/runway',
            'parameters' => [
                ['name' => 'confidence_interval', 'type' => 'float', 'req' => false, 'desc' => 'Desired statistical confidence (default 0.95).'],
            ],
            'has_code' => true,
            'req_curl' => "curl https://api.financeai.com/v2/forecasts/runway?confidence_interval=0.95 \\\n  -H \"Authorization: Bearer sk_live_...\"",
            'req_node' => "const forecast = await financeai.forecasts.runway({\n  confidence_interval: 0.95\n});",
            'req_php' => "\$forecast = \$financeai->forecasts->runway([\n  'confidence_interval' => 0.95\n]);",
            'response' => "{\n  \"object\": \"forecast_report\",\n  \"current_burn_rate\": 125000,\n  \"runway_months\": 14.2,\n  \"risk_status\": \"optimal\",\n  \"recommendations\": [\n    \"Maintain current equity allocation.\",\n    \"Audit AWS Cloud spending.\"\n  ]\n}"
        ]
    ];
@endphp

<div x-data="apiDocsEngine()" class="min-h-screen bg-[#f8fafc] font-sans selection:bg-indigo-500 selection:text-white relative flex flex-col pt-[72px]">

    {{-- Top API Navbar --}}
    <div class="sticky top-[72px] z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200 shadow-sm px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between animate-fade-in-up">
        <div class="flex items-center gap-4">
            <span class="bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded shadow-sm">v2.0 Active</span>
            <span class="text-sm font-bold text-slate-600 hidden sm:block">FinanceAI Developer Documentation</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden md:block">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search API docs... (Press '/')" class="pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 outline-none transition-all w-64">
            </div>
            <a href="{{ route('pages.changelog') ?? '#' }}" @mouseenter="playHover()" @click="playClick()" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">Changelog</a>
        </div>
    </div>

    <div class="flex-1 max-w-[1600px] mx-auto w-full grid grid-cols-1 lg:grid-cols-12 items-start relative">

        {{-- ================= 1. LEFT SIDEBAR (NAVIGATION) ================= --}}
        <div class="hidden lg:block lg:col-span-2 sticky top-[130px] h-[calc(100vh-130px)] overflow-y-auto scrollbar-hide border-r border-slate-200 py-8 pr-6 animate-fade-in-up" style="animation-delay: 100ms;">
            <nav class="space-y-1">
                @foreach($navigation as $nav)
                    @if($nav['is_header'])
                        <div class="pt-6 pb-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $nav['label'] }}</span>
                        </div>
                    @else
                        <a href="#{{ $nav['id'] }}" @click="activeSection = '{{ $nav['id'] }}'; playClick()" @mouseenter="playHover()" 
                           :class="activeSection === '{{ $nav['id'] }}' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900'"
                           class="block px-3 py-2 rounded-lg text-sm transition-colors focus:outline-none">
                            {{ $nav['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- ================= 2. MAIN CONTENT & CODE (STRIPE-STYLE SPLIT) ================= --}}
        <div class="col-span-1 lg:col-span-10 w-full relative animate-fade-in-up" style="animation-delay: 200ms;">
            
            @foreach($endpoints as $endpoint)
                <div id="{{ $endpoint['id'] }}" class="endpoint-section border-b border-slate-200 last:border-0 grid grid-cols-1 xl:grid-cols-12 gap-y-8 xl:gap-x-12 py-16 px-4 sm:px-6 lg:px-12 items-start relative group" x-intersect="activeSection = '{{ $endpoint['id'] }}'">
                    
                    {{-- Anchor Link Offset Fix --}}
                    <div class="absolute -top-[130px]" id="{{ $endpoint['id'] }}-anchor"></div>

                    {{-- Text Column (Left) --}}
                    <div class="xl:col-span-5 space-y-6 relative z-10">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 px-2 py-1 rounded">{{ $endpoint['badge'] }}</span>
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-4 mb-3">{{ $endpoint['title'] }}</h2>
                            <p class="text-sm font-medium text-slate-600 leading-relaxed">{{ $endpoint['description'] }}</p>
                        </div>

                        @if(isset($endpoint['base_url']))
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-inner">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Base URL</p>
                                <code class="text-sm font-mono font-bold text-slate-800">{{ $endpoint['base_url'] }}</code>
                            </div>
                        @endif

                        @if(isset($endpoint['method']))
                            <div class="flex items-center gap-3 mt-4">
                                @php $mColor = $endpoint['method'] === 'GET' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'; @endphp
                                <span class="px-2.5 py-1 rounded {{ $mColor }} text-[10px] font-black tracking-widest">{{ $endpoint['method'] }}</span>
                                <code class="text-sm font-mono font-bold text-slate-800">{{ $endpoint['endpoint'] }}</code>
                            </div>
                        @endif

                        @if(isset($endpoint['parameters']) && count($endpoint['parameters']) > 0)
                            <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 mb-4">Parameters</h3>
                                <ul class="space-y-4">
                                    @foreach($endpoint['parameters'] as $param)
                                        <li class="pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                                            <div class="flex items-center gap-3 mb-1">
                                                <code class="text-sm font-mono font-bold text-slate-900">{{ $param['name'] }}</code>
                                                <span class="text-[10px] font-mono text-slate-400">{{ $param['type'] }}</span>
                                                @if($param['req'])
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded">Required</span>
                                                @else
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">Optional</span>
                                                @endif
                                            </div>
                                            <p class="text-xs font-medium text-slate-600">{{ $param['desc'] }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Code Column (Right) --}}
                    @if($endpoint['has_code'])
                        <div class="xl:col-span-7 bg-[#0f172a] rounded-2xl border border-slate-700 shadow-2xl overflow-hidden sticky top-[150px] transform transition-transform duration-500 group-hover:shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                            
                            {{-- Language Switcher & Mac Header --}}
                            <div class="bg-[#1e293b] border-b border-slate-700 flex items-center justify-between px-4">
                                <div class="flex items-center gap-2 mr-4">
                                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                </div>
                                <div class="flex gap-1 overflow-x-auto scrollbar-hide">
                                    <button @click="lang = 'curl'; playClick()" @mouseenter="playHover()" :class="lang === 'curl' ? 'border-indigo-400 text-white bg-slate-800/50' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-3 text-[11px] font-mono font-bold uppercase tracking-widest border-b-2 transition-all focus:outline-none whitespace-nowrap">cURL</button>
                                    <button @click="lang = 'node'; playClick()" @mouseenter="playHover()" :class="lang === 'node' ? 'border-indigo-400 text-white bg-slate-800/50' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-3 text-[11px] font-mono font-bold uppercase tracking-widest border-b-2 transition-all focus:outline-none whitespace-nowrap">Node.js</button>
                                    <button @click="lang = 'php'; playClick()" @mouseenter="playHover()" :class="lang === 'php' ? 'border-indigo-400 text-white bg-slate-800/50' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-3 text-[11px] font-mono font-bold uppercase tracking-widest border-b-2 transition-all focus:outline-none whitespace-nowrap">PHP</button>
                                </div>
                            </div>

                            {{-- Request Window --}}
                            <div class="relative group/req">
                                <div class="absolute right-3 top-3 opacity-0 group-hover/req:opacity-100 transition-opacity z-20">
                                    <button @click="copyCode($refs.req_{{ $endpoint['id'] }}.innerText)" @mouseenter="playHover()" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-600 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors flex items-center justify-center focus:outline-none" title="Copy Request">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                                <div class="p-5 overflow-x-auto text-[13px] font-mono leading-relaxed" style="tab-size: 2;">
                                    <div x-show="lang === 'curl'" x-ref="req_{{ $endpoint['id'] }}" x-html="syntaxHighlight(`{{ str_replace('"', '\"', $endpoint['req_curl']) }}`)"></div>
                                    <div x-show="lang === 'node'" x-ref="req_{{ $endpoint['id'] }}" style="display: none;" x-html="syntaxHighlight(`{{ str_replace('"', '\"', $endpoint['req_node']) }}`)"></div>
                                    <div x-show="lang === 'php'"  x-ref="req_{{ $endpoint['id'] }}" style="display: none;" x-html="syntaxHighlight(`{{ str_replace('"', '\"', $endpoint['req_php']) }}`)"></div>
                                </div>
                            </div>

                            {{-- Response Window --}}
                            <div class="border-t border-slate-700 bg-[#0b1120] relative group/res">
                                <div class="absolute right-3 top-3 opacity-0 group-hover/res:opacity-100 transition-opacity z-20">
                                    <button @click="copyCode(`{{ str_replace('"', '\"', $endpoint['response']) }}`)" @mouseenter="playHover()" class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-600 text-slate-400 hover:text-emerald-400 hover:bg-slate-700 transition-colors flex items-center justify-center focus:outline-none" title="Copy Response">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                                <div class="px-4 py-2 border-b border-slate-800 flex justify-between items-center">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500 font-mono flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Response (200 OK)</span>
                                </div>
                                <div class="p-5 overflow-x-auto text-[12px] font-mono leading-loose" x-html="syntaxHighlight(`{{ str_replace('"', '\"', $endpoint['response']) }}`)"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
    </div>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Copied to clipboard</span>
</div>

@endsection

@push('styles')
<style>
    /* Premium Scrollbars */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    /* Code Highlighting Core Colors */
    .str { color: #34d399; } /* emerald-400 */
    .num { color: #38bdf8; } /* sky-400 */
    .key { color: #a5b4fc; } /* indigo-300 */
    .boo { color: #fb7185; } /* rose-400 */
    .kwd { color: #c084fc; font-weight: bold; } /* purple-400 */
    .var { color: #f472b6; } /* rose-400 */
    .pnc { color: #94a3b8; } /* slate-400 */
</style>
@endpush

@push('scripts')
<script>
// ================= AUDIO ENGINE =================
window.audioEngine = {
    ctx: null, lastHover: 0,
    init() { if(!this.ctx) { const AC = window.AudioContext || window.webkitAudioContext; if(AC) this.ctx = new AC(); } },
    playClick() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(800, this.ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(300, this.ctx.currentTime + 0.05);
        gain.gain.setValueAtTime(0.1, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.05);
        osc.start(); osc.stop(this.ctx.currentTime + 0.05);
    },
    playHover() {
        const now = Date.now(); if(now - this.lastHover < 50) return; this.lastHover = now;
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(400, this.ctx.currentTime); gain.gain.setValueAtTime(0.015, this.ctx.currentTime); 
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.03); osc.start(); osc.stop(this.ctx.currentTime + 0.03);
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('apiDocsEngine', () => ({
        activeSection: 'introduction',
        lang: 'curl', // 'curl', 'node', 'php'

        playClick() { window.audioEngine.playClick(); },
        playHover() { window.audioEngine.playHover(); },

        async copyCode(text) {
            this.playClick();
            // Remove any HTML tags that might have been added by the syntax highlighter
            const cleanText = text.replace(/<[^>]*>?/gm, '');
            try {
                await navigator.clipboard.writeText(cleanText);
                this.showToast('Code snippet copied to clipboard!');
            } catch (err) {
                const textarea = document.createElement('textarea');
                textarea.value = cleanText;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showToast('Code snippet copied to clipboard!');
            }
        },

        showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
        },

        // Advanced Regex Syntax Highlighter
        syntaxHighlight(code) {
            if (typeof code !== 'string') return '';
            
            // Protect HTML entities first
            code = code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            
            // Check if it looks like JSON
            if (code.trim().startsWith('{') || code.trim().startsWith('[')) {
                return code.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    let cls = 'num'; 
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) cls = 'key'; 
                        else cls = 'str'; 
                    } else if (/true|false/.test(match)) { cls = 'boo'; } 
                    else if (/null/.test(match)) { cls = 'pnc'; }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
            }

            // Highlight basic keywords for JS/PHP/cURL
            code = code.replace(/\b(const|await|require|console|echo|new|curl|-X|-H|-d)\b/g, '<span class="kwd">$1</span>');
            // Highlight strings (single and double quotes)
            code = code.replace(/('.*?'|".*?")/g, '<span class="str">$1</span>');
            // Highlight variables in PHP ($var)
            code = code.replace(/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/g, '<span class="var">$1</span>');
            // Punctuation
            code = code.replace(/([{}[\](),:;])/g, '<span class="pnc">$1</span>');

            return code;
        }
    }));
});
</script>
@endpush