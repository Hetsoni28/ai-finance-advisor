{{--
    Shared add/edit form for income and expenses.
    $cfg = [type, mode (create|edit), record (or null), action, families, recent, routes => [index, destroy?]]
    Field names and rules match IncomeController / ExpenseController exactly.
--}}
@php
    $isIncome = $cfg['type'] === 'income';
    $isEdit   = $cfg['mode'] === 'edit';
    $r        = $cfg['record'] ?? null;
    $titleField = $isIncome ? 'source' : 'title';
    $dateField  = $isIncome ? 'income_date' : 'expense_date';
    $today = now()->format('Y-m-d');
    $categories = $isIncome
        ? ['Salary' => 'fa-briefcase', 'Freelance' => 'fa-laptop-code', 'Investments' => 'fa-chart-line', 'Business' => 'fa-store', 'Gifts' => 'fa-gift', 'Others' => 'fa-ellipsis']
        : ['Food' => 'fa-utensils', 'Travel' => 'fa-plane', 'Bills' => 'fa-file-invoice', 'Shopping' => 'fa-bag-shopping', 'Health' => 'fa-heart-pulse', 'Others' => 'fa-ellipsis'];
    $currentCategory = old('category', $r->category ?? '');
    // Keep a legacy category selectable if a record uses one not in the list
    if ($currentCategory && !array_key_exists($currentCategory, $categories)) { $categories[$currentCategory] = 'fa-tag'; }
    $dateValue = old($dateField, $r ? \Carbon\Carbon::parse($r->{$dateField})->format('Y-m-d') : $today);
    $families = $cfg['families'] ?? collect();
    $accent = $isIncome ? 'emerald' : 'rose';
    $noun = $isIncome ? 'income' : 'expense';
    $inr = fn ($v) => '₹' . number_format((float) $v, 0);
@endphp

<div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <x-admin.page-header :title="($isEdit ? 'Edit ' : 'Add ') . $noun" eyebrow="My money" icon="fa-wallet" :stamp="false"
        :description="$isEdit ? 'Change the details and save.' : ($isIncome ? 'Record money you received.' : 'Record money you spent.')">
        <x-slot name="actions">
            <a href="{{ $cfg['routes']['index'] }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">
                <i class="fa-solid fa-arrow-left text-xs text-zinc-400" aria-hidden="true"></i> Back
            </a>
        </x-slot>
    </x-admin.page-header>

    <x-admin.flash />

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-medium">Please fix the highlighted {{ \Illuminate\Support\Str::plural('field', $errors->count()) }}.</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ $cfg['action'] }}" class="lg:col-span-2"
              x-data="{
                  amount: '{{ old('amount', $r ? (float) $r->amount : '') }}',
                  account: '{{ old('income_type', old('family_id') ? 'family' : 'personal') }}',
                  submitting: false,
                  {{-- AI category suggestion --}}
                  cat: {{ json_encode((string) $currentCategory) }},
                  picked: {{ $currentCategory ? 'true' : 'false' }},
                  aiCat: null, aiSource: null, aiBusy: false, aiSeq: 0,
                  async suggest(title) {
                      title = (title || '').trim();
                      if (title.length < 3) { this.aiCat = null; return; }
                      const seq = ++this.aiSeq; this.aiBusy = true;
                      try {
                          const r = await fetch('{{ route('user.ai.categorize') }}', { method: 'POST',
                              headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                              body: JSON.stringify({ title, type: '{{ $noun }}' }) });
                          const j = r.ok ? await r.json() : {};
                          if (seq !== this.aiSeq) return;
                          this.aiCat = j.category || null; this.aiSource = j.source || null;
                          if (this.aiCat && !this.picked) this.applyCat(this.aiCat, false);
                      } catch (e) { this.aiCat = null; } finally { if (seq === this.aiSeq) this.aiBusy = false; }
                  },
                  applyCat(cat, byUser) { this.cat = cat; if (byUser) this.picked = true; }
              }"
              @submit="submitting = true">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <x-admin.card flush>
                <div class="space-y-6 p-5 sm:p-6">
                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="text-sm font-medium text-zinc-800">Amount</label>
                        <div class="mt-2 flex items-center rounded-xl border bg-white px-4 focus-within:ring-2 {{ $errors->has('amount') ? 'border-rose-300 focus-within:ring-rose-100' : 'border-zinc-200 focus-within:border-indigo-400 focus-within:ring-indigo-100' }}">
                            <span class="text-2xl font-semibold text-zinc-400">₹</span>
                            <input id="amount" name="amount" type="number" inputmode="decimal" step="0.01" min="0.01" max="999999999" required x-model="amount" autofocus
                                   class="h-16 w-full border-0 bg-transparent px-2 text-3xl font-semibold tabular-nums text-zinc-900 placeholder:text-zinc-300 focus:outline-none focus:ring-0" placeholder="0">
                        </div>
                        <p class="mt-1.5 text-xs text-zinc-500" x-show="amount > 0" x-text="'₹' + Number(amount).toLocaleString('en-IN', { maximumFractionDigits: 2 })"></p>
                        @error('amount')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="{{ $titleField }}" class="text-sm font-medium text-zinc-800">{{ $isIncome ? 'Source' : 'Title' }}</label>
                            <input id="{{ $titleField }}" name="{{ $titleField }}" type="text" maxlength="150" required
                                   value="{{ old($titleField, $r->{$titleField} ?? '') }}"
                                   @input.debounce.600ms="suggest($event.target.value)"
                                   autocomplete="off"
                                   placeholder="{{ $isIncome ? 'e.g. Monthly salary' : 'e.g. Swiggy order' }}"
                                   class="mt-2 h-11 w-full rounded-lg border px-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 {{ $errors->has($titleField) ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                            @error($titleField)<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="{{ $dateField }}" class="text-sm font-medium text-zinc-800">Date</label>
                            <input id="{{ $dateField }}" name="{{ $dateField }}" type="date" required max="{{ $today }}" value="{{ $dateValue }}"
                                   class="mt-2 h-11 w-full rounded-lg border px-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 {{ $errors->has($dateField) ? 'border-rose-300 focus:ring-rose-100' : 'border-zinc-200 focus:border-indigo-400 focus:ring-indigo-100' }}">
                            @error($dateField)<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Category --}}
                    <fieldset>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <legend class="text-sm font-medium text-zinc-800">Category</legend>
                            {{-- AI suggestion status --}}
                            <p class="text-xs" aria-live="polite">
                                <span x-show="aiBusy" x-cloak class="text-zinc-400"><i class="fa-solid fa-wand-magic-sparkles mr-1 animate-pulse text-indigo-400" aria-hidden="true"></i>AI is choosing a category…</span>
                                <template x-if="!aiBusy && aiCat && cat === aiCat">
                                    <span class="text-indigo-600"><i class="fa-solid fa-wand-magic-sparkles mr-1" aria-hidden="true"></i>AI picked <strong x-text="aiCat"></strong> from the {{ $isIncome ? 'source' : 'title' }} — change it if it's wrong</span>
                                </template>
                                <template x-if="!aiBusy && aiCat && cat !== aiCat">
                                    <span class="text-zinc-500"><i class="fa-solid fa-wand-magic-sparkles mr-1 text-indigo-500" aria-hidden="true"></i>AI suggests <strong x-text="aiCat" class="text-zinc-800"></strong>
                                        <button type="button" @click="applyCat(aiCat, true)" class="ml-1 font-medium text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Use it</button></span>
                                </template>
                            </p>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach($categories as $cat => $icon)
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="{{ $cat }}" class="peer sr-only" required x-model="cat" @change="picked = true; aiSeq++">
                                    <span class="flex h-11 items-center gap-2.5 rounded-lg border border-zinc-200 px-3 text-sm text-zinc-700 transition hover:border-zinc-300 peer-checked:border-zinc-900 peer-checked:bg-zinc-900 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-300">
                                        <i class="fa-solid {{ $icon }} w-4 text-center text-xs opacity-70" aria-hidden="true"></i>{{ $cat }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('category')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </fieldset>

                    {{-- Personal / family (create only, and only if the user is in a family) --}}
                    @if(!$isEdit)
                        @if($isIncome)<input type="hidden" name="income_type" :value="account">@endif
                        @if($families->isNotEmpty())
                            <fieldset>
                                <legend class="text-sm font-medium text-zinc-800">Whose money is this?</legend>
                                <div class="mt-2 inline-flex rounded-lg border border-zinc-200 p-1">
                                    <button type="button" @click="account = 'personal'" :class="account === 'personal' ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:text-zinc-900'" class="h-8 rounded-md px-3 text-sm font-medium">Just mine</button>
                                    <button type="button" @click="account = 'family'" :class="account === 'family' ? 'bg-zinc-900 text-white' : 'text-zinc-600 hover:text-zinc-900'" class="h-8 rounded-md px-3 text-sm font-medium">A family</button>
                                </div>
                                <div x-show="account === 'family'" x-cloak class="mt-3">
                                    <label for="family_id" class="sr-only">Family</label>
                                    <select id="family_id" name="family_id" :disabled="account !== 'family'" :required="account === 'family'"
                                            class="h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-800 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 sm:w-72">
                                        <option value="">Choose a family</option>
                                        @foreach($families as $fam)
                                            <option value="{{ $fam->id }}" {{ (string) old('family_id') === (string) $fam->id ? 'selected' : '' }}>{{ $fam->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('family_id')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </fieldset>
                        @endif
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 sm:px-6">
                    <a href="{{ $cfg['routes']['index'] }}" class="h-10 rounded-lg px-4 text-sm font-medium leading-10 text-zinc-600 hover:text-zinc-900">Cancel</a>
                    <button type="submit" :disabled="submitting"
                            class="inline-flex h-10 items-center gap-2 rounded-lg bg-zinc-900 px-5 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 disabled:opacity-60">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs" x-show="submitting" x-cloak aria-hidden="true"></i>
                        {{ $isEdit ? 'Save changes' : 'Add ' . $noun }}
                    </button>
                </div>
            </x-admin.card>
        </form>

        {{-- Side panel --}}
        <div class="space-y-6">
            @if($isEdit)
                <x-admin.card title="Record details">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-zinc-500">Added</dt><dd class="text-zinc-800">{{ $r->created_at?->format('d M Y, h:i A') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-500">Last changed</dt><dd class="text-zinc-800">{{ $r->updated_at?->diffForHumans() }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-zinc-500">Belongs to</dt><dd class="text-zinc-800">{{ $r->family_id ? 'A family' : 'Just you' }}</dd></div>
                    </dl>
                </x-admin.card>
                <x-admin.card title="Delete">
                    <p class="text-sm text-zinc-500">Remove this {{ $noun }} permanently. Your totals update straight away.</p>
                    <form method="POST" action="{{ route($cfg['routes']['destroy'], $r) }}" class="mt-4" data-confirm-title="Delete this {{ $noun }}?" data-confirm="It will be removed from your records and totals. This can't be undone." data-confirm-ok="Delete {{ $noun }}">
                        @csrf @method('DELETE')
                        <button class="inline-flex h-9 items-center gap-2 rounded-lg border border-rose-200 px-3 text-sm font-medium text-rose-600 hover:bg-rose-50">
                            <i class="fa-solid fa-trash-can text-xs" aria-hidden="true"></i> Delete {{ $noun }}
                        </button>
                    </form>
                </x-admin.card>
            @else
                <x-admin.card :title="'Your recent ' . \Illuminate\Support\Str::plural($noun)" flush>
                    @php $recent = $cfg['recent'] ?? collect(); @endphp
                    @if($recent->isEmpty())
                        <p class="px-5 py-6 text-sm text-zinc-500">Nothing yet — this will be your first.</p>
                    @else
                        <ul class="divide-y divide-zinc-100">
                            @foreach($recent as $item)
                                <li class="flex items-center justify-between gap-3 px-5 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-zinc-800">{{ $item->{$titleField} }}</p>
                                        <p class="text-xs text-zinc-500">{{ $item->category }} · {{ \Carbon\Carbon::parse($item->{$dateField})->format('d M') }}</p>
                                    </div>
                                    <span class="shrink-0 text-sm tabular-nums {{ $isIncome ? 'text-emerald-600' : 'text-zinc-800' }}">{{ $inr($item->amount) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-admin.card>
            @endif
        </div>
    </div>
</div>
