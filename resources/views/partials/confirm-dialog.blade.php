{{--
    Styled replacement for the browser's confirm() box.

    Any form with data-confirm="Body text" shows this dialog before it submits. Optional attributes:
      data-confirm-title   heading (default "Are you sure?")
      data-confirm-ok      confirm button label (default "Confirm")
      data-confirm-cancel  cancel button label (default "Go back")
      data-confirm-tone    "danger" (red, for deletions) or "primary" (default "danger")
--}}
<div id="confirm-dialog" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" aria-hidden="true">
    <div data-cd-backdrop class="absolute inset-0 bg-zinc-950/50 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <div data-cd-panel role="alertdialog" aria-modal="true" aria-labelledby="cd-title" aria-describedby="cd-body"
         class="relative w-full max-w-md translate-y-2 scale-[.98] rounded-2xl bg-white p-6 opacity-0 shadow-[0_24px_64px_-12px_rgba(24,24,27,.45)] ring-1 ring-zinc-900/5 transition duration-200 sm:p-7">
        <div class="flex gap-4">
            <span data-cd-icon class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full" aria-hidden="true"><i class="fa-solid"></i></span>
            <div class="min-w-0 pt-0.5">
                <h2 id="cd-title" class="text-base font-semibold text-zinc-900"></h2>
                <p id="cd-body" class="mt-1.5 text-sm leading-relaxed text-zinc-600"></p>
            </div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" data-cd-cancel class="inline-flex h-10 items-center justify-center rounded-lg border border-zinc-200 bg-white px-4 text-sm font-medium text-zinc-700 hover:bg-zinc-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-zinc-200"></button>
            <button type="button" data-cd-ok class="inline-flex h-10 items-center justify-center gap-2 rounded-lg px-4 text-sm font-semibold text-white focus:outline-none focus-visible:ring-4 disabled:opacity-70"></button>
        </div>
    </div>
</div>

<script>
(function () {
    var root = document.getElementById('confirm-dialog');
    if (!root) return;
    var panel = root.querySelector('[data-cd-panel]'), backdrop = root.querySelector('[data-cd-backdrop]');
    var titleEl = root.querySelector('#cd-title'), bodyEl = root.querySelector('#cd-body');
    var icon = root.querySelector('[data-cd-icon]'), okBtn = root.querySelector('[data-cd-ok]'), cancelBtn = root.querySelector('[data-cd-cancel]');
    var tones = {
        danger:  { icon: 'fa-triangle-exclamation', iconCls: 'bg-rose-50 text-rose-600 ring-8 ring-rose-50/50', okCls: 'bg-rose-600 hover:bg-rose-700 focus-visible:ring-rose-200' },
        primary: { icon: 'fa-circle-question',      iconCls: 'bg-indigo-50 text-indigo-600 ring-8 ring-indigo-50/50', okCls: 'bg-zinc-900 hover:bg-zinc-800 focus-visible:ring-zinc-300' }
    };
    var form = null, submitter = null, returnFocus = null;

    function open(f, sub) {
        form = f; submitter = sub || null; returnFocus = document.activeElement;
        var d = f.dataset, tone = tones[d.confirmTone] || tones.danger;
        titleEl.textContent = d.confirmTitle || 'Are you sure?';
        bodyEl.textContent = d.confirm;
        okBtn.textContent = d.confirmOk || 'Confirm';
        cancelBtn.textContent = d.confirmCancel || 'Go back';
        icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full ' + tone.iconCls;
        icon.innerHTML = '<i class="fa-solid ' + tone.icon + '"></i>';
        okBtn.className = okBtn.className.replace(/\b(bg|hover:bg|focus-visible:ring)-(rose|zinc|indigo)-\d+\b/g, '').trim() + ' ' + tone.okCls;
        okBtn.disabled = false;

        root.classList.remove('hidden'); root.classList.add('flex'); root.setAttribute('aria-hidden', 'false');
        document.documentElement.style.overflow = 'hidden';
        requestAnimationFrame(function () {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'translate-y-2', 'scale-[.98]');
        });
        // For destructive actions the safe choice gets focus first.
        (d.confirmTone === 'primary' ? okBtn : cancelBtn).focus();
    }

    function close() {
        backdrop.classList.add('opacity-0');
        panel.classList.add('opacity-0', 'translate-y-2', 'scale-[.98]');
        root.setAttribute('aria-hidden', 'true');
        document.documentElement.style.overflow = '';
        setTimeout(function () { root.classList.add('hidden'); root.classList.remove('flex'); }, 180);
        if (returnFocus && returnFocus.focus) returnFocus.focus();
        form = null; submitter = null;
    }

    function confirmIt() {
        if (!form) return;
        var f = form, s = submitter;
        okBtn.disabled = true;
        okBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> ' + okBtn.textContent;
        f.dataset.confirmed = '1';
        if (f.requestSubmit) { s && s.form === f ? f.requestSubmit(s) : f.requestSubmit(); } else { f.submit(); }
    }

    // Capture phase: runs before any other submit handler on the form.
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!(f instanceof HTMLFormElement) || !f.dataset.confirm) return;
        if (f.dataset.confirmed === '1') { delete f.dataset.confirmed; return; }
        e.preventDefault(); e.stopImmediatePropagation();
        open(f, e.submitter);
    }, true);

    okBtn.addEventListener('click', confirmIt);
    cancelBtn.addEventListener('click', close);
    backdrop.addEventListener('click', close);
    root.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { e.preventDefault(); close(); }
        else if (e.key === 'Tab') {
            e.preventDefault();
            (document.activeElement === okBtn ? cancelBtn : okBtn).focus();
        }
    });
})();
</script>
