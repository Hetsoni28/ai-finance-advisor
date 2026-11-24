@props(['title', 'description' => null, 'eyebrow' => 'Admin', 'icon' => 'fa-shield-halved', 'stamp' => true])

<header class="flex flex-col gap-4 border-b border-zinc-200/80 pb-5 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-zinc-900 px-1.5 py-0.5 text-[11px] font-semibold text-white">
                <i class="fa-solid {{ $icon }} text-[9px]" aria-hidden="true"></i>{{ $eyebrow }}
            </span>
            <span class="text-zinc-300" aria-hidden="true">/</span>
            <span>{{ $title }}</span>
        </div>
        <h1 class="mt-3 text-[28px] font-bold leading-tight tracking-tight text-zinc-950">{{ $title }}</h1>
        @if($description)
            <p class="mt-1 max-w-2xl text-sm text-zinc-500">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @if($stamp)<span class="hidden items-center gap-1.5 text-xs text-zinc-400 lg:inline-flex" title="Page generated at {{ now()->format('d M Y, h:i:s A') }}">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>Data as of {{ now()->format('h:i A') }}
        </span>@endif
        {{ $actions ?? '' }}
    </div>
</header>
