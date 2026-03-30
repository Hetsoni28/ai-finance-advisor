@extends('layouts.app')

@section('title', 'FinanceAI - Core Intelligence')

@section('content')

@php
    $user = auth()->user();
    
    // Simulated Financial Context
    $context = [
        'net_worth'    => '₹9,70,000',
        'monthly_burn' => '₹45,000',
        'top_category' => 'Dining & Ent.',
        'risk_status'  => 'Optimal',
        'last_sync'    => now()->format('h:i:s A')
    ];

    // High-Value Prompts
    $prompts = [
        ['icon' => 'fa-magnifying-glass-chart', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50 border-indigo-100', 'title' => 'Deep Audit', 'prompt' => '/analyze'],
        ['icon' => 'fa-route', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50 border-emerald-100', 'title' => 'Capital Allocation', 'prompt' => 'I want to reach ₹1,500,000 in liquid assets by next year. Generate an allocation plan.'],
        ['icon' => 'fa-building-columns', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50 border-amber-100', 'title' => 'Risk Mitigation', 'prompt' => '/runway'],
        ['icon' => 'fa-database', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50 border-rose-100', 'title' => 'Top Expenses', 'prompt' => 'What is my biggest expense category?'],
    ];

    $chats = $chats ?? collect([]);
@endphp

<div class="min-h-[calc(100vh-4rem)] bg-[#f8fafc] p-4 sm:p-6 lg:p-8 font-sans selection:bg-indigo-500 selection:text-white relative overflow-hidden flex flex-col">
    
    {{-- Pristine Light Ambient Background --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[800px] bg-gradient-to-b from-indigo-50/80 to-transparent rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute bottom-0 right-0 w-[800px] h-[600px] bg-sky-50/40 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="max-w-[1600px] w-full mx-auto relative z-10 flex flex-col xl:flex-row gap-6 flex-1 h-[calc(100vh-8rem)] min-h-[600px]">

        {{-- ================= 1. MAIN CHAT INTERFACE ================= --}}
        <div class="flex-1 flex flex-col bg-white/90 backdrop-blur-xl rounded-[2.5rem] ring-1 ring-slate-900/5 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.05)] overflow-hidden relative group/app" id="chatContainer" data-csrf="{{ csrf_token() }}" data-url="{{ Route::has('user.ai.chat.send') ? route('user.ai.chat.send') : '/api/chat/mock' }}">
            
            {{-- Network Loading Bar (Top) --}}
            <div id="networkProgress" class="absolute top-0 left-0 h-1.5 bg-indigo-500 w-0 transition-all duration-300 z-50 shadow-[0_0_15px_rgba(79,70,229,1)]"></div>

            {{-- Chat Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100/80 bg-white/95 backdrop-blur-md z-30 shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Interactive Model Selector --}}
                    <div class="relative group/model" id="modelSelector">
                        <button onmouseenter="audioEngine.playHover()" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 rounded-2xl border border-transparent hover:border-slate-200 transition-all focus:outline-none shadow-sm hover:shadow-md">
                            <div class="w-8 h-8 rounded-[10px] bg-indigo-600 text-white flex items-center justify-center shadow-inner shrink-0 relative overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                <i class="fa-solid fa-sparkles text-xs relative z-10"></i>
                            </div>
                            <div class="text-left hidden sm:block">
                                <span class="text-sm font-black text-slate-900 tracking-tight block leading-tight" id="currentModel">FinanceAI Core</span>
                                <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Socket
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-2"></i>
                        </button>
                        
                        {{-- Dropdown Menu --}}
                        <div class="absolute top-full left-0 mt-3 w-72 bg-white border border-slate-200 rounded-[1.5rem] shadow-2xl opacity-0 invisible group-hover/model:opacity-100 group-hover/model:visible transition-all duration-300 transform origin-top-left scale-95 group-hover/model:scale-100 z-50 p-2">
                            <div class="p-3 hover:bg-indigo-50/50 rounded-xl cursor-pointer flex gap-3.5 transition-all border border-transparent hover:border-indigo-100 group/item" onclick="switchModel('FinanceAI Core', 'Lightning fast general analytics.')">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 group-hover/item:scale-110 transition-transform shadow-sm"><i class="fa-solid fa-bolt"></i></div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 tracking-tight">FinanceAI Core</p>
                                    <p class="text-xs text-slate-500 font-medium">Lightning fast heuristic analysis.</p>
                                </div>
                            </div>
                            <div class="p-3 hover:bg-emerald-50/50 rounded-xl cursor-pointer flex gap-3.5 transition-all border border-transparent hover:border-emerald-100 group/item mt-1" onclick="switchModel('Reasoning Engine', 'Advanced predictive forecasting.')">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 group-hover/item:scale-110 transition-transform shadow-sm"><i class="fa-solid fa-brain"></i></div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 tracking-tight">Reasoning Engine <span class="px-1.5 py-0.5 bg-indigo-500 text-white rounded-[5px] text-[8px] ml-1 uppercase tracking-widest">o1</span></p>
                                    <p class="text-xs text-slate-500 font-medium">Advanced multi-step forecasting.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <button onclick="toggleMobileSidebar()" onmouseenter="audioEngine.playHover()" class="xl:hidden flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-xs font-black uppercase tracking-widest rounded-xl border border-slate-200 transition-all shadow-sm focus:outline-none">
                        <i class="fa-solid fa-database"></i> Context
                    </button>

                    <button onclick="exportTranscript()" onmouseenter="audioEngine.playHover()" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-xl border border-slate-200 transition-all shadow-sm focus:outline-none">
                        <i class="fa-solid fa-download"></i> Export
                    </button>
                    <button onclick="clearChat()" onmouseenter="audioEngine.playHover()" class="flex items-center justify-center w-9 h-9 bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 rounded-xl border border-rose-100 hover:border-rose-500 transition-all shadow-sm focus:outline-none" title="Purge Context">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Scroll To Bottom FAB --}}
            <button id="scrollToBottomBtn" onclick="scrollToBottom()" class="absolute bottom-40 right-8 w-12 h-12 bg-white border border-slate-200 text-slate-600 rounded-full shadow-xl flex items-center justify-center transform translate-y-10 opacity-0 transition-all duration-300 z-30 hover:bg-slate-50 hover:text-indigo-600 focus:outline-none hover:scale-110">
                <i class="fa-solid fa-arrow-down"></i>
            </button>

            {{-- Chat Scroll Area --}}
            <div id="chatScrollArea" class="flex-1 overflow-y-auto p-4 sm:p-8 bg-transparent scrollbar-custom relative scroll-smooth">
                
                <div id="chatHistory" class="space-y-8 max-w-4xl mx-auto pb-4">
                    
                    {{-- RENDER EXISTING CHATS --}}
                    @if($chats->isEmpty())
                        {{-- Handled by JS initEmptyState() --}}
                    @else
                        @foreach($chats as $chat)
                            @if($chat->sender === 'user')
                                <div class="flex justify-end animate-fade-in-up w-full">
                                    <div class="max-w-[85%] md:max-w-[75%] flex flex-col items-end">
                                        <div class="bg-indigo-600 text-white px-6 py-4 rounded-[2rem] rounded-tr-sm shadow-md font-medium text-[15px] leading-relaxed relative overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-white/0 to-white/10 pointer-events-none"></div>
                                            {!! nl2br(htmlspecialchars($chat->message)) !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start gap-5 animate-fade-in-up group w-full">
                                    <div class="w-10 h-10 rounded-[1rem] bg-white ring-1 ring-slate-900/5 flex items-center justify-center text-indigo-600 shadow-sm shrink-0 mt-1 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-indigo-50/50"></div>
                                        <i class="fa-solid fa-sparkles text-sm relative z-10"></i>
                                    </div>
                                    <div class="max-w-[90%] md:max-w-[85%] w-full">
                                        <div class="text-slate-800 font-medium text-[15px] leading-loose markdown-content raw-markdown hidden" data-raw="{{ htmlspecialchars($chat->message) }}"></div>
                                        <div class="parsed-content">
                                            <div class="h-6 bg-slate-100 rounded animate-pulse w-1/3 mb-2"></div>
                                            <div class="h-6 bg-slate-100 rounded animate-pulse w-1/2"></div>
                                        </div>
                                        <div class="flex items-center gap-2 mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="copyToClipboard(this)" onmouseenter="audioEngine.playHover()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm"><i class="fa-regular fa-copy text-xs"></i></button>
                                            <button onmouseenter="audioEngine.playHover()" onclick="audioEngine.playSuccess(); showToast('Feedback recorded.')" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 transition-all shadow-sm"><i class="fa-regular fa-thumbs-up text-xs"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- LIVE REASONING INDICATOR --}}
                <div id="typingIndicator" class="hidden flex flex-col justify-start gap-2 mt-8 animate-fade-in-up max-w-4xl mx-auto pb-4">
                    <div class="flex justify-start gap-5 w-full">
                        <div class="w-10 h-10 rounded-[1rem] bg-white ring-1 ring-slate-900/5 flex items-center justify-center text-indigo-600 shadow-sm shrink-0 relative overflow-hidden">
                            <div class="absolute inset-0 bg-indigo-500/10 animate-pulse"></div>
                            <i class="fa-solid fa-sparkles animate-spin-slow relative z-10 text-sm"></i>
                        </div>
                        <div class="w-full">
                            <details class="group/reasoning" open>
                                <summary class="flex items-center gap-2 cursor-pointer list-none text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors py-2 select-none outline-none">
                                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform group-open/reasoning:rotate-90"></i>
                                    <span id="reasoningText" class="text-indigo-500 animate-pulse">Analyzing ledger constraints...</span>
                                </summary>
                                <div class="pl-5 border-l-2 border-slate-100 mt-2 space-y-2 text-[11px] font-mono text-slate-500">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-400"></i> Intent classified.</div>
                                    <div class="flex items-center gap-2 animate-pulse text-indigo-500"><i class="fa-solid fa-database"></i> Executing heuristic query...</div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chat Input Area & Slash Commands --}}
            <div class="p-4 sm:p-6 lg:px-8 lg:pb-8 lg:pt-4 bg-white/95 backdrop-blur-xl border-t border-slate-100 z-40 shrink-0 relative">
                
                {{-- SLASH COMMAND MENU --}}
                <div id="slashMenu" class="absolute bottom-full left-8 mb-4 w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl opacity-0 invisible transition-all duration-200 transform translate-y-4 z-50 overflow-hidden">
                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center justify-between">
                        Quick Commands <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-600 rounded text-[8px]">ESC to close</span>
                    </div>
                    <div class="p-2 space-y-1">
                        <button onclick="insertPrompt('/analyze')" class="w-full text-left p-2.5 hover:bg-indigo-50 rounded-xl flex items-center gap-3 transition-colors group/cmd focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 group-hover/cmd:bg-indigo-600 group-hover/cmd:text-white transition-colors"><i class="fa-solid fa-chart-pie text-xs"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">/analyze</p>
                                <p class="text-[10px] text-slate-500">Generate 30-day spending report</p>
                            </div>
                        </button>
                        <button onclick="insertPrompt('/runway')" class="w-full text-left p-2.5 hover:bg-emerald-50 rounded-xl flex items-center gap-3 transition-colors group/cmd focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 group-hover/cmd:bg-emerald-600 group-hover/cmd:text-white transition-colors"><i class="fa-solid fa-plane-departure text-xs"></i></div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">/runway</p>
                                <p class="text-[10px] text-slate-500">Calculate financial survival rate</p>
                            </div>
                        </button>
                    </div>
                </div>

                <form id="chatForm" class="relative max-w-4xl mx-auto flex flex-col gap-2 bg-white border border-slate-200 rounded-[2rem] p-2 shadow-[0_2px_15px_rgba(0,0,0,0.03)] focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-300 focus-within:shadow-xl transition-all duration-500">
                    
                    <div class="flex items-end gap-3 w-full">
                        {{-- Attach Button Menu --}}
                        <div class="relative group/attach shrink-0">
                            <button type="button" onmouseenter="audioEngine.playHover()" class="w-12 h-12 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50 focus:outline-none">
                                <i class="fa-solid fa-paperclip text-lg"></i>
                            </button>
                            <div class="absolute bottom-full left-0 mb-3 w-56 bg-white border border-slate-200 rounded-[1.5rem] shadow-2xl opacity-0 invisible group-hover/attach:opacity-100 group-hover/attach:visible transition-all duration-300 transform origin-bottom-left scale-95 group-hover/attach:scale-100 p-2 z-50">
                                <button type="button" onclick="showToast('Document parsing is active.')" class="w-full text-left p-3 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-3 transition-colors"><div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100"><i class="fa-solid fa-file-pdf"></i></div> Upload Document</button>
                                <button type="button" onclick="showToast('Database synced.')" class="w-full text-left p-3 hover:bg-slate-50 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-3 transition-colors"><div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100"><i class="fa-solid fa-database"></i></div> Sync Ledger DB</button>
                            </div>
                        </div>

                        <textarea id="messageInput" rows="1" required placeholder="Ask anything... (Type '/' for commands)" 
                                  class="flex-1 max-h-48 min-h-[48px] bg-transparent border-0 focus:ring-0 text-slate-900 text-[15px] font-medium resize-none py-3.5 px-2 placeholder-slate-400 outline-none scrollbar-custom leading-relaxed"></textarea>

                        {{-- Send / Stop Container --}}
                        <div id="btnContainer" class="shrink-0 relative w-12 h-12">
                            <button type="submit" id="sendBtn" disabled
                                    class="absolute inset-0 w-full h-full bg-slate-900 text-white rounded-full flex items-center justify-center shadow-md hover:bg-indigo-600 disabled:opacity-40 disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none transition-all duration-300 transform active:scale-95 focus:outline-none z-10">
                                <i class="fa-solid fa-arrow-up text-lg"></i>
                            </button>
                            
                            <button type="button" id="stopBtn" onclick="stopGeneration()"
                                    class="absolute inset-0 w-full h-full bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-600 transition-all duration-300 transform active:scale-95 focus:outline-none opacity-0 pointer-events-none z-20 scale-75">
                                <i class="fa-solid fa-stop text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Live Draft Tokenizer --}}
                    <div class="w-full flex justify-between items-center px-4 pb-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">FinanceAI heuristic engine v2.0</span>
                        <span class="text-[9px] font-mono font-bold text-slate-400" id="draftTokens">0 Tokens</span>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= 2. CONTEXT SIDEBAR (Desktop & Mobile Slide-out) ================= --}}
        <div id="contextSidebar" class="fixed xl:relative top-0 right-0 h-full w-80 bg-[#f8fafc] xl:bg-transparent transform translate-x-full xl:translate-x-0 transition-transform duration-500 z-[100] xl:z-20 p-6 xl:p-0 flex flex-col gap-6 overflow-y-auto scrollbar-hide border-l border-slate-200 xl:border-none shadow-2xl xl:shadow-none">
            
            <button onclick="toggleMobileSidebar()" class="xl:hidden self-end w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-slate-200 shadow-sm text-slate-500 mb-2">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            {{-- Data Context Panel --}}
            <div class="bg-white rounded-[2.5rem] p-7 ring-1 ring-slate-900/5 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-colors"></div>
                <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center justify-between relative z-10">
                    <span class="flex items-center gap-2"><div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shadow-inner"><i class="fa-solid fa-database text-[10px]"></i></div> Context</span>
                    <button onclick="syncContext()" id="syncBtn" class="text-[9px] text-slate-400 hover:text-indigo-600 font-mono flex items-center gap-1 transition-colors focus:outline-none">
                        <i class="fa-solid fa-rotate"></i> <span id="syncTime">{{ $context['last_sync'] }}</span>
                    </button>
                </h3>
                
                <div class="space-y-3 relative z-10">
                    <div class="p-4 bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 hover:border-indigo-200 transition-colors shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Net Asset Value</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tight">{{ $context['net_worth'] }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-4 bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 hover:border-rose-200 transition-colors shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Burn</p>
                            <p class="text-sm font-black text-rose-600 tracking-tight">{{ $context['monthly_burn'] }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 hover:bg-white rounded-2xl border border-slate-200 hover:border-emerald-200 transition-colors shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Health</p>
                            <p class="text-sm font-black text-emerald-600 uppercase">{{ $context['risk_status'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Token Tracker --}}
            <div class="bg-white rounded-[2.5rem] p-7 ring-1 ring-slate-900/5 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl pointer-events-none"></div>
                <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-widest mb-5 flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-inner"><i class="fa-solid fa-microchip text-[10px]"></i></div>
                    Compute Usage
                </h3>
                
                <div class="flex items-center gap-5">
                    <div class="relative w-14 h-14 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"/>
                            <path id="tokenRing" class="text-indigo-500 transition-all duration-1000 ease-out" stroke-dasharray="12, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-slate-900" id="tokenPct">12%</div>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Tokens Processed</span>
                        <span class="text-xl font-black text-indigo-600 tracking-tight" id="tokenCounter">1,204</span>
                        <span class="text-[10px] font-bold text-slate-400">/ 10k</span>
                    </div>
                </div>
            </div>

            {{-- LIVE SYSTEM TERMINAL --}}
            <div class="bg-[#0f172a] rounded-[2.5rem] p-7 shadow-2xl text-white relative overflow-hidden group mt-auto border border-slate-800 flex-1 min-h-[220px] flex flex-col">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay pointer-events-none"></div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-150 pointer-events-none"></div>
                
                <div class="relative z-10 flex-1 flex flex-col">
                    <h3 class="font-black text-xs uppercase tracking-widest mb-4 flex items-center justify-between text-slate-300 border-b border-slate-700/50 pb-3">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-terminal text-emerald-400"></i> Node Terminal</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </h3>
                    <div id="systemTerminal" class="flex-1 font-mono text-[10px] text-slate-400 space-y-2.5 overflow-y-auto scrollbar-hide flex flex-col justify-end leading-relaxed pb-2">
                        <div><span class="text-emerald-500">[SYS]</span> Secure Node authenticated.</div>
                        <div><span class="text-indigo-400">[NET]</span> TLS 1.3 Handshake OK.</div>
                        <div><span class="text-sky-400">[DB]</span> Ledger synchronized successfully.</div>
                        <div class="animate-pulse text-slate-300">_</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Notification Toast --}}
<div id="toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 backdrop-blur-xl text-white px-6 py-3.5 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] flex items-center gap-3.5 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none border border-slate-700">
    <i id="toastIcon" class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
    <span id="toastMsg" class="text-sm font-bold tracking-wide">Action completed</span>
</div>

@endsection

@push('styles')
<style>
    .scrollbar-custom::-webkit-scrollbar { width: 4px; height: 4px; }
    .scrollbar-custom::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-custom::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .scrollbar-custom:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    /* Advanced Markdown Styling */
    .markdown-content { word-break: break-word; color: #334155; }
    .markdown-content strong { color: #0f172a; font-weight: 900; }
    .markdown-content em { font-style: italic; color: #64748b; }
    .markdown-content ul { list-style-type: disc; padding-left: 1.5rem; margin: 0.75rem 0; color: #475569;}
    .markdown-content ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0.75rem 0; color: #475569;}
    .markdown-content p { margin-bottom: 0.75rem; }
    .markdown-content p:last-child { margin-bottom: 0; }
    
    .markdown-content code:not(.code-block code) { 
        background-color: #f1f5f9; padding: 0.15rem 0.35rem; border-radius: 0.375rem; 
        font-family: ui-monospace, monospace; font-size: 0.85em; color: #db2777; border: 1px solid #e2e8f0; font-weight: bold;
    }
    
    /* Code Blocks */
    .code-block {
        background-color: #0f172a; border-radius: 1.25rem; overflow: hidden; margin: 1.25rem 0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); border: 1px solid #1e293b;
    }
    .code-header {
        background-color: #1e293b; display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid #334155; padding: 10px 16px;
    }
    .code-mac-dots { display: flex; gap: 6px; }
    .code-mac-dots div { width: 10px; height: 10px; border-radius: 50%; }
    .code-mac-dots .red { background-color: #ff5f56; }
    .code-mac-dots .yellow { background-color: #ffbd2e; }
    .code-mac-dots .green { background-color: #27c93f; }
    .code-lang { font-family: monospace; font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; margin-left: auto; margin-right: 15px;}
    .code-btn { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 0.7rem; font-weight: bold; display: flex; gap: 6px; align-items: center; transition: color 0.2s;}
    .code-btn:hover { color: #fff; }
    .code-body { padding: 1.25rem; overflow-x: auto; color: #e2e8f0; font-family: ui-monospace, monospace; font-size: 0.85em; line-height: 1.6; }
    
    /* Syntax Highlighting */
    .syntax-keyword { color: #c678dd; font-weight: bold; }
    .syntax-string { color: #98c379; }
    .syntax-func { color: #61afef; }

    /* Markdown Tables */
    .markdown-table { width: 100%; border-collapse: collapse; margin: 1.25rem 0; font-size: 0.875rem; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
    .markdown-table th { background-color: #f8fafc; color: #64748b; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.65rem; padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .markdown-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600; }
    .markdown-table tr:last-child td { border-bottom: none; }
    .markdown-table tr:hover td { background-color: #f8fafc; }
</style>
@endpush

@push('scripts')
<script>
// ================= AUDIO ENGINE =================
const audioEngine = {
    ctx: null,
    lastHover: 0,
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
        // Debounce to prevent audio context memory leak if swiping mouse
        const now = Date.now(); if(now - this.lastHover < 50) return; this.lastHover = now;
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(400, this.ctx.currentTime); gain.gain.setValueAtTime(0.015, this.ctx.currentTime); 
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.03); osc.start(); osc.stop(this.ctx.currentTime + 0.03);
    },
    playSuccess() {
        this.init(); if(!this.ctx) return; if(this.ctx.state === 'suspended') this.ctx.resume();
        const osc = this.ctx.createOscillator(); const gain = this.ctx.createGain();
        osc.connect(gain); gain.connect(this.ctx.destination); osc.type = 'sine'; 
        osc.frequency.setValueAtTime(600, this.ctx.currentTime); osc.frequency.setValueAtTime(900, this.ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.05, this.ctx.currentTime); gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.3);
        osc.start(); osc.stop(this.ctx.currentTime + 0.3);
    }
};

document.addEventListener('DOMContentLoaded', () => {

    const chatContainer = document.getElementById('chatContainer');
    const apiUrl = chatContainer.dataset.url;
    const csrfToken = chatContainer.dataset.csrf;
    
    const chatHistory = document.getElementById('chatHistory');
    const chatScrollArea = document.getElementById('chatScrollArea');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const stopBtn = document.getElementById('stopBtn');
    const typingIndicator = document.getElementById('typingIndicator');
    const scrollBtn = document.getElementById('scrollToBottomBtn');
    const networkProgress = document.getElementById('networkProgress');
    const slashMenu = document.getElementById('slashMenu');
    const terminal = document.getElementById('systemTerminal');
    const draftTokens = document.getElementById('draftTokens');
    
    let isGenerating = false;
    let currentTokens = 1204;
    let abortController = null;
    let emptyStateHtml = `
        <div id="emptyState" class="w-full mt-4 sm:mt-12 animate-fade-in-up">
            <div class="text-center mb-14 relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none z-0"></div>
                <div class="w-20 h-20 bg-white border border-slate-200 shadow-xl rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 relative z-10 hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 border border-indigo-100 rounded-[1.5rem] animate-ping opacity-20"></div>
                    <i class="fa-solid fa-sparkles text-3xl text-indigo-600"></i>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-4 tracking-tight relative z-10">System Ready, {{ explode(' ', $user->name ?? 'User')[0] }}.</h2>
                <p class="text-slate-500 font-medium text-base relative z-10">The heuristic engine is synchronized with your ledger. Issue a command.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-4 max-w-3xl mx-auto relative z-10">
                @foreach($prompts as $p)
                <button onclick="insertPrompt('{{ addslashes($p['prompt']) }}')" onmouseenter="audioEngine.playHover()" class="group text-left p-6 bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-500/10 rounded-3xl transition-all duration-300 focus:outline-none transform hover:-translate-y-1">
                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="w-10 h-10 rounded-xl {{ $p['bg'] }} {{ $p['color'] }} flex items-center justify-center border shadow-inner"><i class="fa-solid {{ $p['icon'] }} text-sm"></i></div>
                        <span class="font-black text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $p['title'] }}</span>
                    </div>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ $p['prompt'] }}</p>
                </button>
                @endforeach
            </div>
        </div>
    `;

    // 🚨 BEAST MODE: XSS Escape
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, tag => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'}[tag] || tag));
    }

    // --- Advanced Markdown Parser (Tables + Code) ---
    function parseMarkdown(text) {
        let html = text;
        
        // 1. Parse Code Blocks
        html = html.replace(/```(\w+)?\n([\s\S]*?)```/g, function(match, lang, code) {
            lang = lang ? lang.toLowerCase() : 'code';
            let hCode = code;
            if(lang === 'sql' || lang === 'php') {
                hCode = hCode.replace(/\b(SELECT|FROM|WHERE|AND|OR|INSERT|UPDATE|DELETE|JOIN|GROUP BY|ORDER BY|LIMIT|NOW)\b/gi, '<span class="syntax-keyword">$1</span>');
                hCode = hCode.replace(/('.*?'|".*?")/g, '<span class="syntax-string">$1</span>');
                hCode = hCode.replace(/\b(sum|count|avg|min|max)\b/gi, '<span class="syntax-func">$1</span>');
            }
            return `<div class="code-block">
                        <div class="code-header">
                            <div class="code-mac-dots"><div class="red"></div><div class="yellow"></div><div class="green"></div></div>
                            <span class="code-lang">${lang}</span>
                            <button class="code-btn" onclick="copyCode(this)"><i class="fa-regular fa-copy"></i> Copy</button>
                        </div>
                        <div class="code-body"><code>${hCode}</code></div>
                    </div>`;
        });

        // 2. Parse Markdown Tables (Fixing the Crash)
        html = html.replace(/(?:\|.*?\|\n)+/g, function(match) {
            let rows = match.trim().split('\n');
            let tableHtml = '<div class="overflow-x-auto w-full"><table class="markdown-table">';
            rows.forEach((row, i) => {
                let cols = row.split('|').filter(c => c.trim() !== '');
                if (row.includes('---')) return; // Skip Markdown separator
                tableHtml += '<tr>';
                cols.forEach(col => {
                    let c = col.trim();
                    // Parse inline bolding inside table cells
                    c = c.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
                    tableHtml += i === 0 ? `<th>${c}</th>` : `<td>${c}</td>`;
                });
                tableHtml += '</tr>';
            });
            tableHtml += '</table></div>';
            return tableHtml;
        });

        // 3. Inline Formatting
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        html = html.replace(/^\s*-\s+(.*)$/gm, '<ul><li>$1</li></ul>');
        html = html.replace(/<\/ul>\n<ul>/g, ''); 
        
        // 4. Line breaks (protecting pre/code/table blocks)
        const parts = html.split(/(<div class="code-block">[\s\S]*?<\/div>|<div class="overflow-x-auto w-full">[\s\S]*?<\/div>)/);
        for(let i=0; i<parts.length; i++) {
            if(!parts[i].startsWith('<div')) parts[i] = parts[i].replace(/\n/g, '<br>');
        }
        return parts.join('');
    }

    function formatExistingChats() {
        const rawDivs = document.querySelectorAll('.raw-markdown');
        if(rawDivs.length === 0) {
            // Inject empty state if no chats exist
            chatHistory.innerHTML = emptyStateHtml;
        } else {
            rawDivs.forEach(el => {
                const decodedText = (el.dataset.raw || el.innerHTML).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#039;/g, "'");
                el.nextElementSibling.innerHTML = parseMarkdown(decodedText);
                el.remove(); 
            });
        }
        scrollToBottom();
    }
    formatExistingChats();

    // --- Inputs & Tokenizer ---
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 192) + 'px'; 
        
        let val = this.value.trim();
        if(val.length > 0 && !isGenerating) sendBtn.disabled = false;
        else sendBtn.disabled = true;

        if(val === '/') slashMenu.classList.remove('opacity-0', 'invisible', 'translate-y-4');
        else slashMenu.classList.add('opacity-0', 'invisible', 'translate-y-4');

        // Live Draft Tokens
        let tks = Math.ceil(val.length / 4);
        draftTokens.innerText = tks > 0 ? `${tks} Tokens` : '';
    });

    messageInput.addEventListener('keydown', function(e) {
        if(e.key === 'Escape') slashMenu.classList.add('opacity-0', 'invisible', 'translate-y-4');
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if(this.value.trim().length > 0 && !isGenerating) chatForm.dispatchEvent(new Event('submit'));
        }
    });

    function scrollToBottom() { chatScrollArea.scrollTo({ top: chatScrollArea.scrollHeight, behavior: 'smooth' }); }

    chatScrollArea.addEventListener('scroll', () => {
        const isNearBottom = chatScrollArea.scrollHeight - chatScrollArea.scrollTop - chatScrollArea.clientHeight < 100;
        if(isNearBottom) scrollBtn.classList.add('opacity-0', 'translate-y-10');
        else { scrollBtn.classList.add('opacity-100', 'translate-y-0'); scrollBtn.classList.remove('opacity-0', 'translate-y-10'); }
    });

    function showToast(msg, isError = false) {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        document.getElementById('toastMsg').innerText = msg;
        if(isError) icon.className = "fa-solid fa-triangle-exclamation text-rose-400 text-lg";
        else icon.className = "fa-solid fa-circle-check text-emerald-400 text-lg";
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-20', 'opacity-0'), 3000);
    }

    function logTerminal(msg, type = 'SYS') {
        const colors = { 'SYS': 'text-emerald-500', 'NET': 'text-indigo-400', 'ERR': 'text-rose-500', 'AI': 'text-sky-400' };
        const color = colors[type] || colors['SYS'];
        const line = document.createElement('div');
        line.innerHTML = `<span class="${color}">[${type}]</span> ${msg}`;
        terminal.insertBefore(line, terminal.lastElementChild);
        terminal.scrollTop = terminal.scrollHeight;
    }

    window.toggleMobileSidebar = function() {
        audioEngine.playClick();
        document.getElementById('contextSidebar').classList.toggle('translate-x-full');
    };

    window.switchModel = function(name, desc) {
        audioEngine.playClick();
        document.getElementById('currentModel').innerText = name;
        logTerminal(`Switched to ${name} compute node.`, 'SYS');
        showToast(`Switched to ${name}`);
    };

    window.syncContext = function() {
        audioEngine.playClick();
        const icon = document.querySelector('#syncBtn i');
        icon.classList.add('animate-spin');
        logTerminal('Force synchronizing ledger context...', 'NET');
        setTimeout(() => {
            icon.classList.remove('animate-spin');
            const now = new Date();
            let hours = now.getHours(); let minutes = now.getMinutes(); let seconds = now.getSeconds();
            let ampm = hours >= 12 ? 'PM' : 'AM'; hours = hours % 12; hours = hours ? hours : 12;
            document.getElementById('syncTime').innerText = `${hours.toString().padStart(2,'0')}:${minutes.toString().padStart(2,'0')}:${seconds.toString().padStart(2,'0')} ${ampm}`;
            logTerminal('Context successfully synchronized.', 'SYS');
            showToast('Context synced.');
        }, 1200);
    };

    // --- Streaming Logic ---
    window.stopGeneration = function() {
        if(isGenerating && abortController) {
            audioEngine.playClick();
            abortController.abort(); 
            isGenerating = false;
            toggleSendStopUI(false);
            typingIndicator.classList.add('hidden');
            networkProgress.style.width = '0%';
            logTerminal('Generation aborted by user.', 'ERR');
        }
    }

    function toggleSendStopUI(generating) {
        isGenerating = generating;
        if(generating) {
            sendBtn.classList.add('scale-75', 'opacity-0', 'pointer-events-none');
            stopBtn.classList.remove('scale-75', 'opacity-0', 'pointer-events-none');
            messageInput.disabled = true;
        } else {
            sendBtn.classList.remove('scale-75', 'opacity-0', 'pointer-events-none');
            stopBtn.classList.add('scale-75', 'opacity-0', 'pointer-events-none');
            messageInput.disabled = false;
            if(messageInput.value.trim().length > 0) sendBtn.disabled = false;
        }
    }

    function streamText(element, htmlContent) {
        element.innerHTML = '';
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;
        let isStopped = false; 

        const origStop = stopBtn.onclick;
        stopBtn.onclick = function() { isStopped = true; window.stopGeneration(); };

        function appendNode(sourceNode, targetNode, callback) {
            if(isStopped) { callback(); return; }

            if (sourceNode.nodeType === Node.TEXT_NODE) {
                let words = sourceNode.textContent.split(/(\s+)/);
                let i = 0;
                function typeWord() {
                    if(isStopped) { callback(); return; }
                    if (i < words.length) {
                        targetNode.appendChild(document.createTextNode(words[i])); i++;
                        scrollToBottom();
                        if(Math.random() > 0.4) updateTokens(1);
                        setTimeout(typeWord, Math.random() * 25 + 5); 
                    } else { callback(); }
                }
                typeWord();
            } else if (sourceNode.nodeType === Node.ELEMENT_NODE) {
                const newElement = document.createElement(sourceNode.tagName);
                for (let attr of sourceNode.attributes) newElement.setAttribute(attr.name, attr.value);
                targetNode.appendChild(newElement);
                
                let childNodes = Array.from(sourceNode.childNodes);
                let i = 0;
                function processNextChild() {
                    if (i < childNodes.length) appendNode(childNodes[i], newElement, () => { i++; processNextChild(); });
                    else callback();
                }
                processNextChild();
            } else { callback(); }
        }
        
        let topLevelNodes = Array.from(tempDiv.childNodes);
        let idx = 0;
        function processTopLevel() {
            if(idx < topLevelNodes.length && !isStopped) {
                appendNode(topLevelNodes[idx], element, () => { idx++; processTopLevel(); });
            } else {
                toggleSendStopUI(false);
                scrollToBottom();
                stopBtn.onclick = origStop; 
                audioEngine.playSuccess();
            }
        }
        processTopLevel();
    }

    function updateTokens(amount) {
        currentTokens += amount;
        document.getElementById('tokenCounter').innerText = currentTokens.toLocaleString('en-US');
        let pct = Math.min((currentTokens / 10000) * 100, 100);
        document.getElementById('tokenRing').setAttribute('stroke-dasharray', `${pct}, 100`);
        document.getElementById('tokenPct').innerText = Math.round(pct) + '%';
    }

    async function simulateReasoning() {
        const rt = document.getElementById('reasoningText');
        const phrases = ['Querying master ledger...', 'Normalizing vectors...', 'Applying heuristic models...', 'Generating response...'];
        for(let phrase of phrases) {
            rt.innerText = phrase;
            await new Promise(r => setTimeout(r, 350));
        }
        rt.innerText = "Analysis complete.";
    }

    // --- Form Submit & Fetch Logic ---
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        audioEngine.playClick();
        
        slashMenu.classList.add('opacity-0', 'invisible', 'translate-y-4');
        const rawMsg = messageInput.value.trim();
        if (!rawMsg || isGenerating) return;

        // XSS Protection
        const safeMsg = escapeHTML(rawMsg);
        const emptyStateDiv = document.getElementById('emptyState');
        if (emptyStateDiv) emptyStateDiv.remove();

        toggleSendStopUI(true);
        messageInput.value = '';
        messageInput.style.height = '48px'; 
        draftTokens.innerText = '';
        networkProgress.style.width = '20%'; 
        logTerminal(`Transmitting payload: ${safeMsg.substring(0, 15)}...`, 'NET');

        const userHtml = `
            <div class="flex justify-end animate-fade-in-up w-full mb-8">
                <div class="max-w-[85%] md:max-w-[75%] flex flex-col items-end">
                    <div class="bg-indigo-600 text-white px-5 sm:px-7 py-3.5 sm:py-5 rounded-[2rem] rounded-tr-sm shadow-md font-medium text-[15px] leading-relaxed">
                        ${safeMsg.replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        `;
        chatHistory.insertAdjacentHTML('beforeend', userHtml);
        scrollToBottom();
        updateTokens(Math.floor(safeMsg.length / 4)); 

        typingIndicator.classList.remove('hidden');
        scrollToBottom();
        
        const reasoningPromise = simulateReasoning();
        abortController = new AbortController();

        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ message: rawMsg }), 
                signal: abortController.signal
            });

            networkProgress.style.width = '80%';
            await reasoningPromise; 
            logTerminal('Response received.', 'AI');

            let replyText = "";
            if (response.ok) {
                const data = await response.json();
                replyText = data.reply || "Empty payload returned.";
            } else {
                replyText = `**Network Desync Detected.** \n\nUnable to reach backend logic. Code 500.`;
                logTerminal('Connection refused (500).', 'ERR');
            }

            networkProgress.style.width = '100%';
            setTimeout(() => { networkProgress.style.width = '0%'; }, 500);

            typingIndicator.classList.add('hidden');
            const aiId = 'ai-msg-' + Date.now();
            
            const aiHtml = `
                <div class="flex justify-start gap-5 animate-fade-in-up group w-full mb-8">
                    <div class="w-10 h-10 rounded-[1rem] bg-white ring-1 ring-slate-900/5 flex items-center justify-center text-indigo-600 shadow-sm shrink-0 mt-1 relative">
                        <i class="fa-solid fa-sparkles text-sm"></i>
                    </div>
                    <div class="max-w-[90%] md:max-w-[85%] w-full">
                        <div id="${aiId}" class="text-slate-800 font-medium text-[15px] leading-loose markdown-content min-h-[40px]"></div>
                        <div class="flex items-center gap-2 mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="copyToClipboard(this)" onmouseenter="audioEngine.playHover()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 shadow-sm transition-colors"><i class="fa-regular fa-copy text-xs"></i></button>
                        </div>
                    </div>
                </div>
            `;
            chatHistory.insertAdjacentHTML('beforeend', aiHtml);
            
            const msgElement = document.getElementById(aiId);
            const parsedHtml = parseMarkdown(replyText);
            streamText(msgElement, parsedHtml);

        } catch (error) {
            networkProgress.style.width = '0%';
            if(error.name !== 'AbortError') {
                typingIndicator.classList.add('hidden');
                logTerminal('Network execution failed.', 'ERR');
                chatHistory.insertAdjacentHTML('beforeend', `
                    <div class="flex justify-center my-6 animate-fade-in-up">
                        <div class="bg-rose-50 text-rose-600 px-5 py-2.5 rounded-[1rem] text-xs font-bold border border-rose-100 shadow-sm"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Secure connection failed.</div>
                    </div>
                `);
                toggleSendStopUI(false);
            }
        }
    });

    // --- Global Expose ---
    window.insertPrompt = function(text) {
        audioEngine.playClick();
        slashMenu.classList.add('opacity-0', 'invisible', 'translate-y-4');
        messageInput.value = text;
        messageInput.dispatchEvent(new Event('input'));
        messageInput.focus();
        scrollToBottom();
    };

    window.copyToClipboard = function(btn) {
        audioEngine.playClick();
        const textElement = btn.closest('.group').querySelector('.markdown-content');
        navigator.clipboard.writeText(textElement.innerText).then(() => { showToast("Copied to clipboard"); });
    };

    window.copyCode = function(btn) {
        audioEngine.playClick();
        const codeElement = btn.parentElement.parentElement.nextElementSibling.querySelector('code');
        navigator.clipboard.writeText(codeElement.innerText).then(() => {
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Copied';
            setTimeout(() => { btn.innerHTML = '<i class="fa-regular fa-copy"></i> Copy'; }, 2000);
        });
    }

    window.clearChat = function() {
        audioEngine.playClick();
        if(confirm('Clear context window?')) {
            chatHistory.innerHTML = emptyStateHtml;
            currentTokens = 0; updateTokens(0);
            logTerminal('Memory purged.', 'SYS');
            showToast("Memory purged successfully.");
        }
    };

    window.exportTranscript = function() {
        audioEngine.playClick();
        showToast("Compiling PDF Transcript...");
        logTerminal('Exporting ledger context...', 'SYS');
    };

    setTimeout(scrollToBottom, 500); 
});
</script>
@endpush