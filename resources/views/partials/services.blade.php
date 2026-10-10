{{-- Treatments offered — Klinik Pergigian Dr. Zuraida --}}
{{-- Included from the landing page, inside .kx-shell --}}

@php
    // Category key => lang key for its label.
    $catLabels = [
        'asas'      => 'cat_routine',
        'pemulihan' => 'cat_restore',
        'ganti'     => 'cat_replace',
        'estetik'   => 'cat_appearance',
    ];

    // [lang key prefix, category, number of option bullets]
    // Order here is the order shown on the page.
    $treatments = [
        ['scaling',   'asas',      0],
        ['fluoride',  'asas',      0],
        ['extract',   'asas',      0],

        ['filling',   'pemulihan', 0],
        ['rootcanal', 'pemulihan', 0],
        ['post',      'pemulihan', 0],
        ['crown',     'pemulihan', 3],

        ['bridge',    'ganti',     0],
        ['denture',   'ganti',     2],
        ['frc',       'ganti',     0],
        ['implant',   'ganti',     0],

        ['veneer',    'estetik',   2],
        ['whitening', 'estetik',   0],
        ['braces',    'estetik',   2],
        ['retainer',  'estetik',   3],
    ];

    // Starting prices from the clinic's own printed price list.
    // Treatments not in this list show "price after check-up" instead.
    $prices = config('clinic.prices', []);

    // $bookUrl comes from the landing page. Fall back to login if this
    // partial is ever included somewhere else.
    $svcBookUrl = $bookUrl ?? route('login');
@endphp

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
        @foreach ($catLabels as $cat => $labelKey)
            <button type="button" class="kx-svc-chip" data-filter="{{ $cat }}" aria-pressed="false">{{ __('site.treat.' . $labelKey) }}</button>
        @endforeach
    </div>

    <ul class="kx-svc-grid" id="kxSvcGrid">
        @foreach ($treatments as [$key, $cat, $optCount])
            <li class="kx-svc-card" data-cat="{{ $cat }}">
                <svg class="kx-svc-mark" aria-hidden="true"><use href="#kx-svc-tooth"></use></svg>
                <span class="kx-svc-cat">{{ __('site.treat.' . $catLabels[$cat]) }}</span>
                <h3 class="kx-svc-name">{{ __("site.treat.{$key}_name") }}</h3>
                <p class="kx-svc-desc">{{ __("site.treat.{$key}_desc") }}</p>

                @if ($optCount > 0)
                    <ul class="kx-svc-opts">
                        @for ($i = 1; $i <= $optCount; $i++)
                            <li>{{ __("site.treat.{$key}_opt_{$i}") }}</li>
                        @endfor
                    </ul>
                @endif

                <div class="kx-svc-price-row">
                    @if (isset($prices[$key]))
                        <span class="kx-svc-price">
                            <i class='bx bx-purchase-tag-alt'></i>
                            {{ __('site.treat.price_from', ['amount' => number_format($prices[$key])]) }}
                        </span>
                    @else
                        <span class="kx-svc-price is-ask">
                            <i class='bx bx-search-alt'></i>
                            {{ __('site.treat.price_ask') }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    <p class="kx-svc-empty" id="kxSvcEmpty" hidden>{{ __('site.treat.empty') }}</p>

    @if (! empty($prices))
        <p class="kx-svc-price-note">
            <i class='bx bx-info-circle'></i>
            {{ __('site.treat.price_note', ['date' => config('clinic.prices_updated', '')]) }}
        </p>
    @endif

    <div class="kx-svc-foot">
        <p class="kx-svc-foot-text">{{ __('site.treat.foot_text') }}</p>
        <a href="{{ $svcBookUrl }}" class="kx-btn kx-btn-dark">
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
    display:flex; flex-direction:column;
    background:var(--vanilla);
    border:1px solid rgba(94,68,46,.08);
    border-left:4px solid var(--powder);
    border-radius:28px;
    padding:28px 26px 24px;
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

/* Price tag: the row is pushed to the bottom so tags line up across a row */
.kx-svc-price-row{ position:relative; z-index:1; margin-top:auto; padding-top:18px; }
.kx-svc-price{
    display:inline-flex; align-items:center; gap:6px;
    background:#fff;
    border:1px solid rgba(94,68,46,.14);
    border-radius:999px;
    padding:6px 13px;
    font-size:.86rem; font-weight:700;
    color:var(--walnut);
}
.kx-svc-price i{ font-size:1.05rem; }
.kx-svc-price.is-ask{ background:transparent; border-color:transparent; padding-left:0; font-weight:600; color:var(--muted); }

.kx-svc-price-note{
    display:flex; align-items:flex-start; gap:8px;
    margin:22px 0 0;
    font-size:.84rem; line-height:1.55;
    color:var(--muted);
}
.kx-svc-price-note i{ font-size:1rem; margin-top:2px; flex-shrink:0; }

.kx-svc-empty{ padding:28px 0; margin:0; color:var(--muted); }

.kx-svc-foot{
    margin-top:24px;
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