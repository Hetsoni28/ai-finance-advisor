{{-- Shared editorial design layer for public pages (matches the homepage). Include once per page. --}}
@once
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    .font-serif-i { font-family: 'Instrument Serif', Georgia, serif; font-style: italic; font-weight: 400; letter-spacing: -0.01em; }
    .font-mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }
    .paper { background-color: #fbfbfa; }
    .eyebrow { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 11px; text-transform: uppercase; letter-spacing: .2em; }
    .hairline { background-image: linear-gradient(to right, transparent, rgba(15,23,42,.12), transparent); height: 1px; }

    .grain { position: relative; isolation: isolate; }
    .grain::after { content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 0; opacity: .09; mix-blend-mode: overlay;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>"); }
    .grain > *:not(.absolute) { position: relative; z-index: 1; }

    /* Long-form text (legal pages, articles) */
    .prose-site { color: #334155; font-size: 1.0625rem; line-height: 1.8; }
    .prose-site p + p { margin-top: 1rem; }
    .prose-site strong { color: #0f172a; font-weight: 700; }
    .prose-site a { color: #4f46e5; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; text-decoration-color: rgba(79,70,229,.3); }
    .prose-site a:hover { text-decoration-color: #4f46e5; }

    /* Quiet cards */
    .card-site { background: #fff; border: 1px solid #e7e5e0; border-radius: 1rem; transition: border-color .3s ease, box-shadow .3s ease; }
    a.card-site:hover, .card-site.is-hover:hover { border-color: #c7d2fe; box-shadow: 0 24px 48px -28px rgba(30,27,75,.28); }

    /* Hero entrance + scroll reveal */
    .rise > span { display: inline-block; overflow: hidden; vertical-align: bottom; padding-bottom: .08em; }
    .rise > span > span { display: inline-block; transform: translateY(105%); animation: site-rise .9s cubic-bezier(.2,.7,.2,1) forwards; animation-delay: var(--d, 0ms); }
    @keyframes site-rise { to { transform: none; } }
    .fade-in { opacity: 0; animation: site-fade .8s ease forwards; animation-delay: var(--d, 0ms); }
    @keyframes site-fade { to { opacity: 1; } }
    [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1); transition-delay: var(--d, 0ms); }
    [data-reveal].is-visible { opacity: 1; transform: none; }

    @media (prefers-reduced-motion: reduce) {
        [data-reveal], .rise > span > span, .fade-in { opacity: 1 !important; transform: none !important; animation: none !important; transition: none !important; }
    }
</style>
@endpush
@push('scripts')
<script>
    (function () {
        var els = document.querySelectorAll('[data-reveal]');
        if (!('IntersectionObserver' in window)) { els.forEach(function (el) { el.classList.add('is-visible'); }); return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
        }, { rootMargin: '0px 0px -8% 0px', threshold: .08 });
        els.forEach(function (el) { io.observe(el); });
    })();
</script>
@endpush
@endonce
