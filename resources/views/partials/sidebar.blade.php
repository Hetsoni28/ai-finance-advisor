{{-- ======================================================================
     FinanceAI — sidebar
     Black panel (matches the website's dark sections). Plain labels,
     real routes, and a "This month" card built from the user's own records.
     Mobile open/close is driven by the layout's `sidebarOpen`; desktop can
     collapse to icons.
     ====================================================================== --}}
@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    $unreadAlertCount = (int) ($unreadAlerts ?? 0); // from the view composer (one query per request)

    // This month's real totals
    $monthIn = 0; $monthOut = 0;
    if ($user) {
        $monthStart = now()->startOfMonth();
        $monthIn  = (float) \App\Models\Income::where('user_id', $user->id)->where('income_date', '>=', $monthStart)->sum('amount');
        $monthOut = (float) \App\Models\Expense::where('user_id', $user->id)->where('expense_date', '>=', $monthStart)->sum('amount');
    }
    $monthKept   = $monthIn - $monthOut;
    $spentShare  = $monthIn > 0 ? min(100, round($monthOut / $monthIn * 100)) : ($monthOut > 0 ? 100 : 0);
    $inr = fn ($v) => '₹' . inr_number(abs($v), 0);

    $groups = [
        'Money' => [
            ['user.dashboard',        ['user.dashboard', 'dashboard'], 'fa-house',            'Dashboard'],
            ['user.incomes.index',    'user.incomes.*',                'fa-arrow-trend-up',   'Income'],
            ['user.expenses.index',   'user.expenses.*',               'fa-arrow-trend-down', 'Expenses'],
            ['user.budgets.index',    'user.budgets.*',                'fa-gauge-high',       'Budgets'],
            ['user.reports.index',    'user.reports.*',                'fa-chart-pie',        'Reports'],
            ['user.ai.chat',          'user.ai.*',                     'fa-robot',            'AI Advisor'],
        ],
        'Family' => [
            ['user.families.index',   'user.families.*',               'fa-people-roof',      'Family'],
        ],
        'Account' => [
            ['user.notifications.index', 'user.notifications.*',       'fa-bell',             'Notifications', $unreadAlertCount],
            ['user.profile.index',    ['user.profile.index', 'user.profile.edit', 'user.profile.password.*'], 'fa-user', 'Profile'],
            ['user.subscription.index', ['user.subscription.*', 'user.profile.subscription'], 'fa-credit-card', 'Plan & Billing'],
        ],
    ];

    if ($isAdmin) {
        // Admins: admin tools first, and make the personal dashboard clearly "My Dashboard"
        $groups['Money'][0][3] = 'My Dashboard';
        $groups = ['Admin' => [
            ['admin.dashboard',        'admin.dashboard*',   'fa-gauge',      'Overview'],
            ['admin.users.index',      'admin.users.*',      'fa-users',      'Users'],
            ['admin.contacts.index',   'admin.contacts.*',   'fa-envelope',   'Messages'],
            ['admin.activities.index', 'admin.activities.*', 'fa-list-check', 'Activity Log'],
            ['admin.reports.index',    'admin.reports.*',    'fa-file-lines', 'Admin Reports'],
        ]] + $groups;
    }

    $initials = 'U';
    if ($user) {
        $parts = array_values(array_filter(explode(' ', trim($user->name ?? ''))));
        $initials = strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1) : mb_substr($user->name ?? 'U', 0, 2));
    }
    $planSlug = $user->current_plan_slug ?? 'free';
@endphp

<aside x-data="{ collapsed: false }"
       class="sb fixed lg:relative inset-y-0 left-0 z-50 h-screen flex flex-col bg-black text-zinc-300 border-r border-white/5 transition-[width,transform] duration-300 no-print"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'lg:w-[76px] w-[17rem]' : 'w-[17rem]']"
       aria-label="Sidebar">

    {{-- Logo --}}
    <div class="relative h-16 flex items-center justify-between px-4 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400" :class="collapsed && 'lg:justify-center lg:w-full'">
            <span class="h-9 w-9 shrink-0 rounded-[10px] flex items-center justify-center text-white bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-900/60 ring-1 ring-white/10">
                <i class="fa-solid fa-cube" aria-hidden="true"></i>
            </span>
            <span class="text-lg font-black text-white tracking-tight" :class="collapsed && 'lg:hidden'">Finance<span class="text-indigo-400">AI</span></span>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden w-9 h-9 rounded-lg text-zinc-400 hover:text-white hover:bg-white/10 flex items-center justify-center" aria-label="Close menu">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Search (opens the real command palette) + quick add --}}
    <div class="relative px-3 space-y-2.5 shrink-0" :class="collapsed && 'lg:px-2'">
        <button type="button" @click="if (typeof toggleCommandPalette === 'function') toggleCommandPalette()" title="Search (Ctrl/⌘ K)"
                class="w-full h-10 flex items-center gap-3 px-3 rounded-xl bg-white/[0.04] border border-white/[0.07] text-zinc-400 hover:text-white hover:bg-white/[0.07] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                :class="collapsed && 'lg:justify-center lg:px-0'">
            <i class="fa-solid fa-magnifying-glass text-sm" aria-hidden="true"></i>
            <span class="flex-1 text-left text-sm" :class="collapsed && 'lg:hidden'">Search</span>
            <kbd class="font-mono text-[11px] text-zinc-500 border border-white/10 rounded-md px-1.5 py-0.5" :class="collapsed && 'lg:hidden'">⌘K</kbd>
        </button>

        <div class="flex rounded-xl overflow-hidden ring-1 ring-white/[0.07]" :class="collapsed && 'lg:flex-col'">
            <a href="{{ route('user.incomes.create') }}" title="Add income"
               class="flex-1 h-10 flex items-center justify-center gap-2 text-sm font-semibold text-emerald-300 bg-emerald-400/[0.08] hover:bg-emerald-400/[0.16] transition-colors">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i><span :class="collapsed && 'lg:hidden'">Income</span>
            </a>
            <span class="w-px bg-white/[0.07]" :class="collapsed && 'lg:w-auto lg:h-px'" aria-hidden="true"></span>
            <a href="{{ route('user.expenses.create') }}" title="Add expense"
               class="flex-1 h-10 flex items-center justify-center gap-2 text-sm font-semibold text-rose-300 bg-rose-400/[0.08] hover:bg-rose-400/[0.16] transition-colors">
                <i class="fa-solid fa-minus text-xs" aria-hidden="true"></i><span :class="collapsed && 'lg:hidden'">Expense</span>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sb-scroll relative flex-1 overflow-y-auto px-3 pt-5 pb-4 space-y-5" :class="collapsed && 'lg:px-2'" aria-label="Main">
        @foreach($groups as $groupName => $items)
            <div>
                <p class="px-3 mb-1.5 font-mono text-[10.5px] uppercase tracking-[0.18em] text-zinc-500" :class="collapsed && 'lg:hidden'">{{ $groupName }}</p>
                <div x-show="collapsed" class="hidden lg:block mx-auto mb-2 w-5 h-px bg-white/10"></div>
                <ul class="space-y-0.5">
                    @foreach($items as $item)
                        @php
                            [$routeName, $match, $icon, $label] = $item;
                            $count = $item[4] ?? 0;
                            if (!Route::has($routeName)) continue;
                            $active = request()->routeIs((array) $match);
                        @endphp
                        <li>
                            <a href="{{ route($routeName) }}"
                               title="{{ $label }}"
                               @click="if (window.innerWidth < 1024) sidebarOpen = false"
                               @if($active) aria-current="page" @endif
                               class="sb-link group relative flex items-center gap-3 h-10 px-3 rounded-xl text-[14px] font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 {{ $active ? 'bg-white/[0.08] text-white' : 'text-zinc-400 hover:text-white hover:bg-white/[0.04]' }}"
                               :class="collapsed && 'lg:justify-center lg:px-0'">
                                @if($active)
                                    <span class="absolute -left-3 top-1/2 -translate-y-1/2 h-6 w-[3px] rounded-r-full bg-gradient-to-b from-indigo-400 to-violet-500 shadow-[0_0_12px_rgba(129,140,248,.8)]" :class="collapsed && 'lg:-left-2'" aria-hidden="true"></span>
                                @endif
                                <i class="fa-solid {{ $icon }} w-5 text-center text-[15px] transition-transform group-hover:scale-110 {{ $active ? 'text-indigo-300' : 'text-zinc-500 group-hover:text-zinc-300' }}" aria-hidden="true"></i>
                                <span class="flex-1 truncate" :class="collapsed && 'lg:hidden'">{{ $label }}</span>
                                @if($count > 0)
                                    <span class="min-w-[20px] h-5 px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-bold flex items-center justify-center shadow-[0_0_0_3px_#000]"
                                          :class="collapsed && 'lg:absolute lg:top-1 lg:right-2 lg:min-w-[16px] lg:h-4 lg:text-[10px] lg:px-1'">{{ $count > 99 ? '99+' : $count }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        {{-- This month (real totals from the user's records) --}}
        @if($user)
            <a href="{{ route('user.reports.index') }}" class="block rounded-2xl border border-white/[0.07] bg-gradient-to-b from-white/[0.05] to-white/[0.02] p-4 hover:border-indigo-400/30 transition-colors" :class="collapsed && 'lg:hidden'">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-[10.5px] uppercase tracking-[0.18em] text-zinc-500">{{ now()->format('F') }}</span>
                    <i class="fa-solid fa-arrow-right text-[11px] text-zinc-500" aria-hidden="true"></i>
                </div>
                <p class="mt-2 text-xl font-black tabular-nums {{ $monthKept >= 0 ? 'text-white' : 'text-rose-300' }}">
                    {{ $monthKept < 0 ? '−' : '' }}{{ $inr($monthKept) }}
                    <span class="text-xs font-semibold text-zinc-500">{{ $monthKept >= 0 ? 'kept' : 'over' }}</span>
                </p>
                <div class="mt-3 h-1.5 rounded-full bg-white/10 overflow-hidden" role="img" aria-label="{{ $spentShare }}% of this month's income spent">
                    <div class="h-full rounded-full {{ $spentShare >= 90 ? 'bg-rose-400' : 'bg-gradient-to-r from-indigo-400 to-violet-400' }}" style="width: {{ $spentShare }}%"></div>
                </div>
                <div class="mt-2.5 flex justify-between text-[11px] tabular-nums">
                    <span class="text-emerald-300/90">In {{ $inr($monthIn) }}</span>
                    <span class="text-rose-300/90">Out {{ $inr($monthOut) }}</span>
                </div>
            </a>
        @endif
    </nav>

    {{-- User + sign out --}}
    @if($user)
        <div class="relative border-t border-white/[0.06] p-3 shrink-0">
            <div class="flex items-center gap-3 rounded-xl p-1.5" :class="collapsed && 'lg:flex-col lg:p-0 lg:gap-2'">
                <a href="{{ route('user.profile.index') }}" title="Profile" class="relative h-10 w-10 shrink-0 rounded-full p-[2px] bg-gradient-to-br from-indigo-400 to-violet-500">
                    <span class="h-full w-full rounded-full bg-black text-white text-xs font-bold flex items-center justify-center">{{ $initials }}</span>
                </a>
                <div class="flex-1 min-w-0" :class="collapsed && 'lg:hidden'">
                    <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                    <a href="{{ route('user.subscription.index') }}" class="mt-0.5 inline-flex items-center gap-1 text-[11px] font-semibold {{ $planSlug === 'free' ? 'text-zinc-400 hover:text-white' : 'text-indigo-300 hover:text-indigo-200' }}">
                        @if($planSlug !== 'free')<i class="fa-solid fa-bolt text-[10px]" aria-hidden="true"></i>@endif
                        {{ $user->current_plan_name ?? 'Starter' }} plan
                    </a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out" class="w-9 h-9 rounded-lg text-zinc-500 hover:text-rose-300 hover:bg-rose-500/10 flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400" aria-label="Sign out">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <button type="button" @click="collapsed = !collapsed"
                    class="hidden lg:flex mt-2 w-full h-8 items-center justify-center gap-2 rounded-lg text-xs font-medium text-zinc-500 hover:text-white hover:bg-white/[0.04] transition-colors"
                    :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="fa-solid fa-angles-left transition-transform" :class="collapsed && 'rotate-180'" aria-hidden="true"></i>
                <span :class="collapsed && 'lg:hidden'">Collapse</span>
            </button>
        </div>
    @endif
</aside>

@once
<style>
    /* Thin, quiet scrollbar inside the dark sidebar */
    .sb-scroll { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.12) transparent; }
    .sb-scroll::-webkit-scrollbar { width: 6px; }
    .sb-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 9999px; }
    @media (prefers-reduced-motion: reduce) { .sb, .sb-link i { transition: none !important; } }
</style>
@endonce
