@foreach(['success' => ['emerald', 'fa-circle-check'], 'error' => ['rose', 'fa-circle-exclamation']] as $key => [$tone, $icon])
    @if(session($key))
        <div x-data="{ show: true }" x-show="show" role="status"
             class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $tone === 'emerald' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' }}">
            <i class="fa-solid {{ $icon }} mt-0.5" aria-hidden="true"></i>
            <p class="flex-1">{{ session($key) }}</p>
            <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Dismiss"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
    @endif
@endforeach
