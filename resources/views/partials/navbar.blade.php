{{-- Top bar for signed-in pages: breadcrumbs, search / AI, quick add, notifications and account menu. --}}
@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

    // Unread count comes from the view composer in AppServiceProvider (one query per request).
    $unreadAlerts = (int) ($unreadAlerts ?? 0);
    $recentAlerts = ($user && $unreadAlerts > 0)
        ? \App\Models\Alert::where('user_id', $user->id)->where('is_read', false)->latest()->limit(5)->get()
        : collect();

    $planSlug = $user->current_plan_slug ?? 'free';
    $planName = $user->current_plan_name ?? 'Starter';
    $planTone = ['premium' => 'bg-amber-50 text-amber-700 ring-amber-200', 'pro' => 'bg-indigo-50 text-indigo-700 ring-indigo-200'][$planSlug] ?? 'bg-zinc-100 text-zinc-600 ring-zinc-200';

    // Breadcrumbs: My money › Section [› Action], or Admin › Page
    $currentRoute = Route::currentRouteName() ?? '';
    $sections = [
        'user.dashboard' => ['Dashboard', 'user.dashboard'], 'user.incomes' => ['Income', 'user.incomes.index'],
        'user.expenses' => ['Expenses', 'user.expenses.index'], 'user.reports' => ['Reports', 'user.reports.index'],
        'user.ai' => ['AI Advisor', 'user.ai.chat'], 'user.families' => ['Family', 'user.families.index'],
        'user.notifications' => ['Notifications', 'user.notifications.index'], 'user.profile' => ['Profile', 'user.profile.index'],
        'user.subscription' => ['Plan & billing', 'user.subscription.index'],
    ];
    $actionLabels = ['create' => 'New', 'edit' => 'Edit', 'show' => 'Details', 'password' => 'Password', 'form' => 'Password', 'success' => 'Done', 'invites' => 'Invites', 'access' => 'Invites'];

    $workspace = isset($family) && $family ? $family->name : 'Personal';
    $breadcrumbs = ['My money' => route('dashboard')];
    foreach ($sections as $prefix => [$label, $routeName]) {
        if (str_starts_with($currentRoute, $prefix)) {
            $breadcrumbs[$label] = route($routeName);
            $action = \Illuminate\Support\Str::afterLast($currentRoute, '.');
            if (isset($actionLabels[$action])) { $breadcrumbs[$actionLabels[$action]] = null; }
            break;
        }
    }
    if (str_starts_with($currentRoute, 'admin.')) {
        $workspace = 'Admin';
        $adminPages = ['admin.dashboard' => 'Overview', 'admin.users' => 'Users', 'admin.contacts' => 'Messages', 'admin.activities' => 'Activity log', 'admin.reports' => 'Reports'];
        $pageName = 'Overview';
        foreach ($adminPages as $prefix => $name) { if (str_starts_with($currentRoute, $prefix)) { $pageName = $name; break; } }
        $breadcrumbs = ['Admin' => route('admin.dashboard'), $pageName => null];
        if (str_ends_with($currentRoute, '.show')) { $breadcrumbs[$pageName] = route(\Illuminate\Support\Str::beforeLast($currentRoute, '.') . '.index'); $breadcrumbs['Details'] = null; }
    }
    $onAdmin = $workspace === 'Admin';
@endphp

<header class="sticky top-0 z-[45] w-full no-print border-b transition-colors duration-200"
        :class="isScrolled ? 'bg-white/90 backdrop-blur-xl border-zinc-200' : 'bg-[#f8fafc]/80 backdrop-blur border-transparent'">
    <div class="mx-auto flex h-16 max-w-[1800px] items-center gap-3 px-4 sm:px-6 lg:px-8">

        {{-- Mobile: menu + logo --}}
        <button type="button" @click="sidebarOpen = true" aria-label="Open menu"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 lg:hidden focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">
            <i class="fa-solid fa-bars text-sm" aria-hidden="true"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 text-white lg:hidden" aria-label="FinanceAI home">
            <i class="fa-solid fa-cube text-sm" aria-hidden="true"></i>
        </a>

        {{-- Breadcrumbs --}}
        <nav class="hidden min-w-0 items-center gap-2 text-sm lg:flex" aria-label="Breadcrumb">
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $onAdmin ? 'bg-zinc-900 text-white ring-zinc-900' : 'bg-white text-zinc-600 ring-zinc-200' }}">
                <i class="fa-solid {{ $onAdmin ? 'fa-shield-halved' : 'fa-wallet' }} text-[10px] {{ $onAdmin ? 'text-indigo-300' : 'text-zinc-400' }}" aria-hidden="true"></i>{{ $workspace }}
            </span>
            <ol class="flex min-w-0 items-center gap-1.5">
                @foreach($breadcrumbs as $label => $url)
                    @if(!$loop->first)<li aria-hidden="true" class="text-zinc-300"><i class="fa-solid fa-chevron-right text-[9px]"></i></li>@endif
                    <li class="truncate">
                        @if($url && !$loop->last)
                            <a href="{{ $url }}" class="text-zinc-500 hover:text-zinc-900">{{ $label }}</a>
                        @else
                            <span class="font-medium text-zinc-900" @if($loop->last) aria-current="page" @endif>{{ $label }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        {{-- Search / command palette --}}
        <div class="flex flex-1 justify-center px-2 lg:px-6">
            <button type="button" @click="toggleCommandPalette()"
                    class="group hidden h-9 w-full max-w-md items-center gap-2.5 rounded-lg border border-zinc-200 bg-white px-3 text-left text-sm text-zinc-400 shadow-[0_1px_2px_rgba(24,24,27,.04)] hover:border-zinc-300 md:flex focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">
                <i class="fa-solid fa-magnifying-glass text-xs" aria-hidden="true"></i>
                <span class="flex-1 truncate">Search pages or ask the AI…</span>
                <kbd class="hidden rounded border border-zinc-200 bg-zinc-50 px-1.5 py-0.5 font-sans text-[10px] font-medium text-zinc-500 sm:inline">⌘K</kbd>
            </button>
        </div>

        @auth
        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">

            {{-- Ask AI --}}
            <a href="{{ route('user.ai.chat') }}"
               class="inline-flex h-9 items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100 sm:px-3 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100"
               title="Ask the AI advisor">
                <i class="fa-solid fa-wand-magic-sparkles text-xs" aria-hidden="true"></i><span class="hidden xl:inline">Ask AI</span>
            </a>

            {{-- New entry --}}
            <div class="relative hidden sm:block" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu"
                        class="inline-flex h-9 items-center gap-2 rounded-lg bg-zinc-900 px-3 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-zinc-300">
                    <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i> New
                    <i class="fa-solid fa-chevron-down text-[9px] text-zinc-400 transition-transform" :class="open && 'rotate-180'" aria-hidden="true"></i>
                </button>
                <div x-show="open" x-cloak x-transition.opacity.duration.150ms role="menu"
                     class="absolute right-0 top-full z-[100] mt-2 w-64 rounded-xl border border-zinc-200 bg-white p-1.5 shadow-[0_16px_40px_-12px_rgba(24,24,27,.25)]">
                    @foreach([
                        [route('user.incomes.create'), 'fa-arrow-trend-up', 'bg-emerald-50 text-emerald-600', 'Income', 'Money you received'],
                        [route('user.expenses.create'), 'fa-arrow-trend-down', 'bg-rose-50 text-rose-600', 'Expense', 'Money you spent'],
                    ] as [$href, $icon, $tone, $label, $hint])
                        <a href="{{ $href }}" role="menuitem" class="flex items-center gap-3 rounded-lg px-2.5 py-2 hover:bg-zinc-50 focus:bg-zinc-50 focus:outline-none">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $tone }}"><i class="fa-solid {{ $icon }} text-xs" aria-hidden="true"></i></span>
                            <span class="min-w-0"><span class="block text-sm font-medium text-zinc-900">{{ $label }}</span><span class="block truncate text-xs text-zinc-500">{{ $hint }}</span></span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Notifications --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                        aria-label="Notifications{{ $unreadAlerts ? ' (' . $unreadAlerts . ' unread)' : '' }}"
                        class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">
                    <i class="fa-regular fa-bell text-sm" aria-hidden="true"></i>
                    @if($unreadAlerts > 0)
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold text-white ring-2 ring-white">{{ $unreadAlerts > 9 ? '9+' : $unreadAlerts }}</span>
                    @endif
                </button>
                <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                     class="absolute right-0 top-full z-[100] mt-2 w-[22rem] max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-[0_16px_40px_-12px_rgba(24,24,27,.25)]">
                    <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
                        <p class="text-sm font-semibold text-zinc-900">Notifications</p>
                        @if($unreadAlerts > 0)<span class="text-xs text-zinc-500">{{ $unreadAlerts }} unread</span>@endif
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        @forelse($recentAlerts as $alert)
                            <a href="{{ route('user.notifications.index') }}" class="flex gap-3 border-b border-zinc-50 px-4 py-3 hover:bg-zinc-50">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-indigo-500" aria-hidden="true"></span>
                                <span class="min-w-0">
                                    <span class="line-clamp-2 block text-sm text-zinc-800">{{ $alert->message }}</span>
                                    <span class="mt-0.5 block text-xs text-zinc-400">{{ $alert->created_at?->diffForHumans() }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <i class="fa-regular fa-bell-slash text-lg text-zinc-300" aria-hidden="true"></i>
                                <p class="mt-2 text-sm font-medium text-zinc-700">You're all caught up</p>
                                <p class="mt-0.5 text-xs text-zinc-500">AI spending insights appear on the notifications page.</p>
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('user.notifications.index') }}" class="block border-t border-zinc-100 px-4 py-2.5 text-center text-sm font-medium text-indigo-600 hover:bg-zinc-50">View all notifications</a>
                </div>
            </div>

            {{-- Account --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" aria-label="Account menu"
                        class="flex items-center gap-2.5 rounded-lg py-1 pl-1 pr-1 hover:bg-white sm:pr-2 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100">
                    <x-admin.avatar :name="$user->name" />
                    <span class="hidden text-left leading-tight xl:block">
                        <span class="block max-w-[10rem] truncate text-sm font-medium text-zinc-900">{{ $user->name }}</span>
                        <span class="block text-xs text-zinc-500">{{ $isAdmin ? 'Admin · ' : '' }}{{ $planName }} plan</span>
                    </span>
                    <i class="fa-solid fa-chevron-down hidden text-[9px] text-zinc-400 sm:block" aria-hidden="true"></i>
                </button>
                <div x-show="open" x-cloak x-transition.opacity.duration.150ms role="menu"
                     class="absolute right-0 top-full z-[100] mt-2 w-72 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-[0_16px_40px_-12px_rgba(24,24,27,.25)]">
                    <div class="flex items-center gap-3 border-b border-zinc-100 px-4 py-3.5">
                        <x-admin.avatar :name="$user->name" size="lg" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-zinc-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-zinc-500">{{ $user->email }}</p>
                            <span class="mt-1 inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $planTone }}">{{ $planName }} plan</span>
                        </div>
                    </div>
                    <div class="p-1.5">
                        @foreach(array_filter([
                            [route('user.profile.index'), 'fa-user', 'Profile'],
                            [route('user.subscription.index'), 'fa-credit-card', 'Plan & billing'],
                            [route('user.profile.password.form'), 'fa-key', 'Change password'],
                            $isAdmin ? ($onAdmin ? [route('dashboard'), 'fa-wallet', 'My money'] : [route('admin.dashboard'), 'fa-shield-halved', 'Admin panel']) : null,
                        ]) as [$href, $icon, $label])
                            <a href="{{ $href }}" role="menuitem" class="flex items-center gap-3 rounded-lg px-2.5 py-2 text-sm text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900 focus:bg-zinc-50 focus:outline-none">
                                <i class="fa-solid {{ $icon }} w-4 text-center text-xs text-zinc-400" aria-hidden="true"></i>{{ $label }}
                            </a>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-zinc-100 p-1.5">
                        @csrf
                        <button type="submit" role="menuitem" class="flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-sm text-rose-600 hover:bg-rose-50 focus:bg-rose-50 focus:outline-none">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-xs" aria-hidden="true"></i>Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>
