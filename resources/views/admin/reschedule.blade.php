@extends('layouts.app')

@section('title', 'Reschedule Appointment - KPZ Admin')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@php
    $current = [
        'date' => $appointment->appointment_date->toDateString(),
        'time' => substr((string) $appointment->appointment_time, 0, 5),
    ];
@endphp

<div class="kad">

    <!-- ===== SIDEBAR ===== -->
    @include('admin.partials.sidebar')

    <!-- ===== MAIN ===== -->
    <main class="kad-main">

        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">Reschedule appointment</h1>
                <p class="kad-date">For patients who call or message the clinic to change their date or time.</p>
            </div>
        </header>

        @if ($errors->any())
            <div class="kset-errors">
                @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        <!-- 1. CURRENT BOOKING -->
        <section class="kad-panel">
            <div class="kad-panel-head"><div>
                <h2>Current booking</h2>
            </div></div>

            <div class="krs-current">
                <span class="kad-avatar">{{ strtoupper(substr($appointment->patient->name, 0, 2)) }}</span>
                <div class="krs-who">
                    <strong>{{ $appointment->patient->name }}</strong>
                    <small>{{ $appointment->patient->phone }}@if ($appointment->patient->email) &middot; {{ $appointment->patient->email }}@endif</small>
                </div>
                <span class="krs-badge s-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>

                <div class="krs-facts">
                    <div><span>Date</span><strong>{{ $appointment->appointment_date->format('D, j M Y') }}</strong></div>
                    <div><span>Time</span><strong>{{ $appointment->time_label }}</strong></div>
                    <div><span>Dentist</span><strong>{{ $appointment->doctor?->name ?? 'Unassigned' }}</strong></div>
                </div>
            </div>
        </section>

        <form action="{{ route('admin.appointment.reschedule.update', $appointment) }}" method="POST" id="rescheduleForm" autocomplete="off">
            @csrf @method('PATCH')

            <!-- 2. NEW SLOT -->
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>New date &amp; time</h2>
                    <p>Greyed-out times have passed, are fully booked, or are closed. The dashed slot is the current one.</p>
                </div></div>

                <div class="kwi-dates" id="kwiDates"></div>
                <div id="kwiTimes"></div>

                <input type="hidden" name="appointment_date" id="fDate" value="{{ old('appointment_date') }}">
                <input type="hidden" name="appointment_time" id="fTime" value="{{ old('appointment_time') }}">
            </section>

            <!-- 3. CONFIRM -->
            <section class="kad-panel">
                <label class="kset-field"><span>Reason (optional)</span>
                    <div class="kset-input"><i class='bx bxs-note'></i>
                        <input type="text" name="reason" maxlength="255" value="{{ old('reason') }}"
                               placeholder="e.g. Patient called, clashes with work"></div>
                </label>

                <div class="krs-note">
                    <i class='bx bx-info-circle'></i>
                    <span>
                        The patient keeps the same dentist if they're free at the new time.
                        @if ($appointment->status === 'confirmed')
                            Because this booking is confirmed, the patient gets an email with the new time.
                        @else
                            This booking is still pending, so the patient gets the new time in their confirmation email.
                        @endif
                    </span>
                </div>

                <div class="kwi-actions">
                    <a href="{{ route('admin.dashboard', ['day' => 'all']) }}#appointments" class="kset-btn kset-btn-light">Cancel</a>
                    <button type="submit" class="kset-btn kset-btn-dark" id="submitBtn" disabled>
                        <i class='bx bx-calendar-check'></i> Save new time
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
#rescheduleForm{ margin:0; }

/* FIELDS */
.kset-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kset-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kset-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; background:#fff; transition:border-color .2s,box-shadow .2s; }
.kset-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kset-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kset-input input{ border:none; background:none; outline:none; width:100%; font-family:inherit; font-size:.92rem; color:var(--ink); }

.kset-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kset-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kset-errors p + p{ margin-top:5px; }

/* CURRENT APPOINTMENT SUMMARY */
.krs-current{ display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
.krs-current .kad-avatar{ width:48px; height:48px; font-size:.95rem; background:var(--buttercream); }
.krs-who{ flex:1; min-width:160px; }
.krs-who strong{ display:block; font-size:1rem; color:var(--ink); }
.krs-who small{ display:block; font-size:.82rem; color:var(--muted); margin-top:2px; }
.krs-facts{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; width:100%; margin-top:16px; }
.krs-facts div{ background:#fff; border:1px solid rgba(94,68,46,.1); border-radius:14px; padding:12px 14px; min-width:0; }
.krs-facts span{ display:block; font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); font-weight:700; }
.krs-facts strong{ display:block; margin-top:4px; font-size:.9rem; color:var(--ink); overflow-wrap:anywhere; }
.krs-badge{ display:inline-block; padding:5px 12px; border-radius:999px; font-size:.74rem; font-weight:800; }
.s-pending{ background:#FDF1DC; color:#8a6a2e; }
.s-confirmed{ background:#dbe8ef; color:#3f6377; }
.krs-note{ display:flex; gap:10px; margin-top:4px; margin-bottom:18px; padding:12px 14px; border-radius:14px; background:var(--buttercream); font-size:.83rem; line-height:1.5; color:#5c4d3f; }
.krs-note i{ color:var(--walnut); font-size:1.05rem; flex-shrink:0; margin-top:1px; }

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
/* the slot the appointment is in now */
.kwi-time.current, .kwi-date.current:not(.selected){ border-style:dashed; border-color:var(--walnut); }
.kwi-time.current{ background:var(--buttercream); color:var(--walnut); text-decoration:none; cursor:not-allowed; }
.kwi-time.current small{ display:block; font-size:.62rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; opacity:.7; }

/* BUTTONS */
.kwi-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-top:4px; }
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; text-decoration:none; transition:transform .22s,box-shadow .22s,background .22s,opacity .2s; }
.kset-btn:hover{ transform:translateY(-2px); }
.kset-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -14px rgba(94,68,46,.9); }
.kset-btn-dark:hover{ background:var(--walnut-dark); }
.kset-btn-dark:disabled{ opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }
.kset-btn-light{ background:#fff; color:var(--walnut); border-color:rgba(94,68,46,.18); }

@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; overflow-y:auto; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
    .kad-top{ align-items:flex-start; gap:12px; margin-bottom:20px; }
    .kad-top > div:not([class]){ min-width:0; }
    .kad-panel{ padding:18px 16px; border-radius:20px; }
    .kset-input input{ font-size:16px; } /* stops iOS zooming in on focus */
}
@media (max-width:540px){
    .kad-title{ font-size:1.4rem; }
    .krs-facts{ grid-template-columns:1fr 1fr; }
    .kwi-actions .kset-btn{ flex:1 1 100%; }
    .kwi-grid{ grid-template-columns:repeat(3,1fr); gap:8px; }
    .kwi-time{ font-size:.8rem; }
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

    /* Same availability data as online booking and walk-in (SlotService). */
    var AVAILABILITY = @json($availability);
    var CURRENT = @json($current);

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
            if(day.date === CURRENT.date) btn.classList.add('current');

            if(day.full){
                btn.classList.add('booked');
                btn.disabled = true;
                btn.title = 'No slots available';
            } else {
                btn.addEventListener('click', function(){ selectDate(day, btn); });
            }
            wrap.appendChild(btn);
        });

        /* Open the old choice after a failed submit, else the current appointment's day, else the first open day. */
        var target = (oldDate && wrap.querySelector('.kwi-date[data-date="' + oldDate + '"]:not(.booked)'))
                  || wrap.querySelector('.kwi-date[data-date="' + CURRENT.date + '"]:not(.booked)')
                  || wrap.querySelector('.kwi-date:not(.booked)');
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

                var isCurrent = day.date === CURRENT.date && slot.time === CURRENT.time;

                if(isCurrent){
                    /* Where the appointment is now: shown, but not a choice. */
                    btn.classList.add('current');
                    btn.disabled = true;
                    btn.title = 'Current appointment time';
                    btn.innerHTML = slot.label + '<small>Current</small>';
                } else if(slot.booked){
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
    document.getElementById('rescheduleForm').addEventListener('submit', function(){
        submitBtn.disabled = true;
        submitBtn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Saving...";
    });
})();
</script>
@endsection