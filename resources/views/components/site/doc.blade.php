{{--
    Long-form document layout (legal pages, security). Left: sticky contents with scroll-spy.
    Right: the slot. Each section inside the slot should be <section id="..." class="doc-section">.
    Props: sections = [id => title, ...]
--}}
@props(['sections' => []])

<section class="py-16 sm:py-24" x-data="docSpy()" x-init="init()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12">
        <aside class="lg:col-span-3">
            <nav class="lg:sticky lg:top-28 -mx-4 px-4 lg:mx-0 lg:px-0 overflow-x-auto no-scrollbar" aria-label="Contents">
                <p class="eyebrow text-slate-400 hidden lg:block">Contents</p>
                <ol class="flex lg:flex-col gap-2 lg:gap-0 lg:mt-5 lg:border-l border-slate-200 w-max lg:w-auto">
                    @foreach($sections as $id => $title)
                        <li>
                            <a href="#{{ $id }}"
                               class="block whitespace-nowrap lg:whitespace-normal lg:-ml-px px-3 py-1.5 lg:py-2 lg:pl-4 rounded-full lg:rounded-none border lg:border-0 lg:border-l text-sm transition-colors"
                               :class="active === '{{ $id }}' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-slate-200 lg:border-transparent text-slate-500 hover:text-slate-900'">
                                <span class="font-mono text-xs text-slate-400 mr-1.5">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $title }}
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </aside>

        <article class="lg:col-span-9 max-w-3xl prose-site space-y-14">
            {{ $slot }}
        </article>
    </div>
</section>

@once
@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .doc-section { scroll-margin-top: 7rem; }
    .doc-section h2 { margin-top: .5rem; font-size: 1.75rem; line-height: 1.2; font-weight: 900; letter-spacing: -.02em; color: #0f172a; }
    .doc-section > h2 + * { margin-top: 1.25rem; }
    .doc-section ul.doc-list { margin-top: 1.25rem; border-top: 1px solid #e2e8f0; }
    .doc-section ul.doc-list li { padding: .85rem 0; border-bottom: 1px solid #e2e8f0; font-size: 1rem; }
    @media print { header, footer, aside, .fixed { display: none !important; } [data-reveal] { opacity: 1 !important; transform: none !important; } }
</style>
@endpush
@push('scripts')
<script>
    function docSpy() {
        return {
            active: null,
            init() {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((e) => { if (e.isIntersecting) this.active = e.target.id; });
                }, { rootMargin: '-20% 0px -70% 0px' });
                document.querySelectorAll('.doc-section').forEach((s) => io.observe(s));
            },
        };
    }
</script>
@endpush
@endonce
