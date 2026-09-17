{{-- Treatments offered — Klinik Pergigian Dr. Zuraida --}}
{{-- Included from the landing page, inside .kx-shell --}}

<section id="services" class="kx-section">

    {{-- reusable tooth glyph, referenced by every card --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="kx-svc-tooth" viewBox="0 0 24 24">
            <path d="M12 2.6c-1.3 0-2.1.5-3.2.5-1 0-1.6-.5-2.9-.5C3.6 2.6 2 4.8 2 8.1c0 2.3.6 4 1.2 5.6.5 1.3.8 2.4 1 3.9.3 2.1.5 3.8 2 3.8 1.4 0 1.8-1.6 2.2-3.6.4-1.9.7-3.4 1.6-3.4s1.2 1.5 1.6 3.4c.4 2 .8 3.6 2.2 3.6 1.5 0 1.7-1.7 2-3.8.2-1.5.5-2.6 1-3.9.6-1.6 1.2-3.3 1.2-5.6 0-3.3-1.6-5.5-3.9-5.5-1.3 0-1.9.5-2.9.5-1.1 0-1.9-.5-3.2-.5z"/>
        </symbol>
    </svg>

    <div class="kx-sec-head">
        <p class="kx-eyebrow">{{ __('site.treat.eyebrow') }}</p>
        <h2 class="kx-h2">{{ __('site.treat.title') }}</h2>
        <p class="kx-sec-sub">{{ __('site.treat.sub') }}</p>
    </div>

    <div class="kx-svc-filters" role="group" aria-label="{{ __('site.treat.filter_label') }}">
        <button type="button" class="kx-svc-chip is-active" data-filter="all" aria-pressed="true">{{ __('site.treat.all') }}</button>
        <button type="button" class="kx-svc-chip" data-filter="asas" aria-pressed="false">{{ __('site.treat.cat_routine') }}</button>
        <button type="button" class="kx-svc-chip" data-filter="pemulihan" aria-pressed="false">{{ __('site.treat.cat_restore') }}</button>
        <button type="button" class="kx-svc-chip" data-filter="ganti" aria-pressed="false">{{ __('site.treat.cat_replace') }}</button>
        <button type="button" class="kx-svc-chip" data-filter="estetik" aria-pressed="false">{{ __('site.treat.cat_appearance') }}</button>
    </div>

    <ul class="kx-svc-grid" id="kxSvcGrid">

        {{-- ── Routine & preventive ───────────────────────────── --}}
        <li class="kx-svc-card" data-cat="asas">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_routine') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.scaling_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.scaling_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="asas">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_routine') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.fluoride_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.fluoride_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="asas">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_routine') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.extract_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.extract_desc') }}</p>
        </li>

        {{-- ── Restorative ────────────────────────────────────── --}}
        <li class="kx-svc-card" data-cat="pemulihan">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_restore') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.filling_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.filling_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="pemulihan">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_restore') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.rootcanal_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.rootcanal_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="pemulihan">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_restore') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.post_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.post_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="pemulihan">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_restore') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.crown_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.crown_desc') }}</p>
            <ul class="kx-svc-opts">
                <li>{{ __('site.treat.crown_opt_1') }}</li>
                <li>{{ __('site.treat.crown_opt_2') }}</li>
                <li>{{ __('site.treat.crown_opt_3') }}</li>
            </ul>
        </li>

        {{-- ── Replacing teeth ────────────────────────────────── --}}
        <li class="kx-svc-card" data-cat="ganti">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_replace') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.bridge_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.bridge_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="ganti">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_replace') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.denture_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.denture_desc') }}</p>
            <ul class="kx-svc-opts">
                <li>{{ __('site.treat.denture_opt_1') }}</li>
                <li>{{ __('site.treat.denture_opt_2') }}</li>
            </ul>
        </li>

        <li class="kx-svc-card" data-cat="ganti">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_replace') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.frc_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.frc_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="ganti">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_replace') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.implant_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.implant_desc') }}</p>
        </li>

        {{-- ── Appearance & alignment ─────────────────────────── --}}
        <li class="kx-svc-card" data-cat="estetik">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_appearance') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.veneer_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.veneer_desc') }}</p>
            <ul class="kx-svc-opts">
                <li>{{ __('site.treat.veneer_opt_1') }}</li>
                <li>{{ __('site.treat.veneer_opt_2') }}</li>
            </ul>
        </li>

        <li class="kx-svc-card" data-cat="estetik">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_appearance') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.whitening_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.whitening_desc') }}</p>
        </li>

        <li class="kx-svc-card" data-cat="estetik">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_appearance') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.braces_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.braces_desc') }}</p>
            <ul class="kx-svc-opts">
                <li>{{ __('site.treat.braces_opt_1') }}</li>
                <li>{{ __('site.treat.braces_opt_2') }}</li>
            </ul>
        </li>

        <li class="kx-svc-card" data-cat="estetik">
            <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
            <span class="kx-svc-cat">{{ __('site.treat.cat_appearance') }}</span>
            <h3 class="kx-svc-name">{{ __('site.treat.retainer_name') }}</h3>
            <p class="kx-svc-desc">{{ __('site.treat.retainer_desc') }}</p>
            <ul class="kx-svc-opts">
                <li>{{ __('site.treat.retainer_opt_1') }}</li>
                <li>{{ __('site.treat.retainer_opt_2') }}</li>
                <li>{{ __('site.treat.retainer_opt_3') }}</li>
            </ul>
        </li>

    </ul>

    <p class="kx-svc-empty" id="kxSvcEmpty" hidden>{{ __('site.treat.empty') }}</p>

    <div class="kx-svc-foot">
        <p class="kx-svc-foot-text">{{ __('site.treat.foot_text') }}</p>
        <a href="@auth{{ route('booking') }}@else{{ route('login') }}@endauth" class="kx-btn kx-btn-dark">
            {{ __('site.treat.foot_cta') }} <i class='bx bx-right-arrow-alt'></i>
        </a>
    </div>

</section>

<style>
/* ============================================================
   Treatments section — prefix kx-svc-
   Sits inside .kx-shell, inherits the kx colour variables
   ============================================================ */

.kx-svc-filters{ display:flex; flex-wrap:wrap; gap:10px; margin:-28px 0 30px; }

.kx-svc-chip{
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.88rem; font-weight:600;
    color:var(--walnut);
    background:rgba(255,255,255,.7);
    border:1px solid rgba(94,68,46,.14);
    border-radius:999px;
    padding:9px 18px;
    cursor:pointer;
    transition:background .22s ease, border-color .22s ease, color .22s ease, transform .22s ease;
}
.kx-svc-chip:hover{ background:#fff; transform:translateY(-1px); }
.kx-svc-chip.is-active{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }
.kx-svc-chip:focus-visible{ outline:3px solid var(--powder); outline-offset:2px; }

.kx-svc-grid{
    list-style:none; margin:0; padding:0;
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(268px, 1fr));
    gap:18px;
}

.kx-svc-card{
    position:relative;
    overflow:hidden;
    background:var(--vanilla);
    border:1px solid rgba(94,68,46,.08);
    border-left:4px solid var(--powder);
    border-radius:28px;
    padding:28px 26px 26px;
    box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 14px 30px -18px rgba(94,68,46,.4);
    transition:transform .3s ease, box-shadow .3s ease;
}
.kx-svc-card:hover{ transform:translateY(-5px); box-shadow:0 24px 44px -18px rgba(94,68,46,.45); }
.kx-svc-card[hidden]{ display:none; }

.kx-svc-card[data-cat="asas"]      { border-left-color:#8FBF9F; }
.kx-svc-card[data-cat="pemulihan"] { border-left-color:var(--powder); }
.kx-svc-card[data-cat="ganti"]     { border-left-color:#D9A441; }
.kx-svc-card[data-cat="estetik"]   { border-left-color:#C89BB8; }

.kx-svc-mark{
    position:absolute; right:-14px; bottom:-18px;
    width:98px; height:98px;
    fill:rgba(94,68,46,.055);
    pointer-events:none;
}

.kx-svc-cat{
    display:block;
    font-size:.75rem; font-weight:700;
    color:var(--muted);
    margin-bottom:10px;
}

.kx-svc-name{
    font-family:'Fraunces',serif;
    font-size:1.24rem; font-weight:600; line-height:1.25;
    color:var(--walnut);
    margin:0 0 10px;
}

.kx-svc-desc{
    position:relative;
    margin:0;
    font-size:.94rem; line-height:1.6;
    color:#5c4d3f;
}

.kx-svc-opts{
    position:relative;
    margin:10px 0 0; padding-left:18px;
    font-size:.89rem; line-height:1.7;
    color:#6b5b4b;
}
.kx-svc-opts li::marker{ color:rgba(94,68,46,.4); }

.kx-svc-empty{ padding:28px 0; margin:0; color:var(--muted); }

.kx-svc-foot{
    margin-top:30px;
    padding:26px 30px;
    background:rgba(255,255,255,.6);
    border:1px solid rgba(94,68,46,.08);
    border-radius:28px;
    display:flex; flex-wrap:wrap;
    align-items:center; justify-content:space-between;
    gap:18px;
}

.kx-svc-foot-text{
    margin:0;
    max-width:34rem;
    font-size:.96rem; line-height:1.6;
    color:#5c4d3f;
}

@media (max-width:680px){
    .kx-svc-grid{ grid-template-columns:1fr; }
    .kx-svc-filters{ margin-top:-16px; }
    .kx-svc-foot{ flex-direction:column; align-items:flex-start; }
    .kx-svc-foot .kx-btn{ width:100%; }
}
</style>

<script>
(function () {
    var grid = document.getElementById('kxSvcGrid');
    if (!grid) return;

    var chips = document.querySelectorAll('.kx-svc-chip');
    var cards = grid.querySelectorAll('.kx-svc-card');
    var empty = document.getElementById('kxSvcEmpty');

    function applyFilter(value) {
        var shown = 0;

        cards.forEach(function (card) {
            var match = (value === 'all' || card.dataset.cat === value);
            card.hidden = !match;
            if (match) shown++;
        });

        if (empty) empty.hidden = shown > 0;
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) {
                c.classList.remove('is-active');
                c.setAttribute('aria-pressed', 'false');
            });

            chip.classList.add('is-active');
            chip.setAttribute('aria-pressed', 'true');

            applyFilter(chip.dataset.filter);
        });
    });
})();
</script>