@php
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Navigation Structure (Self-contained – no external dependency)
    |--------------------------------------------------------------------------
    */

    $menu = [

        [
            'section' => null,
            'items' => [
                ['route'=>'user.dashboard','match'=>'user.dashboard*','icon'=>'fa-chart-line','label'=>'Dashboard'],
                ['route'=>'user.incomes.index','match'=>'user.incomes.*','icon'=>'fa-wallet','label'=>'Income'],
                ['route'=>'user.expenses.index','match'=>'user.expenses.*','icon'=>'fa-receipt','label'=>'Expenses'],
                ['route'=>'user.families.index','match'=>'user.families.*','icon'=>'fa-people-group','label'=>'Family Budget'],
                [
                    'route'=>'user.reports.index',
                    'match'=>['user.reports.*','reports.*'],
                    'icon'=>'fa-chart-pie',
                    'label'=>'Reports'
                ],
                ['route'=>'user.notifications.index','match'=>'user.notifications.*','icon'=>'fa-bell','label'=>'Notifications'],
                ['route'=>'user.ai.chat','match'=>'user.ai.*','icon'=>'fa-robot','label'=>'AI Assistant'],
            ],
        ],

        [
            'section'=>'Account',
            'items'=>[
                ['route'=>'user.profile.index','match'=>'user.profile.*','icon'=>'fa-user','label'=>'Profile'],
                ['route'=>'user.profile.password.form','match'=>'user.profile.password.*','icon'=>'fa-lock','label'=>'Change Password'],
                ['route'=>'user.profile.subscription','match'=>'user.profile.subscription*','icon'=>'fa-credit-card','label'=>'Subscription'],
            ],
        ],
    ];

    // Admin Section
    if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
        $menu[] = [
            'section'=>'Admin',
            'items'=>[
                ['route'=>'admin.dashboard','match'=>'admin.dashboard*','icon'=>'fa-shield-halved','label'=>'Admin Dashboard'],
                ['route'=>'admin.users.index','match'=>'admin.users.*','icon'=>'fa-users','label'=>'Users'],
                ['route'=>'admin.activities.index','match'=>'admin.activities.*','icon'=>'fa-clipboard-list','label'=>'Activities'],
            ],
        ];
    }
@endphp

{{-- ================= OVERLAY ================= --}}
<div x-show="sidebarOpen"
     x-transition.opacity
     class="fixed inset-0 bg-black/60 backdrop-blur-md z-40 lg:hidden"
     @click="sidebarOpen = false"
     aria-hidden="true">
</div>


{{-- ================= SIDEBAR ================= --}}
<aside 
    x-data="{ 
        accountOpen: true,
        collapsed: false
    }"
    class="fixed lg:static inset-y-0 left-0
           bg-white/90 dark:bg-slate-900/90
           backdrop-blur-xl
           border-r border-slate-200 dark:border-slate-800
           shadow-2xl lg:shadow-none
           transform transition-all duration-300 ease-in-out
           z-50 flex flex-col"

    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        collapsed ? 'w-20' : 'w-72'
    ]">

    {{-- HEADER --}}
    <div class="px-4 py-5 border-b border-slate-200 dark:border-slate-800
                flex items-center justify-between">

        <div class="flex items-center gap-3">

            <div class="h-10 w-10 rounded-xl
                        bg-gradient-to-br from-indigo-600 to-blue-600
                        flex items-center justify-center
                        text-white font-bold shadow-lg">
                FA
            </div>

            <span x-show="!collapsed"
                  x-transition
                  class="font-bold text-lg text-slate-800 dark:text-white">
                FinanceAI
            </span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Collapse Toggle --}}
            <button @click="collapsed = !collapsed"
                    class="hidden lg:block text-slate-400 hover:text-indigo-500">
                <i class="fa-solid"
                   :class="collapsed ? 'fa-angle-right' : 'fa-angle-left'"></i>
            </button>

            {{-- Mobile Close --}}
            <button class="lg:hidden"
                    @click="sidebarOpen = false">
                <i class="fa-solid fa-xmark text-lg text-slate-500"></i>
            </button>
        </div>

    </div>


    {{-- NAVIGATION --}}
    <div class="flex-1 overflow-y-auto px-3 py-6 space-y-8 relative">

        @foreach($menu as $group)

            @if($group['section'])
                <p x-show="!collapsed"
                   class="px-3 text-[10px] uppercase tracking-widest text-slate-400 font-semibold">
                    {{ $group['section'] }}
                </p>
            @endif

            <div class="space-y-1">

                @foreach($group['items'] as $item)

                    @php
                        $match = $item['match'];
                        $active = is_array($match)
                            ? collect($match)->contains(fn($m)=>request()->routeIs($m))
                            : request()->routeIs($match);

                        $url = Route::has($item['route'])
                            ? route($item['route'])
                            : '#';
                    @endphp

                    <a href="{{ $url }}"
                       class="relative flex items-center gap-3
                              px-3 py-3 rounded-xl text-sm font-medium
                              transition-all duration-200 group
                              {{ $active
                                  ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                                  : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                              }}">

                        {{-- Active Bar --}}
                        <span class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full
                                     {{ $active ? 'bg-indigo-600' : '' }}">
                        </span>

                        <i class="fa-solid {{ $item['icon'] }}
                                  w-5 text-center
                                  {{ !$active ? 'group-hover:scale-110 transition' : '' }}">
                        </i>

                        {{-- Label --}}
                        <span x-show="!collapsed" x-transition>
                            {{ $item['label'] }}
                        </span>

                        {{-- Tooltip when collapsed --}}
                        <span x-show="collapsed"
                              class="absolute left-16 bg-slate-800 text-white
                                     text-xs px-2 py-1 rounded-md opacity-0
                                     group-hover:opacity-100 transition pointer-events-none">
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endforeach

            </div>

        @endforeach

    </div>


    {{-- PROFILE FOOTER --}}
    @if($user)
    <div class="px-4 py-4 border-t border-slate-200 dark:border-slate-800">

        <div class="flex items-center gap-3">

            <div class="relative">
                <div class="h-9 w-9 rounded-lg
                            bg-gradient-to-br from-indigo-500 to-blue-600
                            flex items-center justify-center
                            text-white font-bold">
                    {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
                </div>

                {{-- Online dot --}}
                <span class="absolute -bottom-1 -right-1 h-3 w-3
                             bg-emerald-500 border-2 border-white
                             rounded-full"></span>
            </div>

            <div x-show="!collapsed" class="min-w-0">
                <p class="text-sm font-semibold truncate">
                    {{ $user->name ?? 'User' }}
                </p>
                <p class="text-xs text-slate-400">
                    {{ ucfirst($user->role ?? 'member') }}
                </p>
            </div>

        </div>

    </div>
    @endif

</aside>