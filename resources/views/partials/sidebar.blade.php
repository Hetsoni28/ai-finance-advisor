{{-- ======================================================================
     FinanceAI — simple sidebar
     Plain labels, real routes only. Mobile open/close is driven by the
     layout's `sidebarOpen`; desktop can collapse to icons.
     ====================================================================== --}}
@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    $unreadAlertCount = $user ? \App\Models\Alert::where('user_id', $user->id)->where('is_read', false)->count() : 0;

    $groups = [
        'Money' => [
            ['user.dashboard',        ['user.dashboard', 'dashboard'], 'fa-house',            'Dashboard'],
            ['user.incomes.index',    'user.incomes.*',                'fa-arrow-trend-up',   'Income'],
            ['user.expenses.index',   'user.expenses.*',               'fa-arrow-trend-down', 'Expenses'],
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
        $groups['Admin'] = [
            ['admin.dashboard',        'admin.dashboard*',  'fa-gauge',          'Overview'],
            ['admin.users.index',      'admin.users.*',     'fa-users',          'Users'],
            ['admin.contacts.index',   'admin.contacts.*',  'fa-envelope',       'Messages'],
            ['admin.activities.index', 'admin.activities.*','fa-list-check',     'Activity Log'],
            ['admin.reports.index',    'admin.reports.*',   'fa-file-lines',     'Admin Reports'],
        ];
    }

    $initials = 'U';
    if ($user) {
        $parts = array_values(array_filter(explode(' ', trim($user->name ?? ''))));
        $initials = strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1) : mb_substr($user->name ?? 'U', 0, 2));
    }
@endphp

<aside x-data="{ collapsed: false }"
       class="fixed lg:static inset-y-0 left-0 z-50 h-screen flex flex-col bg-white border-r border-slate-200 transition-all duration-300 no-print"
       :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'lg:w-[76px] w-64' : 'w-64']"
       aria-label="Sidebar">

    {{-- Logo --}}
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0" :class="collapsed && 'lg:justify-center lg:w-full'">
            <span class="h-9 w-9 shrink-0 rounded-[10px] flex items-center justify-center text-white bg-gradient-to-br from-indigo-500 to-violet-600 shadow-md shadow-indigo-500/25">
                <i class="fa-solid fa-cube" aria-hidden="true"></i>
            </span>
            <span class="text-lg font-black text-slate-900 tracking-tight" :class="collapsed && 'lg:hidden'">Finance<span class="text-indigo-600">AI</span></span>
        </a>
        {{-- Close (mobile) --}}
        <button type="button" @click="sidebarOpen = false" class="lg:hidden w-9 h-9 rounded-lg text-slate-500 hover:bg-slate-100 flex items-center justify-center" aria-label="Close menu">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Quick add --}}
    <div class="px-3 pt-4 shrink-0" :class="collapsed && 'lg:px-2'">
        <div class="grid grid-cols-2 gap-2" :class="collapsed && 'lg:grid-cols-1'">
            <a href="{{ route('user.incomes.create') }}" title="Add income"
               class="flex items-center justify-center gap-2 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 text-sm font-bold hover:bg-emerald-100 transition-colors">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i><span :class="collapsed && 'lg:hidden'">Income</span>
            </a>
            <a href="{{ route('user.expenses.create') }}" title="Add expense"
               class="flex items-center justify-center gap-2 h-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-100 text-sm font-bold hover:bg-rose-100 transition-colors">
                <i class="fa-solid fa-minus text-xs" aria-hidden="true"></i><span :class="collapsed && 'lg:hidden'">Expense</span>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" :class="collapsed && 'lg:px-2'" aria-label="Main">
        @foreach($groups as $groupName => $items)
            <div>
                <p class="px-3 mb-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-400" :class="collapsed && 'lg:hidden'">{{ $groupName }}</p>
                <div x-show="collapsed" class="hidden lg:block mx-auto mb-2 w-6 h-px bg-slate-200"></div>
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
                               class="relative flex items-center gap-3 h-10 px-3 rounded-xl text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 {{ $active ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                               :class="collapsed && 'lg:justify-center lg:px-0'">
                                @if($active)
                                    <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-indigo-600" aria-hidden="true"></span>
                                @endif
                                <i class="fa-solid {{ $icon }} w-5 text-center {{ $active ? 'text-indigo-600' : 'text-slate-400' }}" aria-hidden="true"></i>
                                <span class="flex-1 truncate" :class="collapsed && 'lg:hidden'">{{ $label }}</span>
                                @if($count > 0)
                                    <span class="min-w-[20px] h-5 px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-bold flex items-center justify-center" :class="collapsed && 'lg:absolute lg:top-1 lg:right-1 lg:min-w-[16px] lg:h-4 lg:text-[10px]'">{{ $count > 99 ? '99+' : $count }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- User + sign out --}}
    @if($user)
        <div class="border-t border-slate-100 p-3 shrink-0">
            <div class="flex items-center gap-3 rounded-xl p-2" :class="collapsed && 'lg:flex-col lg:p-1'">
                <a href="{{ route('user.profile.index') }}" title="Profile" class="h-9 w-9 shrink-0 rounded-full bg-slate-900 text-white text-xs font-bold flex items-center justify-center">{{ $initials }}</a>
                <div class="flex-1 min-w-0" :class="collapsed && 'lg:hidden'">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $user->current_plan_name ?? 'Starter' }} plan</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out" class="w-9 h-9 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-200" aria-label="Sign out">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            {{-- Collapse toggle (desktop) --}}
            <button type="button" @click="collapsed = !collapsed"
                    class="hidden lg:flex mt-2 w-full h-9 items-center justify-center gap-2 rounded-lg text-xs font-semibold text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-colors"
                    :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="fa-solid fa-angles-left transition-transform" :class="collapsed && 'rotate-180'" aria-hidden="true"></i>
                <span :class="collapsed && 'lg:hidden'">Collapse</span>
            </button>
        </div>
    @endif
</aside>
