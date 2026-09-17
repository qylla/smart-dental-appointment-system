@extends('layouts.app')

@section('title', 'Book an Appointment - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kbk">

    <!-- TOP BAR -->
    <div class="kbk-topbar">
        <div class="kbk-wrap kbk-topbar-inner">
            <a href="{{ url('/') }}" class="kbk-back"><i class='bx bx-arrow-back'></i> Back</a>
            <span class="kbk-topbar-title">Book an Appointment</span>
            <span class="kbk-topbar-spacer"></span>
        </div>
    </div>

    <div class="kbk-wrap kbk-main">

        <!-- STEP INDICATOR -->
        <div class="kbk-steps" id="kbkSteps">
            <div class="kbk-step active" data-step="1">
                <div class="kbk-step-dot"><span>1</span><i class='bx bx-check'></i></div>
                <span class="kbk-step-label">Symptom Check</span>
            </div>
            <div class="kbk-step-line"></div>
            <div class="kbk-step" data-step="2">
                <div class="kbk-step-dot"><span>2</span><i class='bx bx-check'></i></div>
                <span class="kbk-step-label">Choose Slot</span>
            </div>
            <div class="kbk-step-line"></div>
            <div class="kbk-step" data-step="3">
                <div class="kbk-step-dot"><span>3</span><i class='bx bx-check'></i></div>
                <span class="kbk-step-label">Confirm</span>
            </div>
        </div>

        <!-- ===== PANEL 1: SYMPTOM CHECKER ===== -->
        <section class="kbk-panel active" id="panel1">
            <div class="kbk-card">
                <span class="kauth-eyebrow">Step 1</span>
                <h2 class="kbk-h2">What's bothering you?</h2>
                <p class="kbk-sub">Select what applies, add details if you like. We'll suggest how soon you should be seen &mdash; this isn't a diagnosis.</p>

                <div class="kbk-chips" id="symptomChips">
                    <button type="button" class="kbk-chip" data-severity="routine">Toothache</button>
                    <button type="button" class="kbk-chip" data-severity="urgent">Bleeding gums</button>
                    <button type="button" class="kbk-chip" data-severity="urgent">Swollen gums</button>
                    <button type="button" class="kbk-chip" data-severity="routine">Tooth sensitivity</button>
                    <button type="button" class="kbk-chip" data-severity="critical">Broken / knocked-out tooth</button>
                    <button type="button" class="kbk-chip" data-severity="critical">Severe pain</button>
                    <button type="button" class="kbk-chip" data-severity="routine">Just a check-up</button>
                </div>

                <label class="kbk-textarea-wrap">
                    <span>Tell us more (optional)</span>
                    <div class="kbk-textarea-box">
                        <textarea id="symptomDetails" rows="3" placeholder="e.g. Pain started 2 days ago, worse when I bite down..."></textarea>
                        <div class="kbk-mic-controls">
                            <button type="button" class="kbk-lang-toggle" id="langToggle" title="Switch voice input language">EN</button>
                            <button type="button" class="kbk-mic" id="micBtn" title="Voice input"><i class='bx bx-microphone'></i></button>
                        </div>
                    </div>
                    <p class="kbk-voice-note" id="voiceNote" style="display:none;"></p>
                </label>

                <button type="button" class="kpz-btn kpz-btn-brown kbk-analyze" id="analyzeBtn">
                    <i class='bx bx-message-dots'></i> Analyze My Symptoms
                </button>
                <p class="kbk-analyzing" id="analyzingNote"><i class='bx bx-loader-alt bx-spin'></i> Checking your symptoms...</p>

                <!-- RESULT -->
                <div class="kbk-result" id="kbkResult">
                    <div class="kbk-result-card" id="resultCard">
                        <div class="kbk-result-tag" id="resultTag">Routine</div>
                        <h4 id="resultTitle">Looks like a routine case</h4>
                        <p id="resultText">A regular appointment slot should work well for this. We'll show you the next available times.</p>
                    </div>
                    <button type="button" class="kpz-btn kpz-btn-brown kbk-continue" id="toStep2">
                        Continue to Booking <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- ===== PANEL 2: SLOT PICKER ===== -->
        <section class="kbk-panel" id="panel2">
            <div class="kbk-card">
                <span class="kauth-eyebrow">Step 2</span>
                <h2 class="kbk-h2">Choose your slot</h2>
                <p class="kbk-sub" id="slotSub">Pick a date, then a time that works for you.</p>

                @if ($errors->has('slot'))
                    <div class="kbk-slot-error"><i class='bx bx-error-circle'></i> {{ $errors->first('slot') }}</div>
                @endif

                <div class="kbk-recommend" id="kbkRecommend" style="display:none;">
                    <i class='bx bx-flag'></i> <span>Based on your symptoms, we've highlighted the earliest slots.</span>
                </div>

                <div class="kbk-dates" id="kbkDates"></div>

                <div class="kbk-times" id="kbkTimes"></div>

                <div class="kbk-step2-actions">
                    <button type="button" class="kpz-btn kpz-btn-ghost" id="backTo1"><i class='bx bx-left-arrow-alt'></i> Back</button>
                    <button type="button" class="kpz-btn kpz-btn-brown kbk-continue" id="toStep3" disabled>
                        Review Booking <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- ===== PANEL 3: CONFIRM ===== -->
        <section class="kbk-panel" id="panel3">
            <div class="kbk-card kbk-confirm-card">
                <span class="kauth-eyebrow">Step 3</span>
                <h2 class="kbk-h2" id="confirmHeading">Review your appointment</h2>
                <p class="kbk-sub" id="confirmSub">Double-check the details below before confirming.</p>

                <div class="kbk-ticket">
                    <div class="kbk-ticket-top">
                        <span class="kauth-eyebrow" style="color:var(--walnut)">Appointment Slip</span>
                        <i class='bx bx-check-circle' style="color:#5c8a5c; font-size:1.2rem;"></i>
                    </div>
                    <p class="kbk-ticket-name">{{ auth()->user()->name }}</p>
                    <div class="kbk-ticket-row"><span>Concern</span><strong id="tSymptom">&mdash;</strong></div>
                    <div class="kbk-ticket-row"><span>Urgency</span><strong class="kpz-tag" id="tUrgency">&mdash;</strong></div>
                    <div class="kbk-ticket-perf"></div>
                    <div class="kbk-ticket-row"><span>Date</span><strong id="tDate">&mdash;</strong></div>
                    <div class="kbk-ticket-row"><span>Time</span><strong id="tTime">&mdash;</strong></div>
                </div>

                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="symptom_check_id" id="fCheckId">
                    <input type="hidden" name="appointment_date" id="fDate">
                    <input type="hidden" name="appointment_time" id="fTime">
                    <input type="hidden" name="patient_note" id="fNote">

                    <div class="kbk-step2-actions" id="confirmActions">
                        <button type="button" class="kpz-btn kpz-btn-ghost" id="backTo2"><i class='bx bx-left-arrow-alt'></i> Back</button>
                        <button type="submit" class="kpz-btn kpz-btn-brown kbk-continue" id="confirmBtn">
                            Confirm Booking <i class='bx bx-calendar-check'></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>
</div>

<style>
:root{
    --vanilla:#FFF8E2;
    --buttercream:#FFEFB9;
    --powder:#BCD1DE;
    --walnut:#5E442E;
    --walnut-dark:#3f2e1f;
    --ink:#3a2c20;
}
.kbk{ min-height:100vh; background:var(--vanilla); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); padding-bottom:60px; }
.kbk-wrap{ max-width:760px; margin:0 auto; padding:0 20px; }

.kbk-topbar{ background:rgba(255,248,226,.9); backdrop-filter:blur(8px); border-bottom:1px solid rgba(94,68,46,.1); position:sticky; top:0; z-index:20; }
.kbk-topbar-inner{ display:flex; align-items:center; justify-content:space-between; height:66px; }
.kbk-back{ display:flex; align-items:center; gap:6px; text-decoration:none; color:var(--walnut); font-weight:700; font-size:.9rem; }
.kbk-back:hover{ color:var(--walnut-dark); }
.kbk-topbar-title{ font-family:'Fraunces',serif; font-weight:600; color:var(--walnut); font-size:1.05rem; }
.kbk-topbar-spacer{ width:60px; }

.kbk-main{ padding-top:36px; }

/* STEP INDICATOR */
.kbk-steps{ display:flex; align-items:center; justify-content:center; margin-bottom:36px; }
.kbk-step{ display:flex; flex-direction:column; align-items:center; gap:8px; opacity:.45; transition:opacity .3s ease; }
.kbk-step.active,.kbk-step.done{ opacity:1; }
.kbk-step-dot{ width:38px; height:38px; border-radius:50%; background:#fff; border:2px solid rgba(94,68,46,.3); display:flex; align-items:center; justify-content:center; font-family:'Fraunces',serif; font-weight:600; color:var(--walnut); position:relative; transition:background .3s ease, border-color .3s ease; }
.kbk-step.active .kbk-step-dot{ background:var(--walnut); color:var(--vanilla); border-color:var(--walnut); }
.kbk-step-dot i{ display:none; font-size:1.1rem; }
.kbk-step.done .kbk-step-dot{ background:var(--powder); border-color:var(--powder); }
.kbk-step.done .kbk-step-dot span{ display:none; }
.kbk-step.done .kbk-step-dot i{ display:block; color:var(--walnut); }
.kbk-step-label{ font-size:.78rem; font-weight:700; color:var(--walnut); white-space:nowrap; }
.kbk-step-line{ width:60px; height:2px; background:rgba(94,68,46,.25); margin:0 6px 22px; }

/* PANELS */
.kbk-panel{ display:none; }
.kbk-panel.active{ display:block; animation:kbkFadeIn .5s ease both; }
@keyframes kbkFadeIn{ from{ opacity:0; transform:translateY(14px); } to{ opacity:1; transform:translateY(0); } }

.kbk-card{ background:#fff; border-radius:22px; padding:36px 32px; box-shadow:0 20px 45px rgba(94,68,46,.1); }
.kauth-eyebrow{ font-size:.72rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--walnut); opacity:.65; }
.kbk-h2{ font-family:'Fraunces',serif; font-size:1.7rem; color:var(--walnut); margin:6px 0 10px; }
.kbk-sub{ color:#5c4d3f; font-size:.92rem; margin-bottom:26px; line-height:1.6; }

/* CHIPS */
.kbk-chips{ display:flex; flex-wrap:wrap; gap:10px; margin-bottom:24px; }
.kbk-chip{ padding:10px 18px; border-radius:999px; border:1.5px solid rgba(94,68,46,.3); background:var(--vanilla); color:var(--walnut); font-weight:600; font-size:.85rem; cursor:pointer; transition:all .18s ease; }
.kbk-chip:hover{ border-color:var(--walnut); }
.kbk-chip.selected{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }

.kbk-textarea-wrap{ display:block; margin-bottom:24px; }
.kbk-textarea-wrap span{ display:block; font-size:.8rem; font-weight:700; color:var(--walnut); margin-bottom:6px; }
.kbk-textarea-box{ position:relative; }
.kbk-textarea-box textarea{ width:100%; border:1px solid rgba(94,68,46,.25); border-radius:14px; padding:14px 92px 14px 16px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.92rem; color:var(--ink); background:var(--vanilla); resize:vertical; }
.kbk-textarea-box textarea:focus{ outline:none; border-color:var(--powder); box-shadow:0 0 0 3px rgba(188,209,222,.4); }
.kbk-mic-controls{ position:absolute; right:10px; top:10px; display:flex; align-items:center; gap:6px; }
.kbk-lang-toggle{ width:34px; height:34px; border-radius:50%; border:1.5px solid rgba(94,68,46,.25); background:var(--vanilla); color:var(--walnut); font-size:.68rem; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s ease, border-color .2s ease; }
.kbk-lang-toggle:hover{ border-color:var(--walnut); }
.kbk-mic{ width:34px; height:34px; border-radius:50%; border:none; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.05rem; transition:background .2s ease; flex-shrink:0; }
.kbk-mic:hover{ background:var(--buttercream); }
.kbk-mic:disabled{ opacity:.4; cursor:not-allowed; }
.kbk-mic.listening{ background:var(--walnut); color:var(--vanilla); animation:kbkPulse 1s ease infinite; }
.kbk-voice-note{ font-size:.78rem; color:#8a7864; margin:6px 0 0; }
@keyframes kbkPulse{ 0%,100%{ box-shadow:0 0 0 0 rgba(94,68,46,.4); } 50%{ box-shadow:0 0 0 8px rgba(94,68,46,0); } }

.kpz-btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:12px 24px; border-radius:999px; font-weight:700; font-size:.92rem; border:2px solid transparent; cursor:pointer; transition:transform .18s ease, box-shadow .18s ease, opacity .2s ease; text-decoration:none; }
.kpz-btn-brown{ background:var(--walnut); color:var(--vanilla); }
.kpz-btn-brown:hover{ background:var(--walnut-dark); box-shadow:0 8px 20px rgba(94,68,46,.3); transform:translateY(-2px); }
.kpz-btn-brown:disabled{ opacity:.4; cursor:not-allowed; transform:none; box-shadow:none; }
.kpz-btn-ghost{ border-color:var(--walnut); color:var(--walnut); background:transparent; }
.kpz-btn-ghost:hover{ background:var(--walnut); color:var(--vanilla); }
.kbk-analyze{ width:100%; padding:14px; }

/* RESULT */
.kbk-result{ display:none; margin-top:26px; animation:kbkFadeIn .4s ease both; }
.kbk-result.show{ display:block; }
.kbk-result-card{ border-radius:16px; padding:22px 24px; background:var(--buttercream); margin-bottom:18px; border-left:5px solid var(--walnut); }
.kbk-result-card.urgent{ background:#fde3d0; border-left-color:#b5651d; }
.kbk-result-card.critical{ background:#fbdada; border-left-color:#a13d3d; }
.kbk-result-card.off-topic{ background:#eee8de; border-left-color:#8a7864; }
.kbk-result-card.off-topic .kbk-result-tag{ background:#8a7864; }
.kbk-result-tag{ display:inline-block; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; background:var(--walnut); color:var(--vanilla); padding:4px 12px; border-radius:999px; margin-bottom:10px; }
.kbk-result-card.urgent .kbk-result-tag{ background:#b5651d; }
.kbk-result-card.critical .kbk-result-tag{ background:#a13d3d; }
.kbk-result-card h4{ font-family:'Fraunces',serif; color:var(--walnut); font-size:1.1rem; margin:0 0 6px; }
.kbk-result-card p{ font-size:.88rem; color:#5c4d3f; margin:0; line-height:1.5; }
.kbk-continue{ width:100%; padding:13px; }

.kbk-slot-error{ display:flex; align-items:center; gap:8px; background:#fbdada; color:#8a2f2f; border-radius:12px; padding:12px 16px; font-size:.88rem; font-weight:600; margin-bottom:18px; }
.kbk-analyzing{ display:none; align-items:center; gap:8px; justify-content:center; font-size:.85rem; color:var(--walnut); margin-top:14px; }
.kbk-analyzing.show{ display:flex; }

/* SLOTS */
.kbk-recommend{ display:flex; align-items:center; gap:8px; background:var(--powder); color:var(--walnut); font-size:.85rem; font-weight:600; padding:10px 16px; border-radius:12px; margin-bottom:20px; }
.kbk-dates{ display:flex; gap:10px; overflow-x:auto; padding-bottom:8px; margin-bottom:22px; }
.kbk-date-chip{ flex-shrink:0; width:64px; padding:12px 0; border-radius:14px; border:1.5px solid rgba(94,68,46,.25); background:var(--vanilla); text-align:center; cursor:pointer; transition:all .18s ease; }
.kbk-date-chip span{ display:block; }
.kbk-date-chip .dow{ font-size:.7rem; color:#8a7864; font-weight:700; text-transform:uppercase; }
.kbk-date-chip .dnum{ font-family:'Fraunces',serif; font-size:1.3rem; color:var(--walnut); font-weight:600; }
.kbk-date-chip:hover{ border-color:var(--walnut); }
.kbk-date-chip.selected{ background:var(--walnut); border-color:var(--walnut); }
.kbk-date-chip.selected .dow,.kbk-date-chip.selected .dnum{ color:var(--vanilla); }
.kbk-date-chip.recommended:not(.selected){ border-color:#b5651d; }
.kbk-date-chip.recommended:not(.selected) .dnum{ color:#b5651d; }

.kbk-times{ display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:30px; }
.kbk-time-chip{ padding:12px 0; border-radius:12px; border:1.5px solid rgba(94,68,46,.25); background:var(--vanilla); text-align:center; font-weight:700; color:var(--walnut); cursor:pointer; font-size:.9rem; transition:all .18s ease; }
.kbk-time-chip:hover{ border-color:var(--walnut); }
.kbk-time-chip.selected{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }
.kbk-time-chip.booked{ background:#f1ece0; color:#b3a494; border-color:transparent; cursor:not-allowed; text-decoration:line-through; }
.kbk-time-chip.recommended:not(.selected):not(.booked){ border-color:#b5651d; color:#b5651d; }

.kbk-step2-actions{ display:flex; justify-content:space-between; gap:14px; }
.kbk-step2-actions .kpz-btn{ flex:1; }

/* TICKET (step 3) */
.kbk-ticket{ position:relative; width:280px; margin:0 auto 30px; background:var(--vanilla); border-radius:16px; padding:24px 22px; box-shadow:0 14px 30px rgba(94,68,46,.14); border:1px dashed rgba(94,68,46,.35); }
.kbk-ticket-top{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.kbk-ticket-name{ font-family:'Fraunces',serif; font-size:1.3rem; color:var(--walnut); margin-bottom:14px; }
.kbk-ticket-row{ display:flex; justify-content:space-between; font-size:.87rem; padding:5px 0; color:#5c4d3f; }
.kbk-ticket-row strong{ color:var(--ink); text-align:right; }
.kpz-tag{ background:var(--powder); color:var(--walnut); padding:2px 10px; border-radius:999px; font-size:.75rem; }
.kbk-ticket-perf{ border-top:1px dashed rgba(94,68,46,.3); margin:12px 0; }

.kbk-success{ display:none; text-align:center; padding:20px 0; }
.kbk-success.show{ display:block; animation:kbkFadeIn .5s ease both; }
.kbk-success-icon{ width:64px; height:64px; border-radius:50%; background:#dff0d8; color:#4a7c4a; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 16px; }
.kbk-success h3{ font-family:'Fraunces',serif; color:var(--walnut); margin-bottom:8px; }
.kbk-success p{ color:#5c4d3f; font-size:.9rem; margin-bottom:22px; }

@media (max-width:600px){
    .kbk-card{ padding:26px 20px; }
    .kbk-step-label{ display:none; }
    .kbk-times{ grid-template-columns:repeat(2,1fr); }
    .kbk-step-line{ width:34px; }
}
</style>

<script>
(function(){
    /* Real availability from the database, injected by BookingController. */
    var AVAILABILITY = @json($availability);

    var state = { symptoms: [], severity: 'routine', checkId: null,
                  date: null, dateLabel: null, time: null, timeLabel: null };

    /* ---------------- STEP INDICATOR ---------------- */
    function setStep(n){
        document.querySelectorAll('.kbk-step').forEach(function(el){
            var s = parseInt(el.dataset.step, 10);
            el.classList.remove('active','done');
            if(s < n) el.classList.add('done');
            if(s === n) el.classList.add('active');
        });
        document.querySelectorAll('.kbk-panel').forEach(function(p){ p.classList.remove('active'); });
        document.getElementById('panel' + n).classList.add('active');
        window.scrollTo({top:0, behavior:'smooth'});
    }

    /* ---------------- STEP 1: SYMPTOMS ---------------- */
    var chips = document.querySelectorAll('.kbk-chip');
    var analyzeBtn = document.getElementById('analyzeBtn');

    chips.forEach(function(chip){
        chip.addEventListener('click', function(){
            chip.classList.toggle('selected');
            var label = chip.textContent.trim();
            var idx = state.symptoms.indexOf(label);
            if(chip.classList.contains('selected') && idx === -1){
                state.symptoms.push(label);
            } else if(!chip.classList.contains('selected') && idx > -1){
                state.symptoms.splice(idx, 1);
            }
        });
    });

    analyzeBtn.addEventListener('click', function(){
        var note = document.getElementById('analyzingNote');
        note.classList.add('show');
        analyzeBtn.disabled = true;
        document.getElementById('kbkResult').classList.remove('show');

        /* The analysis and the saving both happen server-side. */
        fetch('{{ route('booking.symptom') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                symptoms: state.symptoms,
                description: document.getElementById('symptomDetails').value
            })
        })
        .then(function(r){
            if(!r.ok) throw new Error('request failed');
            return r.json();
        })
        .then(function(result){
            note.classList.remove('show');
            analyzeBtn.disabled = false;

            state.severity = result.severity;
            state.checkId  = result.id;
            state.onTopic  = result.on_topic !== false;
            document.getElementById('fCheckId').value = result.id;

            var card = document.getElementById('resultCard');
            card.classList.remove('urgent','critical','off-topic');

            if(!state.onTopic){
                card.classList.add('off-topic');
                document.getElementById('resultTag').textContent = 'Not dental-related';
            } else {
                if(result.severity !== 'routine') card.classList.add(result.severity);
                document.getElementById('resultTag').textContent =
                    result.severity.charAt(0).toUpperCase() + result.severity.slice(1);
            }

            document.getElementById('resultTitle').textContent = result.verdict;
            document.getElementById('resultText').textContent  = result.advice;
            document.getElementById('kbkResult').classList.add('show');

            /* Only let the patient proceed to booking for genuine dental concerns. */
            document.getElementById('toStep2').style.display = state.onTopic ? 'inline-flex' : 'none';
        })
        .catch(function(){
            note.classList.remove('show');
            analyzeBtn.disabled = false;
            alert('Sorry, something went wrong. Please try again.');
        });
    });

    document.getElementById('toStep2').addEventListener('click', function(){
        buildDates();
        document.getElementById('kbkRecommend').style.display =
            (state.severity !== 'routine') ? 'flex' : 'none';
        setStep(2);
    });

    /* ---------------- STEP 2: REAL SLOTS ---------------- */
    function buildDates(){
        var wrap = document.getElementById('kbkDates');
        wrap.innerHTML = '';

        AVAILABILITY.forEach(function(day, i){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kbk-date-chip';

            /* Every slot gone means the clinic is full that day. */
            var full = day.slots.every(function(s){ return s.booked; });
            if(full) btn.classList.add('booked');
            if(state.severity !== 'routine' && i < 2 && !full) btn.classList.add('recommended');

            btn.dataset.index = i;
            btn.innerHTML = '<span class="dow">' + day.dow + '</span>' +
                            '<span class="dnum">' + day.dnum + '</span>';

            if(!full){
                btn.addEventListener('click', function(){
                    document.querySelectorAll('.kbk-date-chip').forEach(function(c){ c.classList.remove('selected'); });
                    this.classList.add('selected');
                    state.date      = day.date;
                    state.dateLabel = day.label;
                    buildTimes(day, i);
                    checkContinue();
                });
            }
            wrap.appendChild(btn);
        });

        /* Preselect the first day that still has room. */
        var first = wrap.querySelector('.kbk-date-chip:not(.booked)');
        if(first) first.click();
    }

    function buildTimes(day, dayIndex){
        var wrap = document.getElementById('kbkTimes');
        wrap.innerHTML = '';
        state.time = null;
        state.timeLabel = null;
        checkContinue();

        day.slots.forEach(function(slot, i){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kbk-time-chip';
            btn.textContent = slot.label;

            if(slot.booked){
                btn.classList.add('booked');
                btn.title = 'Already taken';
            } else {
                if(state.severity !== 'routine' && dayIndex < 2 && i < 2){
                    btn.classList.add('recommended');
                }
                btn.addEventListener('click', function(){
                    document.querySelectorAll('.kbk-time-chip').forEach(function(c){ c.classList.remove('selected'); });
                    this.classList.add('selected');
                    state.time      = slot.time;
                    state.timeLabel = slot.label;
                    checkContinue();
                });
            }
            wrap.appendChild(btn);
        });
    }

    function checkContinue(){
        document.getElementById('toStep3').disabled = !(state.date && state.time);
    }

    document.getElementById('backTo1').addEventListener('click', function(){ setStep(1); });

    /* ---------------- STEP 3: REVIEW ---------------- */
    document.getElementById('toStep3').addEventListener('click', function(){
        document.getElementById('tSymptom').textContent =
            state.symptoms.length ? state.symptoms.join(', ') : 'General visit';
        document.getElementById('tUrgency').textContent =
            state.severity.charAt(0).toUpperCase() + state.severity.slice(1);
        document.getElementById('tDate').textContent = state.dateLabel;
        document.getElementById('tTime').textContent = state.timeLabel;

        /* Fill the hidden fields the form actually posts. */
        document.getElementById('fDate').value = state.date;
        document.getElementById('fTime').value = state.time;
        document.getElementById('fNote').value = document.getElementById('symptomDetails').value;

        setStep(3);
    });

    document.getElementById('backTo2').addEventListener('click', function(){ setStep(2); });

    /* Stop double submissions on a slow connection. */
    document.getElementById('bookingForm').addEventListener('submit', function(){
        var btn = document.getElementById('confirmBtn');
        btn.disabled = true;
        btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Booking...";
    });

    /* ---------------- VOICE INPUT (Web Speech API) ---------------- */
    var micBtn = document.getElementById('micBtn');
    var langToggle = document.getElementById('langToggle');
    var voiceNote = document.getElementById('voiceNote');
    var detailsBox = document.getElementById('symptomDetails');

    var currentLang = 'en-US'; // 'en-US' or 'ms-MY'
    var recognition = null;
    var isListening = false;
    var baseText = '';

    var SpeechRecognitionImpl = window.SpeechRecognition || window.webkitSpeechRecognition;

    function showVoiceNote(msg){
        voiceNote.textContent = msg;
        voiceNote.style.display = 'block';
    }

    langToggle.addEventListener('click', function(){
        currentLang = currentLang === 'en-US' ? 'ms-MY' : 'en-US';
        langToggle.textContent = currentLang === 'en-US' ? 'EN' : 'BM';
        if(recognition) recognition.lang = currentLang;
    });

    if(!SpeechRecognitionImpl){
        micBtn.disabled = true;
        showVoiceNote('Voice input isn\'t supported in this browser. Try Chrome or Edge, or just type instead.');
    } else {
        recognition = new SpeechRecognitionImpl();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = currentLang;

        recognition.addEventListener('result', function(event){
            var finalTranscript = '';
            var interimTranscript = '';

            for(var i = event.resultIndex; i < event.results.length; i++){
                var transcript = event.results[i][0].transcript;
                if(event.results[i].isFinal){
                    finalTranscript += transcript + ' ';
                } else {
                    interimTranscript += transcript;
                }
            }

            var separator = baseText && !baseText.endsWith(' ') ? ' ' : '';
            detailsBox.value = baseText + separator + finalTranscript + interimTranscript;

            if(finalTranscript){
                baseText = detailsBox.value.trim();
            }
        });

        recognition.addEventListener('error', function(event){
            isListening = false;
            micBtn.classList.remove('listening');
            if(event.error === 'not-allowed' || event.error === 'permission-denied'){
                showVoiceNote('Microphone access was blocked. Please allow it in your browser settings to use voice input.');
            } else if(event.error === 'no-speech'){
                showVoiceNote('Didn\'t catch that — try again, or type instead.');
            } else {
                showVoiceNote('Voice input had a problem. Please try again or type instead.');
            }
        });

        recognition.addEventListener('end', function(){
            isListening = false;
            micBtn.classList.remove('listening');
        });

        micBtn.addEventListener('click', function(){
            if(isListening){
                recognition.stop();
                isListening = false;
                micBtn.classList.remove('listening');
                return;
            }

            baseText = detailsBox.value.trim();
            voiceNote.style.display = 'none';
            recognition.lang = currentLang;

            try {
                recognition.start();
                isListening = true;
                micBtn.classList.add('listening');
            } catch(e){
                showVoiceNote('Could not start voice input. Please try again.');
            }
        });
    }

    /* If the slot was taken while reviewing, drop the patient back on step 2. */
    @if ($errors ->has('slot'))
        buildDates();
        setStep(2);
    @endif
})();
</script>
@endsection