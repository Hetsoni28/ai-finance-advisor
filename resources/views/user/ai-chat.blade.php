@extends('layouts.app')

@section('title', 'AI Advisor · FinanceAI')

@section('content')
@php
    $limitReached = $todayUsage >= $dailyLimit;
    $suggestions = [
        'Where am I spending the most?',
        'How much can I save each month?',
        'Am I on track for my goals?',
        'How big should my emergency fund be?',
    ];
    $goalCategories = ['emergency_fund' => 'Emergency fund', 'house' => 'House', 'car' => 'Car', 'education' => 'Education', 'retirement' => 'Retirement', 'travel' => 'Travel', 'wedding' => 'Wedding', 'gadget' => 'Gadget', 'custom' => 'Other'];
    $history = $chats->map(fn ($c) => ['me' => $c->sender === \App\Models\AiChat::SENDER_USER, 'text' => $c->message, 'time' => $c->created_at?->format('h:i A')])->values();
@endphp

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
     x-data="advisorChat({
        history: {{ json_encode($history) }},
        used: {{ (int) $todayUsage }},
        limit: {{ (int) $dailyLimit }},
        sendUrl: '{{ route('user.ai.chat.send') }}',
        goalUrl: '{{ route('user.ai.goals.store') }}',
        profileUrl: '{{ route('user.ai.profile.save') }}',
        csrf: '{{ csrf_token() }}'
     })">

    <x-admin.page-header title="AI Advisor" eyebrow="My money" icon="fa-wallet" :stamp="false"
        description="Ask anything about your money. Answers use your own income, expenses, goals and risk profile." />

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Chat --}}
        <section class="flex h-[calc(100vh-15rem)] min-h-[520px] flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-[0_1px_2px_rgba(24,24,27,.04)] lg:col-span-2" aria-label="Conversation">
            <div class="flex items-center justify-between gap-3 border-b border-zinc-100 px-4 py-3 sm:px-5">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white"><i class="fa-solid fa-cube text-xs" aria-hidden="true"></i></span>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900">FinanceAI Advisor</p>
                        <p class="text-xs text-zinc-500">{{ $aiMode === 'gemini' ? 'Powered by Google Gemini' : 'Basic mode — answers come from built-in rules' }}</p>
                    </div>
                </div>
                <span class="text-xs tabular-nums text-zinc-500" x-text="used + ' / ' + limit + ' today'">{{ $todayUsage }} / {{ $dailyLimit }} today</span>
            </div>

            <div x-ref="feed" class="flex-1 space-y-5 overflow-y-auto px-4 py-5 sm:px-5" role="log" aria-live="polite">
                <template x-for="(m, i) in messages" :key="i">
                    <div :class="m.me ? 'flex justify-end' : 'flex gap-3'">
                        <template x-if="!m.me">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-zinc-900 text-white"><i class="fa-solid fa-cube text-[10px]" aria-hidden="true"></i></span>
                        </template>
                        <div :class="m.me ? 'max-w-[80%] rounded-2xl rounded-br-md bg-zinc-900 px-4 py-2.5 text-sm text-white' : 'max-w-[85%] min-w-0'">
                            <template x-if="m.me"><p class="whitespace-pre-line" x-text="m.text"></p></template>
                            <template x-if="!m.me"><div class="advisor-md rounded-2xl rounded-tl-md bg-zinc-100 px-4 py-3 text-sm leading-relaxed text-zinc-800" x-html="render(m.text)"></div></template>
                            <p class="mt-1 text-[11px]" :class="m.me ? 'text-right text-zinc-400' : 'text-zinc-400'" x-text="m.time"></p>
                        </div>
                    </div>
                </template>
                <div x-show="sending" x-cloak class="flex gap-3">
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-zinc-900 text-white"><i class="fa-solid fa-cube text-[10px]" aria-hidden="true"></i></span>
                    <div class="rounded-2xl rounded-tl-md bg-zinc-100 px-4 py-3 text-sm text-zinc-500"><span class="typing-dots"><span></span><span></span><span></span></span> Thinking</div>
                </div>
            </div>

            <div class="border-t border-zinc-100 p-3 sm:p-4">
                <div x-show="messages.length <= 1 && !limitHit" class="mb-3 flex flex-wrap gap-2">
                    @foreach($suggestions as $s)
                        <button type="button" @click="send({{ json_encode($s) }})" class="rounded-full border border-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-600 hover:border-zinc-300 hover:bg-zinc-50">{{ $s }}</button>
                    @endforeach
                </div>
                <template x-if="limitHit">
                    <p class="mb-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900">You’ve used all {{ $dailyLimit }} messages for today. <a href="{{ route('user.subscription.index') }}" class="font-semibold underline">See plans</a> for a higher limit.</p>
                </template>
                <form @submit.prevent="send(draft)" class="flex items-end gap-2">
                    <label for="ask" class="sr-only">Ask the advisor</label>
                    <textarea id="ask" x-model="draft" x-ref="input" rows="1" maxlength="2000" :disabled="limitHit"
                              @keydown.enter.prevent="if (!$event.shiftKey) send(draft); else draft += '\n'"
                              @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 160) + 'px'"
                              placeholder="Ask about your spending, savings or goals…"
                              class="max-h-40 min-h-[44px] flex-1 resize-none rounded-xl border border-zinc-200 px-3.5 py-2.5 text-sm placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 disabled:bg-zinc-50"></textarea>
                    <button :disabled="!draft.trim() || sending || limitHit" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-zinc-900 text-white hover:bg-zinc-800 disabled:opacity-40" aria-label="Send">
                        <i class="fa-solid fa-arrow-up text-sm" aria-hidden="true"></i>
                    </button>
                </form>
                <p class="mt-2 text-[11px] text-zinc-400">Enter to send · Shift + Enter for a new line · Guidance only, not regulated financial advice.</p>
            </div>
        </section>

        {{-- Context panel --}}
        <aside class="space-y-6">
            <x-admin.card title="What the advisor sees" description="From your records, right now">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-xs text-zinc-500">Net worth</dt><dd class="mt-0.5 font-semibold tabular-nums text-zinc-900">{{ $context['net_worth'] }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Monthly spend</dt><dd class="mt-0.5 font-semibold tabular-nums text-zinc-900">{{ $context['monthly_burn'] }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Saving rate</dt><dd class="mt-0.5 font-semibold tabular-nums text-zinc-900">{{ $context['saving_rate'] }}</dd></div>
                    <div><dt class="text-xs text-zinc-500">Runway</dt><dd class="mt-0.5 font-semibold tabular-nums text-zinc-900">{{ $context['runway'] }}</dd></div>
                </dl>
            </x-admin.card>

            <x-admin.card title="Your goals">
                <x-slot name="action"><button type="button" @click="goalOpen = !goalOpen" class="text-xs font-medium text-indigo-600 hover:text-indigo-700" x-text="goalOpen ? 'Close' : 'Add goal'">Add goal</button></x-slot>
                <div x-show="goalOpen" x-cloak x-collapse class="mb-4">
                    <form @submit.prevent="saveGoal($event)" class="space-y-2.5">
                        <input name="title" required maxlength="100" placeholder="Goal name, e.g. Goa trip" class="h-9 w-full rounded-lg border border-zinc-200 px-3 text-sm focus:border-indigo-400 focus:outline-none">
                        <div class="grid grid-cols-2 gap-2">
                            <select name="category" required class="h-9 rounded-lg border border-zinc-200 bg-white px-2 text-sm focus:border-indigo-400 focus:outline-none">
                                @foreach($goalCategories as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                            </select>
                            <input name="target_amount" type="number" min="100" step="1" required placeholder="Target ₹" class="h-9 rounded-lg border border-zinc-200 px-3 text-sm focus:border-indigo-400 focus:outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input name="target_date" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" class="h-9 rounded-lg border border-zinc-200 px-2 text-sm focus:border-indigo-400 focus:outline-none" aria-label="Target date">
                            <select name="priority" class="h-9 rounded-lg border border-zinc-200 bg-white px-2 text-sm focus:border-indigo-400 focus:outline-none" aria-label="Priority">
                                <option value="medium">Medium priority</option><option value="high">High priority</option><option value="low">Low priority</option>
                            </select>
                        </div>
                        <button class="h-9 w-full rounded-lg bg-zinc-900 text-sm font-medium text-white hover:bg-zinc-800" :disabled="busy">Save goal</button>
                        <p class="text-xs" :class="formOk ? 'text-emerald-600' : 'text-rose-600'" x-text="formMsg"></p>
                    </form>
                </div>
                <ul class="space-y-3" x-ref="goals">
                    @forelse($activeGoals as $g)
                        @php $p = $g->target_amount > 0 ? min(100, round($g->current_amount / $g->target_amount * 100)) : 0; @endphp
                        <li>
                            <div class="flex justify-between gap-2 text-sm"><span class="truncate font-medium text-zinc-800">{{ $g->title }}</span><span class="shrink-0 tabular-nums text-zinc-500">₹{{ number_format($g->target_amount) }}</span></div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $p }}%"></div></div>
                        </li>
                    @empty
                        <li class="text-sm text-zinc-500" x-show="!newGoals.length">No goals yet.</li>
                    @endforelse
                    <template x-for="g in newGoals" :key="g.id">
                        <li><div class="flex justify-between gap-2 text-sm"><span class="truncate font-medium text-zinc-800" x-text="g.title"></span><span class="shrink-0 tabular-nums text-zinc-500" x-text="'₹' + Number(g.target_amount).toLocaleString('en-IN')"></span></div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-zinc-100"></div></li>
                    </template>
                </ul>
            </x-admin.card>

            <x-admin.card title="Risk profile" :description="$hasProfile ? 'Saved — the advisor uses this for investing questions.' : 'Not set yet. Helps the advisor tailor investing answers.'">
                <form @submit.prevent="saveProfile($event)" class="space-y-2.5">
                    <select name="risk_tolerance" required class="h-9 w-full rounded-lg border border-zinc-200 bg-white px-2 text-sm focus:border-indigo-400 focus:outline-none" aria-label="Risk tolerance">
                        <option value="">How much risk are you comfortable with?</option><option value="conservative">Low — protect my money</option><option value="moderate">Medium — some ups and downs</option><option value="aggressive">High — chase growth</option>
                    </select>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="investment_experience" required class="h-9 rounded-lg border border-zinc-200 bg-white px-2 text-sm focus:border-indigo-400 focus:outline-none" aria-label="Investing experience">
                            <option value="">Experience</option><option value="none">None</option><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option>
                        </select>
                        <select name="age_group" required class="h-9 rounded-lg border border-zinc-200 bg-white px-2 text-sm focus:border-indigo-400 focus:outline-none" aria-label="Age group">
                            <option value="">Age</option><option>18-25</option><option>26-35</option><option>36-45</option><option>46-55</option><option>55+</option>
                        </select>
                    </div>
                    <button class="h-9 w-full rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 hover:bg-zinc-50" :disabled="busy">{{ $hasProfile ? 'Update profile' : 'Save profile' }}</button>
                    <p class="text-xs" :class="profileOk ? 'text-emerald-600' : 'text-rose-600'" x-text="profileMsg"></p>
                </form>
            </x-admin.card>

            @if(!empty($marketData) && ($marketData['available'] ?? false))
                <x-admin.card title="Markets" :description="'In ₹ · updated ' . ($marketData['fetched_at'] ?? '')">
                    <ul class="space-y-2.5 text-sm">
                        @foreach(['btc' => 'Bitcoin', 'eth' => 'Ethereum', 'gold' => 'Gold'] as $k => $label)
                            @if(!empty($marketData[$k]))
                                <li class="flex justify-between"><span class="text-zinc-600">{{ $label }}</span><span class="tabular-nums text-zinc-900">{{ $marketData[$k]['price'] ?? '—' }}
                                    @isset($marketData[$k]['change'])<span class="ml-1 text-xs {{ $marketData[$k]['change'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $marketData[$k]['change'] >= 0 ? '+' : '' }}{{ $marketData[$k]['change'] }}%</span>@endisset</span></li>
                            @endif
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('styles')
<style>
    .advisor-md p { margin: 0 0 .6rem; } .advisor-md p:last-child { margin-bottom: 0; }
    .advisor-md ul, .advisor-md ol { margin: .4rem 0 .6rem 1.1rem; } .advisor-md ul { list-style: disc; } .advisor-md ol { list-style: decimal; }
    .advisor-md li { margin: .15rem 0; } .advisor-md strong { color: #18181b; font-weight: 600; }
    .advisor-md h1, .advisor-md h2, .advisor-md h3 { font-weight: 600; color: #18181b; margin: .5rem 0 .3rem; }
    .advisor-md code { background: #e4e4e7; padding: .05rem .3rem; border-radius: .25rem; font-size: .85em; }
    .advisor-md table { width: 100%; font-size: .85em; margin: .5rem 0; border-collapse: collapse; } .advisor-md th, .advisor-md td { border-bottom: 1px solid #e4e4e7; padding: .3rem .4rem; text-align: left; }
    .typing-dots span { display: inline-block; width: 5px; height: 5px; margin-right: 2px; border-radius: 9999px; background: #a1a1aa; animation: tdot 1.2s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: .15s } .typing-dots span:nth-child(3) { animation-delay: .3s }
    @keyframes tdot { 0%, 60%, 100% { opacity: .3; transform: none } 30% { opacity: 1; transform: translateY(-2px) } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('advisorChat', (cfg) => ({
        messages: cfg.history, used: cfg.used, limit: cfg.limit,
        draft: '', sending: false, busy: false,
        goalOpen: false, newGoals: [], formMsg: '', formOk: true, profileMsg: '', profileOk: true,
        get limitHit() { return this.used >= this.limit; },
        init() { this.$nextTick(() => this.scroll()); },
        scroll() { const f = this.$refs.feed; if (f) f.scrollTop = f.scrollHeight; },
        now() { return new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' }); },
        render(text) {
            const html = window.marked ? marked.parse(String(text ?? ''), { breaks: true }) : String(text ?? '').replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[c]);
            return window.DOMPurify ? DOMPurify.sanitize(html) : html;
        },
        async post(url, body) {
            const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf }, body: JSON.stringify(body) });
            const data = await r.json().catch(() => ({}));
            return { ok: r.ok, data };
        },
        async send(text) {
            text = (text || '').trim();
            if (!text || this.sending || this.limitHit) return;
            this.messages.push({ me: true, text, time: this.now() });
            this.draft = ''; this.sending = true;
            if (this.$refs.input) this.$refs.input.style.height = 'auto';
            this.$nextTick(() => this.scroll());
            try {
                const { ok, data } = await this.post(cfg.sendUrl, { message: text });
                const reply = data.reply || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Sorry — I couldn’t answer that just now. Please try again.');
                this.messages.push({ me: false, text: reply, time: this.now() });
                if (ok) this.used++;
            } catch (e) {
                this.messages.push({ me: false, text: 'I couldn’t reach the server. Check your connection and try again.', time: this.now() });
            } finally {
                this.sending = false;
                this.$nextTick(() => this.scroll());
            }
        },
        async saveGoal(e) {
            this.busy = true; this.formMsg = '';
            const body = Object.fromEntries(new FormData(e.target).entries());
            if (!body.target_date) delete body.target_date;
            const { ok, data } = await this.post(cfg.goalUrl, body).catch(() => ({ ok: false, data: {} }));
            this.formOk = ok;
            this.formMsg = ok ? 'Goal saved.' : (data.errors ? Object.values(data.errors).flat()[0] : 'Couldn’t save the goal.');
            if (ok && data.goal) { this.newGoals.push(data.goal); e.target.reset(); }
            this.busy = false;
        },
        async saveProfile(e) {
            this.busy = true; this.profileMsg = '';
            const body = Object.fromEntries(new FormData(e.target).entries());
            const { ok, data } = await this.post(cfg.profileUrl, body).catch(() => ({ ok: false, data: {} }));
            this.profileOk = ok;
            this.profileMsg = ok ? 'Profile saved.' : (data.errors ? Object.values(data.errors).flat()[0] : 'Couldn’t save your profile.');
            this.busy = false;
        },
    }));
});
</script>
@endpush
