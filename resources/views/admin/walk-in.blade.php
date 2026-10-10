@extends('layouts.app')

@section('title', 'Walk-in / Phone Booking - KPZ Admin')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kad">

    <!-- ===== SIDEBAR ===== -->
       @include('admin.partials.sidebar')

    <!-- ===== MAIN ===== -->
    <main class="kad-main">

        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">Walk-in / phone booking</h1>
                <p class="kad-date">Book on a patient's behalf. The slot is removed from online booking straight away.</p>
            </div>
        </header>

        @if ($errors->any())
            <div class="kset-errors">
                @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        <form action="{{ route('admin.walkin.store') }}" method="POST" id="walkinForm" autocomplete="off">
            @csrf

            <!-- 1. CHANNEL -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>How did they reach the clinic?</h2>
                </div></div>

                <div class="kset-toggle-grid">
                    <label class="kset-toggle">
                        <input type="radio" name="channel" value="walk_in" {{ old('channel', 'walk_in') === 'walk_in' ? 'checked' : '' }}>
                        <span><i class='bx bx-walk'></i> Walk-in at the counter</span>
                    </label>
                    <label class="kset-toggle">
                        <input type="radio" name="channel" value="phone" {{ old('channel') === 'phone' ? 'checked' : '' }}>
                        <span><i class='bx bx-phone'></i> Phone call</span>
                    </label>
                </div>
            </section>

            <!-- 2. PATIENT -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Patient</h2>
                    <p>If this phone number is already registered, the existing patient record is used and the other details here are ignored.</p>
                </div></div>

                <div class="kset-field-row">
                    <label class="kset-field"><span>Full name</span>
                        <div class="kset-input"><i class='bx bxs-user'></i>
                            <input type="text" name="name" value="{{ old('name') }}" required></div>
                    </label>
                    <label class="kset-field"><span>Phone number</span>
                        <div class="kset-input"><i class='bx bxs-phone'></i>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 012-345 6789" required></div>
                    </label>
                </div>

                <div class="kset-field-row">
                    <label class="kset-field"><span>Date of birth (optional)</span>
                        <div class="kset-input"><i class='bx bxs-cake'></i>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ now()->subDay()->toDateString() }}"></div>
                    </label>
                    <label class="kset-field"><span>Email (optional)</span>
                        <div class="kset-input"><i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email') }}" autocomplete="off"></div>
                    </label>
                </div>
            </section>

            <!-- 3. SLOT -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Date &amp; time</h2>
                    <p>Greyed-out times have passed, are fully booked, or are closed.</p>
                </div></div>

                <div class="kwi-dates" id="kwiDates"></div>
                <div id="kwiTimes"></div>

                <input type="hidden" name="appointment_date" id="fDate" value="{{ old('appointment_date') }}">
                <input type="hidden" name="appointment_time" id="fTime" value="{{ old('appointment_time') }}">
            </section>

            <!-- 4. DETAILS -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Details</h2>
                </div></div>

                <div class="kset-field-row">
                    <label class="kset-field"><span>Urgency</span>
                        <div class="kset-input"><i class='bx bxs-flag-alt'></i>
                            <select name="urgency">
                                @foreach (['routine' => 'Routine', 'urgent' => 'Urgent', 'critical' => 'Critical'] as $key => $label)
                                    <option value="{{ $key }}" {{ old('urgency', 'routine') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select></div>
                    </label>
                    <label class="kset-field"><span>Note (optional)</span>
                        <div class="kset-input"><i class='bx bxs-note'></i>
                            <input type="text" name="admin_note" maxlength="1000" value="{{ old('admin_note') }}"
                                   placeholder="e.g. Toothache lower left, wants scaling too"></div>
                    </label>
                </div>

                <div class="kwi-actions">
                    <a href="{{ route('admin.dashboard') }}#appointments" class="kset-btn kset-btn-light">Cancel</a>
                    <button type="submit" class="kset-btn kset-btn-dark" id="submitBtn" disabled>
                        <i class='bx bx-calendar-check'></i> Book appointment
                    </button>
                </div>
            </section>
        </form>

    </main>
</div>

<style>
:root{
    --vanilla:#FBF8F3; --buttercream:#EFE6DA; --powder:#B7C4B1;
    --walnut:#5E442E; --walnut-dark:#2f2318; --ink:#3a2c20; --muted:#7a6a5a;
}
.kad{ display:flex; min-height:100vh; background:var(--buttercream); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); }
.kad h1,.kad h2{ font-family:'Fraunces',serif; letter-spacing:-.02em; margin:0; color:var(--walnut); }

/* SIDEBAR */
.kad-side{ width:250px; flex-shrink:0; background:var(--walnut-dark); color:var(--vanilla); padding:24px 18px; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; z-index:60; transition:left .3s ease; }
.kad-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; font-size:1.05rem; color:var(--vanilla); padding:0 8px 26px; }
.kad-logo-dots{ position:relative; display:flex; width:32px; height:20px; align-items:center; }
.kad-logo-dots i{ position:absolute; width:20px; height:20px; border-radius:50%; }
.kad-logo-dots i:first-child{ left:0; background:var(--buttercream); }
.kad-logo-dots i:last-child{ left:12px; background:var(--powder); }
.kad-menu{ display:flex; flex-direction:column; gap:4px; flex:1; }
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(251,248,243,.6); font-weight:600; font-size:.92rem; transition:background .2s,color .2s; }
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
.kad-main{ flex:1; min-width:0; padding:26px 30px 50px; max-width:980px; }
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:22px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:2px 0 0; font-size:.85rem; color:var(--muted); }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }

/* PANELS */
.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:26px; margin-bottom:16px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ margin-bottom:18px; }
.kad-panel-head h2{ font-size:1.15rem; font-weight:600; }
.kad-panel-head p{ margin:5px 0 0; font-size:.85rem; color:var(--muted); line-height:1.55; }
#walkinForm{ margin:0; }

/* FIELDS */
.kset-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kset-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kset-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; background:#fff; transition:border-color .2s,box-shadow .2s; }
.kset-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kset-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kset-input input, .kset-input select{ border:none; background:none; outline:none; width:100%; font-family:inherit; font-size:.92rem; color:var(--ink); }
.kset-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }

.kset-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kset-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kset-errors p + p{ margin-top:5px; }

/* CHANNEL TOGGLES */
.kset-toggle-grid{ display:flex; flex-wrap:wrap; gap:10px; }
.kset-toggle{ cursor:pointer; }
.kset-toggle input{ position:absolute; opacity:0; width:0; height:0; }
.kset-toggle span{ display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:12px; border:1.5px solid rgba(94,68,46,.25); background:#fff; font-size:.88rem; font-weight:700; color:var(--walnut); transition:all .18s; }
.kset-toggle span i{ font-size:1.15rem; }
.kset-toggle input:checked + span{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }

/* DATE CHIPS */
.kwi-dates{ display:flex; gap:10px; overflow-x:auto; padding-bottom:8px; margin-bottom:20px; }
.kwi-date{ flex-shrink:0; width:64px; padding:11px 0; border-radius:14px; border:1.5px solid rgba(94,68,46,.25); background:#fff; text-align:center; cursor:pointer; font-family:inherit; transition:all .18s ease; }
.kwi-date span{ display:block; }
.kwi-date .dow{ font-size:.7rem; color:#8a7864; font-weight:700; text-transform:uppercase; }
.kwi-date .dnum{ font-family:'Fraunces',serif; font-size:1.3rem; color:var(--walnut); font-weight:600; }
.kwi-date:hover{ border-color:var(--walnut); }
.kwi-date.selected{ background:var(--walnut); border-color:var(--walnut); }
.kwi-date.selected .dow,.kwi-date.selected .dnum{ color:var(--vanilla); }
.kwi-date.booked{ background:#efe6d2; border-color:rgba(94,68,46,.12); cursor:not-allowed; opacity:.55; }
.kwi-date.booked .dow,.kwi-date.booked .dnum{ color:#a89a88; }

/* TIME CHIPS, grouped by session */
.kwi-group + .kwi-group{ margin-top:20px; padding-top:18px; border-top:1px dashed rgba(94,68,46,.2); }
.kwi-group-head{ display:flex; align-items:center; gap:8px; font-size:.78rem; font-weight:700; color:var(--walnut); text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px; }
.kwi-group-head i{ font-size:1.1rem; }
.kwi-group-head .kwi-count{ margin-left:auto; font-size:.78rem; font-weight:600; text-transform:none; letter-spacing:0; color:#8a7864; }
.kwi-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(110px, 1fr)); gap:10px; }
.kwi-time{ padding:11px 0; border-radius:12px; border:1.5px solid rgba(94,68,46,.25); background:#fff; text-align:center; font-weight:700; color:var(--walnut); cursor:pointer; font-size:.88rem; font-family:inherit; transition:all .18s ease; }
.kwi-time:hover{ border-color:var(--walnut); }
.kwi-time.selected{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }
.kwi-time.booked{ background:#f1ece0; color:#b3a494; border-color:transparent; cursor:not-allowed; text-decoration:line-through; }
.kwi-empty{ color:var(--muted); font-size:.88rem; }

/* BUTTONS */
.kwi-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-top:4px; }
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; text-decoration:none; transition:transform .22s,box-shadow .22s,background .22s,opacity .2s; }
.kset-btn:hover{ transform:translateY(-2px); }
.kset-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -14px rgba(94,68,46,.9); }
.kset-btn-dark:hover{ background:var(--walnut-dark); }
.kset-btn-dark:disabled{ opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }
.kset-btn-light{ background:#fff; color:var(--walnut); border-color:rgba(94,68,46,.18); }

@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
}
@media (max-width:540px){
    .kset-field-row{ grid-template-columns:1fr; }
    .kad-title{ font-size:1.4rem; }
}

/* ===== Mobile fixes ===== */
@media (max-width:820px){
    .kad-side{ overflow-y:auto; }
    .kad-top{ align-items:flex-start; gap:12px; margin-bottom:20px; }
    .kad-top > div:not([class]){ min-width:0; }
    .kad-panel{ padding:18px 16px; border-radius:20px; }
    /* 16px stops iOS zooming in when a field is tapped */
    .kset-input input, .kset-input select, .kset-input textarea, .kad-search input{ font-size:16px; }
}
</style>

<script>
(function(){
    /* sidebar */
    var side = document.getElementById('kadSide');
    var overlay = document.getElementById('kadOverlay');
    document.getElementById('kadBurger').addEventListener('click', function(){
        side.classList.add('open'); overlay.classList.add('show');
    });
    overlay.addEventListener('click', function(){
        side.classList.remove('open'); overlay.classList.remove('show');
    });

    /* Same availability data the online booking page uses (SlotService). */
    var AVAILABILITY = @json($availability);

    var fDate = document.getElementById('fDate');
    var fTime = document.getElementById('fTime');
    var submitBtn = document.getElementById('submitBtn');

    /* Values from a failed submit, so staff don't have to pick again. */
    var oldDate = fDate.value;
    var oldTime = fTime.value;

    function checkSubmit(){
        submitBtn.disabled = !(fDate.value && fTime.value);
    }

    function buildDates(){
        var wrap = document.getElementById('kwiDates');
        wrap.innerHTML = '';

        if(!AVAILABILITY.length){
            wrap.innerHTML = '<p class="kwi-empty">No open days in the next two weeks. Check the clinic schedule in Settings.</p>';
            return;
        }

        AVAILABILITY.forEach(function(day){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kwi-date';
            btn.dataset.date = day.date;
            btn.innerHTML = '<span class="dow">' + day.dow + '</span><span class="dnum">' + day.dnum + '</span>';

            if(day.full){
                btn.classList.add('booked');
                btn.disabled = true;
                btn.title = 'No slots available';
            } else {
                btn.addEventListener('click', function(){ selectDate(day, btn); });
            }
            wrap.appendChild(btn);
        });

        /* Reselect the old date after a failed submit, else the first open day (usually today). */
        var target = oldDate ? wrap.querySelector('.kwi-date[data-date="' + oldDate + '"]:not(.booked)') : null;
        if(!target) target = wrap.querySelector('.kwi-date:not(.booked)');
        if(target) target.click();
    }

    function selectDate(day, btn){
        document.querySelectorAll('.kwi-date').forEach(function(c){ c.classList.remove('selected'); });
        btn.classList.add('selected');
        fDate.value = day.date;
        fTime.value = '';
        buildTimes(day);
        checkSubmit();
    }

    function buildTimes(day){
        var wrap = document.getElementById('kwiTimes');
        wrap.innerHTML = '';

        /* Anything before 6 PM counts as the day session. */
        var sessions = [
            { name:'Day session',   icon:'bx-sun',  slots: day.slots.filter(function(s){ return s.time <  '18:00'; }) },
            { name:'Night session', icon:'bx-moon', slots: day.slots.filter(function(s){ return s.time >= '18:00'; }) }
        ];

        sessions.forEach(function(session){
            if(!session.slots.length) return;

            var open = session.slots.filter(function(s){ return !s.booked; }).length;

            var group = document.createElement('div');
            group.className = 'kwi-group';
            group.innerHTML = '<div class="kwi-group-head"><i class="bx ' + session.icon + '"></i> ' + session.name +
                '<span class="kwi-count">' + (open ? open + ' available' : 'No slots left') + '</span></div>';

            var grid = document.createElement('div');
            grid.className = 'kwi-grid';

            session.slots.forEach(function(slot){
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'kwi-time';
                btn.textContent = slot.label;

                if(slot.booked){
                    btn.classList.add('booked');
                    btn.disabled = true;
                    btn.title = slot.past ? 'This time has passed'
                              : (slot.closed ? 'Closed by admin' : 'All dentists booked');
                } else {
                    btn.addEventListener('click', function(){
                        document.querySelectorAll('.kwi-time').forEach(function(c){ c.classList.remove('selected'); });
                        btn.classList.add('selected');
                        fTime.value = slot.time;
                        checkSubmit();
                    });

                    /* Reselect the old time after a failed submit. */
                    if(oldTime && oldTime === slot.time && oldDate === day.date){
                        btn.classList.add('selected');
                        fTime.value = slot.time;
                    }
                }
                grid.appendChild(btn);
            });

            group.appendChild(grid);
            wrap.appendChild(group);
        });

        checkSubmit();
    }

    buildDates();

    /* Stop double submissions. */
    document.getElementById('walkinForm').addEventListener('submit', function(){
        submitBtn.disabled = true;
        submitBtn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Booking...";
    });
})();
</script>
@endsection