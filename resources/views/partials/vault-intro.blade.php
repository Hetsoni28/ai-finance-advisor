{{--
    Welcome intro for first-time visitors: a bank vault the visitor opens by spinning the wheel.
    Plays once per browser session, skips itself for reduced-motion users, and never blocks the page:
    Enter opens it, Escape / "Skip intro" closes it.
--}}
@push('styles')
<script>
    try {
        if (sessionStorage.getItem('fa-intro-seen') === '1' || matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.setAttribute('data-intro-seen', '');
        } else {
            document.documentElement.setAttribute('data-intro-open', '');
        }
    } catch (e) { document.documentElement.setAttribute('data-intro-seen', ''); }
</script>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    html[data-intro-seen] #vault-intro { display: none !important; }
    html[data-intro-open], html[data-intro-open] body { overflow: hidden !important; }

    #vault-intro {
        --ink: #0b0c14; --ink-2: #141624; --ink-3: #1d2033; --cream: #f1ece1; --brass: #c8a866; --brass-2: #8c7240;
        --glow: #ffd79a; --D: min(52vh, 74vw, 380px); --hole: 0px; --hx: 50%; --hy: 55%;
        position: fixed; inset: 0; z-index: 3000; background: var(--ink); color: var(--cream);
        font-family: Inter, system-ui, sans-serif; overflow: hidden; user-select: none; -webkit-user-select: none;
        -webkit-mask-image: radial-gradient(circle at var(--hx) var(--hy), transparent var(--hole), #000 calc(var(--hole) + 1px));
                mask-image: radial-gradient(circle at var(--hx) var(--hy), transparent var(--hole), #000 calc(var(--hole) + 1px));
        transition: opacity .45s ease;
    }
    #vault-intro.is-skipping { opacity: 0; }
    #vault-intro .vi-grain { position: absolute; inset: -50%; opacity: .07; pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
    #vault-intro .vi-vignette { position: absolute; inset: 0; pointer-events: none;
        background: radial-gradient(60% 55% at 50% 58%, rgba(200,168,102,.07), transparent 70%), radial-gradient(120% 90% at 50% 50%, transparent 55%, rgba(0,0,0,.55)); }

    /* Corners */
    #vault-intro .vi-corner { position: absolute; display: flex; gap: .75rem; font-size: 13px; line-height: 1.35; opacity: 0; transition: opacity .6s ease; }
    #vault-intro.is-in .vi-corner { opacity: 1; }
    #vault-intro .vi-corner small { display: block; font-size: 13px; color: rgba(241,236,225,.5); }
    #vault-intro .vi-tl { top: 24px; left: 32px; transition-delay: .15s; }
    #vault-intro .vi-tr { top: 24px; right: 32px; text-align: right; transition-delay: .25s; }
    #vault-intro .vi-bl { bottom: 24px; left: 32px; transition-delay: .35s; }
    #vault-intro .vi-br { bottom: 24px; right: 32px; transition-delay: .45s; }
    #vault-intro .vi-logo { height: 32px; width: 32px; border-radius: 10px; background: var(--cream); color: var(--ink); display: grid; place-items: center; font-size: 13px; }
    #vault-intro .vi-skip { display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px; border: 1px solid rgba(241,236,225,.2);
        background: rgba(11,12,20,.6); padding: .5rem 1rem; font-size: 12px; font-weight: 500; color: rgba(241,236,225,.75); cursor: pointer; transition: border-color .2s, color .2s; }
    #vault-intro .vi-skip:hover, #vault-intro .vi-skip:focus-visible { border-color: var(--cream); color: var(--cream); outline: none; }
    #vault-intro .vi-skip i { font-size: 11px; transition: transform .3s; }
    #vault-intro .vi-skip:hover i { transform: translateX(2px); }

    /* Centre column */
    #vault-intro .vi-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 88px 24px; text-align: center; }
    #vault-intro .vi-title { font-weight: 900; text-transform: uppercase; letter-spacing: -.055em; line-height: .86; font-size: clamp(2.6rem, 7.2vw, 5.8rem); display: flex; gap: .22em; justify-content: center; flex-wrap: wrap; }
    #vault-intro .vi-title .w { display: inline-block; overflow: hidden; padding-bottom: .06em; }
    #vault-intro .vi-title .w > span { display: inline-block; transform: translateY(110%); transition: transform 1s cubic-bezier(.16,1,.3,1); }
    #vault-intro .vi-title .w:nth-child(2) > span { transition-delay: .08s; } #vault-intro .vi-title .w:nth-child(3) > span { transition-delay: .16s; }
    #vault-intro.is-in .vi-title .w > span { transform: none; }
    #vault-intro .vi-title em { font-family: 'Instrument Serif', Georgia, serif; font-style: italic; font-weight: 400; text-transform: none; letter-spacing: -.02em; color: var(--brass); }
    #vault-intro .vi-status { margin-top: 1rem; font: 500 11.5px/1 'JetBrains Mono', ui-monospace, monospace; letter-spacing: .14em; text-transform: uppercase; color: rgba(241,236,225,.6);
        font-variant-numeric: tabular-nums; opacity: 0; transition: opacity .6s ease .55s; }
    #vault-intro.is-in .vi-status { opacity: 1; }
    #vault-intro .vi-status b { font-weight: 500; color: var(--cream); }
    #vault-intro .vi-status .is-open { color: var(--glow); }

    /* Vault */
    #vault-intro .vi-stage { position: relative; margin-top: clamp(1.75rem, 5vh, 3rem); width: var(--D); height: var(--D); perspective: 1600px;
        opacity: 0; transform: translateY(34px) scale(.97); transition: opacity .9s ease .45s, transform 1.2s cubic-bezier(.16,1,.3,1) .45s; }
    #vault-intro.is-in .vi-stage { opacity: 1; transform: none; }
    #vault-intro.is-clunk .vi-stage { animation: vi-clunk .32s ease; }
    @keyframes vi-clunk { 30% { transform: translateX(-3px) } 60% { transform: translateX(2px) } }

    #vault-intro .vi-frame { position: absolute; inset: -9%; border-radius: 50%;
        background: radial-gradient(circle, #05060b 62%, #191b2b 63%, #10121d 72%, #1c1f30 100%);
        box-shadow: 0 0 0 1px rgba(241,236,225,.08), 0 40px 90px -20px rgba(0,0,0,.8), inset 0 2px 0 rgba(255,255,255,.04); }
    #vault-intro .vi-frame::after { content: ''; position: absolute; inset: 9.8%; border-radius: 50%; box-shadow: inset 0 0 0 1px rgba(241,236,225,.06), inset 0 18px 40px rgba(0,0,0,.9); }
    #vault-intro .vi-floor { position: absolute; left: 50%; bottom: -13%; width: 170%; height: 1px; transform: translateX(-50%); background: linear-gradient(90deg, transparent, rgba(241,236,225,.22), transparent); }

    #vault-intro .vi-light { position: absolute; inset: 0; border-radius: 50%; overflow: hidden;
        background: radial-gradient(circle at 50% 50%, #fff6e2 0%, var(--glow) 28%, #c98a3a 62%, #3a2614 100%);
        opacity: .0; transition: opacity 1.1s ease; }
    #vault-intro .vi-light::before { content: '₹'; position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Instrument Serif', Georgia, serif;
        font-size: calc(var(--D) * .42); color: rgba(255,255,255,.55); mix-blend-mode: overlay; }
    #vault-intro.is-opening .vi-light { opacity: 1; }
    #vault-intro .vi-rays { position: absolute; inset: -40%; border-radius: 50%; pointer-events: none; opacity: 0; transition: opacity 1.2s ease .25s;
        background: radial-gradient(circle, rgba(255,215,154,.35), transparent 60%); filter: blur(10px); }
    #vault-intro.is-opening .vi-rays { opacity: 1; }

    #vault-intro .vi-bolts { position: absolute; inset: 0; transition: opacity .3s ease; }
    #vault-intro.is-opening .vi-bolts { opacity: 0; }
    #vault-intro .vi-bolt { position: absolute; left: 50%; top: 50%; width: calc(var(--D) * .075); height: calc(var(--D) * .03); margin: calc(var(--D) * -.015) 0 0 calc(var(--D) * -.0375);
        border-radius: 3px; background: linear-gradient(180deg, #e9d39c, var(--brass) 45%, var(--brass-2));
        box-shadow: 0 1px 0 rgba(255,255,255,.3) inset, 0 2px 6px rgba(0,0,0,.6);
        transform: rotate(var(--a)) translateX(calc(var(--D) * (.5 + .035 - var(--retract, 0) * .075))); }

    #vault-intro .vi-door { position: absolute; inset: 0; border-radius: 50%; transform-origin: 0% 50%; transform-style: preserve-3d;
        transition: transform 1.25s cubic-bezier(.65,0,.2,1); }
    #vault-intro.is-opening .vi-door { transform: rotateY(-108deg); }
    #vault-intro .vi-face { position: absolute; inset: 0; border-radius: 50%;
        background: radial-gradient(circle at 38% 30%, #2a2e45, var(--ink-3) 38%, var(--ink-2) 70%, #0d0f1a);
        box-shadow: inset 0 0 0 1px rgba(241,236,225,.12), inset 0 0 0 calc(var(--D) * .03) rgba(11,12,20,.55), inset 0 -20px 40px rgba(0,0,0,.45), 0 25px 60px -15px rgba(0,0,0,.85); }
    #vault-intro .vi-face svg { position: absolute; inset: 0; width: 100%; height: 100%; }
    #vault-intro .vi-dial { transform-origin: 50% 50%; }
    #vault-intro .vi-hinge { position: absolute; left: -3.5%; width: 7%; height: 13%; border-radius: 4px;
        background: linear-gradient(90deg, #2a2d42, #3a3e58 50%, #23263a); box-shadow: inset 0 0 0 1px rgba(241,236,225,.1); }

    #vault-intro .vi-wheel { position: absolute; left: 50%; top: 50%; width: 46%; height: 46%; transform: translate(-50%, -50%); border-radius: 50%;
        cursor: grab; touch-action: none; outline: none; }
    #vault-intro .vi-wheel:active { cursor: grabbing; }
    #vault-intro .vi-wheel:focus-visible { box-shadow: 0 0 0 3px rgba(200,168,102,.55); }
    #vault-intro .vi-wheel svg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; }
    #vault-intro .vi-spokes { transform-origin: 50% 50%; filter: drop-shadow(0 8px 12px rgba(0,0,0,.55)); }

    #vault-intro .vi-hint { position: absolute; right: -128px; top: 30%; display: flex; align-items: center; gap: .5rem;
        font: 500 10.5px/1 'JetBrains Mono', ui-monospace, monospace; letter-spacing: .14em; text-transform: uppercase; color: rgba(241,236,225,.55);
        opacity: 0; transition: opacity .6s ease; pointer-events: none; }
    #vault-intro.show-hint .vi-hint { opacity: 1; }
    #vault-intro .vi-hint path.arc { stroke-dasharray: 1; stroke-dashoffset: 1; }
    #vault-intro.show-hint .vi-hint path.arc { animation: vi-draw 1.1s cubic-bezier(.65,0,.35,1) forwards; }
    @keyframes vi-draw { to { stroke-dashoffset: 0; } }

    #vault-intro.is-opening .vi-title, #vault-intro.is-opening .vi-status, #vault-intro.is-opening .vi-corner, #vault-intro.is-opening .vi-hint { opacity: 0 !important; transition: opacity .5s ease .35s; }

    @media (max-width: 640px) {
        #vault-intro .vi-tl, #vault-intro .vi-bl { left: 20px; } #vault-intro .vi-tr, #vault-intro .vi-br { right: 20px; }
        #vault-intro .vi-hint { display: none; }
        #vault-intro .vi-tr small, #vault-intro .vi-bl small { display: none; }
    }
</style>
@endpush

@push('before-content')
<div id="vault-intro" role="dialog" aria-modal="true" aria-label="Welcome to FinanceAI">
    <div class="vi-grain" aria-hidden="true"></div>
    <div class="vi-vignette" aria-hidden="true"></div>

    <div class="vi-corner vi-tl">
        <span class="vi-logo" aria-hidden="true"><i class="fa-solid fa-cube"></i></span>
        <div>Made by<small>FinanceAI</small></div>
    </div>
    <div class="vi-corner vi-tr">
        <div><span data-vi-verb>Spin the wheel</span> to open<small>or press Enter</small></div>
    </div>
    <div class="vi-corner vi-bl">
        <div>{{ now()->year }}<small>Personal &amp; family finance · ₹</small></div>
    </div>
    <div class="vi-corner vi-br">
        <button type="button" class="vi-skip" data-vi-skip>Skip intro <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
    </div>

    <div class="vi-center">
        <h2 class="vi-title" aria-label="Open the vault">
            <span class="w"><span>Open</span></span>
            <span class="w"><span><em>the</em></span></span>
            <span class="w"><span>vault</span></span>
        </h2>
        <p class="vi-status" aria-hidden="true">Vault : <b data-vi-state>sealed</b> &nbsp;·&nbsp; <span data-vi-pct>000</span>%</p>

        <div class="vi-stage" data-vi-stage>
            <div class="vi-floor" aria-hidden="true"></div>
            <div class="vi-frame" aria-hidden="true"></div>
            <div class="vi-rays" aria-hidden="true"></div>
            <div class="vi-light" aria-hidden="true"></div>
            <div class="vi-bolts" aria-hidden="true">
                @foreach([0, 45, 90, 135, 225, 270, 315] as $a)
                    <span class="vi-bolt" style="--a: {{ $a }}deg"></span>
                @endforeach
            </div>

            <div class="vi-door">
                <span class="vi-hinge" style="top: 22%" aria-hidden="true"></span>
                <span class="vi-hinge" style="bottom: 22%" aria-hidden="true"></span>
                <div class="vi-face" aria-hidden="true">
                    <svg viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="86" fill="none" stroke="rgba(241,236,225,.08)" />
                        <circle cx="100" cy="100" r="74" fill="none" stroke="rgba(241,236,225,.05)" stroke-width="6" />
                        <g class="vi-dial" data-vi-dial>
                            @for($i = 0; $i < 60; $i++)
                                @php $long = $i % 5 === 0; @endphp
                                <line x1="100" y1="{{ $long ? 17 : 19 }}" x2="100" y2="24" stroke="rgba(241,236,225,{{ $long ? '.45' : '.18' }})" stroke-width="{{ $long ? 1.1 : .7 }}" transform="rotate({{ $i * 6 }} 100 100)" />
                            @endfor
                            @for($i = 0; $i < 12; $i++)
                                <text x="100" y="33" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="5.2" fill="rgba(241,236,225,.4)" transform="rotate({{ $i * 30 }} 100 100)">{{ str_pad((string) ($i * 5), 2, '0', STR_PAD_LEFT) }}</text>
                            @endfor
                        </g>
                        <path d="M100 8 l3 6 h-6z" fill="var(--brass)" />
                    </svg>
                </div>

                <div class="vi-wheel" data-vi-wheel tabindex="0" role="slider" aria-label="Vault wheel. Spin it, or press Enter, to open FinanceAI"
                     aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-valuetext="Unlocked 0 percent">
                    <svg viewBox="0 0 100 100" aria-hidden="true">
                        <circle cx="50" cy="50" r="49" fill="none" stroke="rgba(241,236,225,.1)" stroke-width="1.2" />
                        <circle cx="50" cy="50" r="49" fill="none" stroke="var(--brass)" stroke-width="1.6" stroke-linecap="round" pathLength="100"
                                stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 50 50)" data-vi-ring />
                        <g class="vi-spokes" data-vi-spokes>
                            <circle cx="50" cy="50" r="31" fill="none" stroke="url(#vi-brass)" stroke-width="4.5" />
                            @foreach([0, 60, 120] as $r)
                                <g transform="rotate({{ $r }} 50 50)">
                                    <rect x="47.6" y="6" width="4.8" height="88" rx="2.4" fill="url(#vi-brass)" />
                                    <circle cx="50" cy="6" r="4.6" fill="url(#vi-brass)" />
                                    <circle cx="50" cy="94" r="4.6" fill="url(#vi-brass)" />
                                </g>
                            @endforeach
                            <circle cx="50" cy="50" r="11" fill="#1a1c2c" stroke="url(#vi-brass)" stroke-width="3" />
                            <circle cx="50" cy="50" r="3.2" fill="var(--brass)" />
                        </g>
                        <defs>
                            <linearGradient id="vi-brass" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#f0dcaa" /><stop offset=".45" stop-color="#c8a866" /><stop offset="1" stop-color="#7d6434" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>

            <div class="vi-hint" aria-hidden="true">
                <svg viewBox="0 0 60 40" width="48" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path class="arc" pathLength="1" d="M6 8 C 30 4, 48 14, 50 33" />
                    <path d="M43 27 L50 35 L56 27" />
                </svg>
                Spin
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var root = document.getElementById('vault-intro');
    if (!root || document.documentElement.hasAttribute('data-intro-seen')) { if (root) root.remove(); return; }

    var $ = function (s) { return root.querySelector(s); };
    var wheel = $('[data-vi-wheel]'), spokes = $('[data-vi-spokes]'), dial = $('[data-vi-dial]'), ring = $('[data-vi-ring]'),
        stage = $('[data-vi-stage]'), pctEl = $('[data-vi-pct]'), stateEl = $('[data-vi-state]'), bolts = $('.vi-bolts');
    var TURN = 540;                 // degrees of spin needed to unlock (one and a half turns)
    var target = 0, shown = 0, done = false, auto = false, dragging = false, lastAngle = 0, lastBuzz = 0, raf = 0;

    if (matchMedia('(pointer: coarse)').matches) { var v = $('[data-vi-verb]'); if (v) v.textContent = 'Turn the wheel'; }

    function finish(skipped) {
        try { sessionStorage.setItem('fa-intro-seen', '1'); } catch (e) {}
        cancelAnimationFrame(raf);
        document.removeEventListener('keydown', onKey);
        document.documentElement.removeAttribute('data-intro-open');
        document.documentElement.setAttribute('data-intro-seen', '');
        root.remove();
        window.dispatchEvent(new CustomEvent('intro:done', { detail: { skipped: !!skipped } }));
    }

    function skip() {
        if (done) return; done = true;
        root.classList.add('is-skipping');
        setTimeout(function () { finish(true); }, 450);
    }

    function open() {
        done = true;
        stateEl.textContent = 'open'; stateEl.classList.add('is-open');
        root.classList.add('is-clunk');
        if (navigator.vibrate) { try { navigator.vibrate([12, 40, 18]); } catch (e) {} }
        setTimeout(function () { root.classList.add('is-opening'); }, 260);
        // After the door swings, the vault mouth widens until it reveals the whole page.
        setTimeout(function () {
            var r = stage.getBoundingClientRect();
            var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
            root.style.setProperty('--hx', cx + 'px'); root.style.setProperty('--hy', cy + 'px');
            var from = r.width / 2, to = Math.hypot(Math.max(cx, innerWidth - cx), Math.max(cy, innerHeight - cy)) + 40;
            var t0 = performance.now(), dur = 950;
            (function grow(now) {
                var t = Math.min(1, (now - t0) / dur), e = t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
                root.style.setProperty('--hole', (from + (to - from) * e) + 'px');
                if (t < 1) requestAnimationFrame(grow); else finish(false);
            })(t0);
        }, 1250);
    }

    function render() {
        shown += (target - shown) * (auto ? 1 : .22);
        if (Math.abs(target - shown) < .05) shown = target;
        var p = Math.max(0, Math.min(1, shown / TURN)), pct = Math.round(p * 100);
        spokes.style.transform = 'rotate(' + shown + 'deg)';
        dial.style.transform = 'rotate(' + (-shown * .5) + 'deg)';
        ring.style.strokeDashoffset = 100 - pct;
        bolts.style.setProperty('--retract', Math.min(1, p * 1.15));
        pctEl.textContent = String(pct).padStart(3, '0');
        if (!done) stateEl.textContent = pct === 0 ? 'sealed' : 'unlocking';
        wheel.setAttribute('aria-valuenow', pct); wheel.setAttribute('aria-valuetext', 'Unlocked ' + pct + ' percent');
        if (Math.floor(shown / 45) !== lastBuzz) { lastBuzz = Math.floor(shown / 45); if (dragging && navigator.vibrate) { try { navigator.vibrate(6); } catch (e) {} } }
        if (!done && p >= 1) { open(); }
        raf = requestAnimationFrame(render);
    }

    function spinBy(deg) { if (done) return; target = Math.max(0, Math.min(TURN, target + deg)); root.classList.remove('show-hint'); }

    function autoOpen() {
        if (done || auto) return; auto = true;
        var start = target, t0 = performance.now(), dur = 1100;
        (function step(now) {
            var t = Math.min(1, (now - t0) / dur), e = 1 - Math.pow(1 - t, 3);
            target = start + (TURN - start) * e;
            if (t < 1 && !done) requestAnimationFrame(step);
        })(t0);
        root.classList.remove('show-hint');
    }

    function angleAt(x, y) {
        var r = wheel.getBoundingClientRect();
        return Math.atan2(y - (r.top + r.height / 2), x - (r.left + r.width / 2)) * 180 / Math.PI;
    }

    stage.addEventListener('pointerdown', function (e) {
        if (done || auto) return;
        dragging = true; lastAngle = angleAt(e.clientX, e.clientY);
        try { stage.setPointerCapture(e.pointerId); } catch (err) {}
        wheel.focus({ preventScroll: true });
    });
    stage.addEventListener('pointermove', function (e) {
        if (!dragging) return;
        var a = angleAt(e.clientX, e.clientY), d = a - lastAngle;
        if (d > 180) d -= 360; if (d < -180) d += 360;
        lastAngle = a; spinBy(d);
    });
    ['pointerup', 'pointercancel'].forEach(function (t) { stage.addEventListener(t, function () { dragging = false; }); });
    root.addEventListener('wheel', function (e) { e.preventDefault(); if (!auto) spinBy((Math.abs(e.deltaY) > Math.abs(e.deltaX) ? e.deltaY : e.deltaX) * .45); }, { passive: false });

    function onKey(e) {
        if (e.key === 'Escape') { e.preventDefault(); skip(); }
        else if (e.key === 'Enter' && !(e.target.closest && e.target.closest('[data-vi-skip]'))) { e.preventDefault(); autoOpen(); }
        else if (e.key === ' ' && document.activeElement === wheel) { e.preventDefault(); autoOpen(); }
        else if ((e.key === 'ArrowRight' || e.key === 'ArrowUp') && !auto) { e.preventDefault(); spinBy(30); }
        else if ((e.key === 'ArrowLeft' || e.key === 'ArrowDown') && !auto) { e.preventDefault(); spinBy(-30); }
        else if (e.key === 'Tab') {   // keep focus inside the intro
            var f = [wheel, $('[data-vi-skip]')], i = f.indexOf(document.activeElement);
            e.preventDefault(); f[(i + (e.shiftKey ? f.length - 1 : 1)) % f.length].focus();
        }
    }
    document.addEventListener('keydown', onKey);
    $('[data-vi-skip]').addEventListener('click', skip);

    requestAnimationFrame(function () { requestAnimationFrame(function () { root.classList.add('is-in'); }); });
    setTimeout(function () { if (!done && target === 0) root.classList.add('show-hint'); }, 2200);
    setTimeout(function () { if (!done) wheel.focus({ preventScroll: true }); }, 900);
    raf = requestAnimationFrame(render);
})();
</script>
@endpush
