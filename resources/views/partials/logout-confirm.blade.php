{{--
    Logout confirmation.
    Included once in layouts/app, so it covers every logout button on the site
    (landing nav, patient settings, admin and doctor sidebars, doctor avatar menu)
    without each page needing its own code.
--}}
@auth
<div class="klo" id="kloModal" hidden>
    <div class="klo-backdrop" data-klo-close></div>
    <div class="klo-box" role="dialog" aria-modal="true" aria-labelledby="kloTitle" aria-describedby="kloText">
        <span class="klo-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </span>
        <h2 id="kloTitle">{{ __('site.logout.title') }}</h2>
        <p id="kloText">{{ __('site.logout.text') }}</p>
        <div class="klo-actions">
            <button type="button" class="klo-btn klo-btn-light" id="kloCancel" data-klo-close>{{ __('site.logout.cancel') }}</button>
            <button type="button" class="klo-btn klo-btn-dark" id="kloConfirm">{{ __('site.logout.confirm') }}</button>
        </div>
    </div>
</div>
@endauth

{{-- After logging out (or deleting an account) the user lands on the home page with a status message.
     The landing page has no flash area of its own, so show it here as a small toast. --}}
@if (session('status') && request()->routeIs('home'))
    <div class="klo-toast" id="kloToast" role="status">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif

<style>
.klo{ position:fixed; inset:0; z-index:1060; display:flex; align-items:center; justify-content:center; padding:20px; font-family:'Plus Jakarta Sans',system-ui,sans-serif; }
.klo[hidden]{ display:none; }
.klo-backdrop{ position:absolute; inset:0; background:rgba(47,35,24,.55); opacity:0; transition:opacity .2s ease; }
.klo-box{ position:relative; width:100%; max-width:380px; background:#FBF8F3; border-radius:24px; padding:28px 26px 24px; text-align:center; box-shadow:0 30px 60px -20px rgba(47,35,24,.6); opacity:0; transform:translateY(12px) scale(.97); transition:opacity .2s ease, transform .2s ease; }
.klo.show .klo-backdrop{ opacity:1; }
.klo.show .klo-box{ opacity:1; transform:none; }
.klo-ico{ width:56px; height:56px; margin:0 auto 14px; border-radius:50%; background:#EFE6DA; color:#5E442E; display:flex; align-items:center; justify-content:center; }
.klo-box h2{ font-family:'Fraunces',Georgia,serif; font-size:1.4rem; font-weight:600; color:#5E442E; margin:0 0 6px; letter-spacing:-.02em; }
.klo-box p{ font-size:.9rem; color:#7a6a5a; line-height:1.55; margin:0 0 22px; }
.klo-actions{ display:flex; gap:10px; }
.klo-btn{ flex:1; border-radius:999px; padding:12px 16px; font-family:inherit; font-size:.9rem; font-weight:700; cursor:pointer; border:1px solid transparent; transition:background .2s ease, transform .2s ease; }
.klo-btn:hover{ transform:translateY(-1px); }
.klo-btn:focus-visible{ outline:3px solid #B7C4B1; outline-offset:2px; }
.klo-btn-light{ background:#fff; color:#5E442E; border-color:rgba(94,68,46,.2); }
.klo-btn-dark{ background:#5E442E; color:#FBF8F3; }
.klo-btn-dark:hover{ background:#2f2318; }
.klo-btn:disabled{ opacity:.6; cursor:wait; transform:none; }

/* left + right + auto margins centre it without squeezing it to half the screen width */
.klo-toast{ position:fixed; left:16px; right:16px; top:20px; margin:0 auto; width:fit-content; z-index:1050; display:flex; align-items:center; gap:9px; background:#5E442E; color:#FBF8F3; padding:12px 20px; border-radius:999px; font-family:'Plus Jakarta Sans',system-ui,sans-serif; font-size:.88rem; font-weight:600; box-shadow:0 16px 32px -14px rgba(47,35,24,.7); opacity:0; transform:translateY(-12px); transition:opacity .3s ease, transform .3s ease; pointer-events:none; }
.klo-toast.show{ opacity:1; transform:none; }
.klo-toast svg{ flex-shrink:0; color:#B7C4B1; }

@media (max-width:420px){
    .klo{ align-items:flex-end; padding:12px; }
    .klo-box{ border-radius:22px; padding:24px 20px 20px; }
    .klo-actions{ flex-direction:column-reverse; }
}
@media (prefers-reduced-motion: reduce){
    .klo-backdrop, .klo-box, .klo-toast{ transition:none; }
}
</style>

<script>
(function(){
    /* ---------- logout confirmation ---------- */
    var modal = document.getElementById('kloModal');
    if (modal) {
        var logoutPath = new URL(@json(route('logout')), window.location.href).pathname;
        var cancelBtn  = document.getElementById('kloCancel');
        var confirmBtn = document.getElementById('kloConfirm');
        var pendingForm = null, lastFocus = null, closeTimer = null;

        function isLogoutForm(form){
            try { return new URL(form.action, window.location.href).pathname === logoutPath; }
            catch (e) { return false; }
        }

        function open(form){
            pendingForm = form;
            lastFocus = document.activeElement;
            clearTimeout(closeTimer);
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function(){ modal.classList.add('show'); });
            cancelBtn.focus();
        }

        function close(){
            modal.classList.remove('show');
            document.body.style.overflow = '';
            closeTimer = setTimeout(function(){ modal.hidden = true; }, 200);
            pendingForm = null;
            if (lastFocus && lastFocus.focus) lastFocus.focus();
        }

        /* Capture phase, so this runs before anything else and every logout form is covered. */
        document.addEventListener('submit', function(e){
            var form = e.target;
            if (!isLogoutForm(form)) return;
            e.preventDefault();
            open(form);
        }, true);

        confirmBtn.addEventListener('click', function(){
            if (!pendingForm) return;
            confirmBtn.disabled = true;
            cancelBtn.disabled = true;
            /* form.submit() does not fire the submit event again, so no loop. */
            pendingForm.submit();
        });

        modal.querySelectorAll('[data-klo-close]').forEach(function(el){
            el.addEventListener('click', close);
        });

        document.addEventListener('keydown', function(e){
            if (modal.hidden) return;
            if (e.key === 'Escape') { e.preventDefault(); close(); }
            /* keep keyboard focus inside the dialog */
            if (e.key === 'Tab') {
                var first = cancelBtn, last = confirmBtn;
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });
    }

    /* ---------- "you have been logged out" toast ---------- */
    var toast = document.getElementById('kloToast');
    if (toast) {
        requestAnimationFrame(function(){ toast.classList.add('show'); });
        setTimeout(function(){ toast.classList.remove('show'); }, 4000);
    }
})();
</script>