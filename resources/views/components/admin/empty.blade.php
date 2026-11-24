@props(['icon' => 'fa-inbox', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-400">
        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    </span>
    <p class="mt-3 text-sm font-semibold text-zinc-900">{{ $title }}</p>
    @if($text)<p class="mt-1 max-w-sm text-sm text-zinc-500">{{ $text }}</p>@endif
    {{ $slot }}
</div>
