@props(['title' => null, 'description' => null, 'flush' => false])

<section {{ $attributes->merge(['class' => 'min-w-0 rounded-xl border border-zinc-200 bg-white']) }}>
    @if($title || isset($action))
        <div class="flex items-start justify-between gap-4 border-b border-zinc-100 px-4 py-3.5 sm:px-5">
            <div class="min-w-0">
                @if($title)<h2 class="text-sm font-semibold text-zinc-900">{{ $title }}</h2>@endif
                @if($description)<p class="mt-0.5 text-xs text-zinc-500">{{ $description }}</p>@endif
            </div>
            @isset($action)<div class="shrink-0">{{ $action }}</div>@endisset
        </div>
    @endif
    <div class="{{ $flush ? '' : 'p-4 sm:p-5' }}">{{ $slot }}</div>
</section>
