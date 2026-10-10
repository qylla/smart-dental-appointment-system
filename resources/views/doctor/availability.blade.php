@extends('layouts.app')

@section('title', 'My Availability - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kad">

    @include('doctor.partials.sidebar')

    <main class="kad-main">

        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">My Availability</h1>
                <p class="kad-date">Mark the days or sessions you're away. Patients stop being booked with you for that time straight away.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="kdoc-flash"><i class='bx bx-check-circle'></i> {{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="kav-errors">
                @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        <div class="kav-grid">

            <!-- MARK TIME AWAY -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Mark time away</h2>
                    <p>Leave, a course, or a half day off.</p>
                </div></div>

                <form action="{{ route('doctor.availability.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <label class="kav-field"><span>Date</span>
                        <input type="date" name="date" min="{{ today()->toDateString() }}"
                               value="{{ old('date') }}" required>
                    </label>

                    <div class="kav-field">
                        <span>Which part of the day?</span>
                        <div class="kav-options">
                            @foreach (['' => 'Whole day', 'day' => 'Day session', 'night' => 'Night session'] as $value => $label)
                                <label class="kav-option">
                                    <input type="radio" name="session" value="{{ $value }}"
                                           {{ (string) old('session', '') === (string) $value ? 'checked' : '' }}>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="kav-hint">Day session is 8:30 AM – 5 PM. Night session is 8 PM – 10 PM.</small>
                    </div>

                    <label class="kav-field"><span>Reason (optional)</span>
                        <input type="text" name="reason" maxlength="100" value="{{ old('reason') }}"
                               placeholder="e.g. Annual leave, CPD course">
                    </label>

                    <button type="submit" class="kx-btn kx-btn-dark"><i class='bx bx-calendar-x'></i> Mark as away</button>
                </form>

                <div class="kav-note">
                    <i class='bx bx-info-circle'></i>
                    <p>This doesn't cancel patients who are already booked with you. If any fall in that time, you'll see a warning below. Ask the front desk to reassign them.</p>
                </div>
            </section>

            <!-- UPCOMING TIME AWAY -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Upcoming time away</h2>
                    <p>{{ $leaves->count() }} {{ \Illuminate\Support\Str::plural('entry', $leaves->count()) }}</p>
                </div></div>

                @forelse ($leaves as $leave)
                    <div class="kav-item">
                        <div class="kav-date">
                            <span class="d">{{ $leave->date->format('j') }}</span>
                            <span class="m">{{ $leave->date->format('M') }}</span>
                        </div>
                        <div class="kav-body">
                            <strong>{{ $leave->session_label }}</strong>
                            <span>
                                {{ $leave->date->format('l') }}@if ($leave->reason) &middot; {{ $leave->reason }}@endif
                            </span>
                            @if ($leave->affected > 0)
                                <span class="kav-warn">
                                    <i class='bx bx-error'></i>
                                    {{ $leave->affected }} {{ \Illuminate\Support\Str::plural('patient', $leave->affected) }} still booked with you. Ask the front desk to reassign.
                                </span>
                            @endif
                        </div>
                        <form action="{{ route('doctor.availability.destroy', $leave) }}" method="POST"
                              onsubmit="return confirm('Remove this and make yourself available again?')">
                            @csrf @method('DELETE')
                            <button class="kav-remove" title="Remove"><i class='bx bx-trash'></i></button>
                        </form>
                    </div>
                @empty
                    <div class="kav-empty">
                        <i class='bx bx-calendar-check'></i>
                        <p>No time away planned. You're available for every open slot.</p>
                    </div>
                @endforelse
            </section>

        </div>
    </main>
</div>

<style>
:root{
    --vanilla:#FBF8F3; --buttercream:#EFE6DA; --powder:#B7C4B1;
    --walnut:#5E442E; --walnut-dark:#2f2318; --ink:#3a2c20; --muted:#7a6a5a;
}
.kad{ display:flex; min-height:100vh; background:var(--buttercream); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); }
.kad h1,.kad h2{ font-family:'Fraunces',serif; letter-spacing:-.02em; margin:0; color:var(--walnut); }

/* SIDEBAR (same as the doctor dashboard) */
.kad-side{ width:250px; flex-shrink:0; background:var(--walnut-dark); color:var(--vanilla); padding:24px 18px; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; z-index:60; transition:left .3s ease; }
.kad-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; font-size:1.05rem; color:var(--vanilla); padding:0 8px 26px; }
.kad-logo-dots{ position:relative; display:flex; width:32px; height:20px; align-items:center; }
.kad-logo-dots i{ position:absolute; width:20px; height:20px; border-radius:50%; }
.kad-logo-dots i:first-child{ left:0; background:var(--buttercream); }
.kad-logo-dots i:last-child{ left:12px; background:var(--powder); }
.kad-menu{ display:flex; flex-direction:column; gap:4px; flex:1; }
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(251,248,243,.6); font-weight:600; font-size:.92rem; transition:background .2s ease,color .2s ease; }
.kad-menu-item i{ font-size:1.2rem; }
.kad-menu-item:hover{ background:rgba(251,248,243,.07); color:var(--vanilla); }
.kad-menu-item.active{ background:var(--walnut); color:var(--vanilla); }
.kad-side-foot{ border-top:1px solid rgba(251,248,243,.12); padding-top:18px; }
.kad-user{ display:flex; align-items:center; gap:12px; padding:0 6px 16px; }
.kad-user strong{ display:block; font-size:.88rem; color:var(--vanilla); }
.kad-user span{ font-size:.75rem; color:rgba(251,248,243,.5); }
.kad-avatar{ width:38px; height:38px; flex-shrink:0; border-radius:50%; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.kad-logout{ display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; color:rgba(251,248,243,.6); font-size:.88rem; font-weight:600; width:100%; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kad-logout:hover{ background:rgba(251,248,243,.07); color:var(--vanilla); }
.kad-overlay{ display:none; position:fixed; inset:0; background:rgba(47,35,24,.5); z-index:55; }
.kad-overlay.show{ display:block; }

/* MAIN */
.kad-main{ flex:1; min-width:0; padding:26px 30px 50px; }
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:24px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:4px 0 0; font-size:.88rem; color:var(--muted); max-width:620px; line-height:1.5; }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }

.kdoc-flash{ display:flex; align-items:flex-start; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 18px; font-size:.88rem; font-weight:600; margin-bottom:18px; line-height:1.5; }
.kdoc-flash i{ font-size:1.1rem; margin-top:1px; }
.kav-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:18px; }
.kav-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; }

.kav-grid{ display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.15fr); gap:16px; align-items:start; }

.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:26px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ margin-bottom:20px; }
.kad-panel-head h2{ font-size:1.15rem; font-weight:600; }
.kad-panel-head p{ margin:4px 0 0; font-size:.84rem; color:var(--muted); }
.kad-panel form{ margin:0; }

/* FORM */
.kav-field{ display:flex; flex-direction:column; gap:7px; margin-bottom:18px; }
.kav-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kav-field input[type="date"], .kav-field input[type="text"]{ border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; font-family:inherit; font-size:.92rem; color:var(--ink); background:#fff; outline:none; }
.kav-field input:focus{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kav-hint{ font-size:.78rem; color:var(--muted); }

.kav-options{ display:flex; flex-wrap:wrap; gap:8px; }
.kav-option{ cursor:pointer; }
.kav-option input{ position:absolute; opacity:0; width:0; height:0; }
.kav-option span{ display:inline-flex; padding:10px 16px; border-radius:12px; border:1.5px solid rgba(94,68,46,.22); background:#fff; font-size:.86rem; font-weight:700; color:var(--walnut); transition:all .18s ease; }
.kav-option input:checked + span{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }
.kav-option input:focus-visible + span{ outline:2px solid var(--powder); outline-offset:2px; }

.kx-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; transition:transform .25s ease, background .25s ease; text-decoration:none; }
.kx-btn:hover{ transform:translateY(-2px); }
.kx-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -12px rgba(94,68,46,.8); }
.kx-btn-dark:hover{ background:var(--walnut-dark); }

.kav-note{ display:flex; gap:10px; margin-top:22px; padding:14px 16px; border-radius:14px; background:var(--buttercream); }
.kav-note i{ color:var(--walnut); font-size:1.1rem; margin-top:1px; flex-shrink:0; }
.kav-note p{ margin:0; font-size:.83rem; line-height:1.55; color:#5c4d3f; }

/* LIST */
.kav-item{ display:flex; align-items:flex-start; gap:14px; padding:14px 0; border-bottom:1px solid rgba(94,68,46,.08); }
.kav-item:last-child{ border-bottom:none; }
.kav-date{ width:52px; flex-shrink:0; text-align:center; background:#fff; border:1px solid rgba(94,68,46,.12); border-radius:14px; padding:7px 0; }
.kav-date .d{ display:block; font-family:'Fraunces',serif; font-size:1.2rem; font-weight:600; color:var(--walnut); line-height:1; }
.kav-date .m{ display:block; font-size:.68rem; font-weight:800; text-transform:uppercase; color:var(--muted); margin-top:2px; }
.kav-body{ flex:1; min-width:0; }
.kav-body strong{ display:block; font-size:.92rem; color:var(--ink); }
.kav-body > span{ display:block; font-size:.8rem; color:var(--muted); margin-top:2px; }
.kav-body .kav-warn{ display:flex; align-items:flex-start; gap:6px; margin-top:8px; padding:8px 12px; border-radius:10px; background:#fde3d0; color:#8a4e14; font-size:.8rem; font-weight:600; line-height:1.45; }
.kav-warn i{ font-size:1rem; margin-top:1px; flex-shrink:0; }
.kav-remove{ width:34px; height:34px; border-radius:10px; border:1px solid rgba(94,68,46,.15); background:#fff; color:var(--walnut); font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .18s ease; }
.kav-remove:hover{ background:#a13d3d; border-color:#a13d3d; color:#fff; }
.kav-item form{ margin:0; }

.kav-empty{ text-align:center; padding:36px 16px; color:var(--muted); }
.kav-empty i{ font-size:2.2rem; color:var(--powder); }
.kav-empty p{ margin:10px 0 0; font-size:.9rem; }

@media (max-width:980px){
    .kav-grid{ grid-template-columns:1fr; }
}
@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
}

/* ===== Mobile fixes ===== */
@media (max-width:820px){
    .kad-side{ overflow-y:auto; }
    .kad-main{ padding-bottom:60px; }
    .kad-top{ align-items:flex-start; gap:12px; }
    .kad-top > div{ min-width:0; }
    .kad-title{ font-size:1.4rem; }
    .kad-panel{ padding:20px 16px; border-radius:20px; }
    .kav-field input[type="date"], .kav-field input[type="text"]{ width:100%; min-width:0; font-size:16px; }
    .kav-option{ flex:1 1 auto; }
    .kav-option span{ width:100%; justify-content:center; }
    .kad-panel form > .kx-btn{ width:100%; }
}
</style>

<script>
(function(){
    var side = document.getElementById('kadSide');
    var overlay = document.getElementById('kadOverlay');
    document.getElementById('kadBurger').addEventListener('click', function(){
        side.classList.add('open'); overlay.classList.add('show');
    });
    overlay.addEventListener('click', function(){
        side.classList.remove('open'); overlay.classList.remove('show');
    });
})();
</script>
@endsection