@props(['title', 'description' => null, 'eyebrow' => 'Admin'])

<header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <p class="text-xs font-semibold text-indigo-600">{{ $eyebrow }}</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 sm:text-[28px]">{{ $title }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-zinc-500">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</header>
