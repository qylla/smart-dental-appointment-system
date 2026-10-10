@extends('layouts.app')

@section('title', 'Account Settings - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kset">

    <!-- TOP BAR -->
    <div class="kset-top">
        <div class="kset-wrap kset-top-inner">
            <a href="{{ route('home') }}" class="kset-back"><i class='bx bx-arrow-back'></i> Back</a>
            <span class="kset-top-title">Account Settings</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="kset-logout"><i class='bx bx-log-out'></i> Log out</button>
            </form>
        </div>
    </div>

    <div class="kset-wrap kset-main">

        @if (session('status'))
            <div class="kset-flash"><i class='bx bx-check-circle'></i> {{ session('status') }}</div>
        @endif

        <!-- PROFILE HEADER -->
        <div class="kset-hero">
            <span class="kset-av">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            <div>
                <h1 class="kset-h1">{{ $user->name }}</h1>
                <p class="kset-hero-sub">{{ $user->email }} &middot; Patient since {{ $user->created_at->format('M Y') }}</p>
            </div>
            <a href="{{ route('booking') }}" class="kset-btn kset-btn-dark">
                <i class='bx bx-calendar-plus'></i> Book appointment
            </a>
        </div>

        <!-- QUICK SUMMARY -->
        <div class="kset-summary">
            <div class="kset-sum">
                <span class="kset-sum-ico"><i class='bx bx-calendar-check'></i></span>
                <div>
                    <p class="kset-sum-num">{{ $appointments->count() }}</p>
                    <p class="kset-sum-cap">Total appointments</p>
                </div>
            </div>
            <div class="kset-sum">
                <span class="kset-sum-ico"><i class='bx bx-time-five'></i></span>
                <div>
                    <p class="kset-sum-num">{{ $appointments->whereIn('status', ['pending','confirmed'])->count() }}</p>
                    <p class="kset-sum-cap">Upcoming</p>
                </div>
            </div>
            <div class="kset-sum">
                <span class="kset-sum-ico"><i class='bx bx-check-double'></i></span>
                <div>
                    <p class="kset-sum-num">{{ $appointments->where('status', 'completed')->count() }}</p>
                    <p class="kset-sum-cap">Completed visits</p>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="kset-tabs" id="setTabs">
            <button class="active" data-tab="profile">Profile</button>
            <button data-tab="appointments">My appointments</button>
            <button data-tab="security">Security</button>
        </div>

        <!-- ============ PROFILE ============ -->
        <div class="kset-pane active" id="tab-profile">
            <section class="kset-card">
                <div class="kset-card-head">
                    <h2>Your details</h2>
                    <p>The clinic uses these to contact you about appointments.</p>
                </div>

                @if ($errors->getBag('default')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('default')->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('settings.profile') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <label class="kset-field">
                        <span>Full name</span>
                        <div class="kset-input">
                            <i class='bx bxs-user'></i>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </label>

                    <label class="kset-field">
                        <span>Email address</span>
                        <div class="kset-input">
                            <i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </label>

                    <label class="kset-field">
                        <span>Phone number</span>
                        <div class="kset-input">
                            <i class='bx bxs-phone'></i>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        </div>
                    </label>

                     <label class="kset-field">
                        <span>Date of birth</span>
                        @if ($user->date_of_birth)
                            <div class="kset-input kset-input-locked">
                                <i class='bx bxs-cake'></i>
                                <input type="text" value="{{ $user->date_of_birth->format('j F Y') }}" disabled>
                                <i class='bx bxs-lock-alt'></i>
                            </div>
                            <small class="kset-field-hint">
                                You are {{ $user->age }} years old. If this is wrong, please contact the clinic to correct it.
                            </small>
                        @else
                            <div class="kset-input">
                                <i class='bx bxs-cake'></i>
                                <input type="date" name="date_of_birth"
                                       value="{{ old('date_of_birth') }}"
                                       max="{{ now()->subDay()->toDateString() }}">
                            </div>
                            <small class="kset-field-hint">
                                Please check carefully. Once saved, it can't be changed here.
                            </small>
                        @endif
                    </label>

                    <button type="submit" class="kset-btn kset-btn-dark">Save changes</button>
                </form>
            </section>

            @if ($lastCheck)
                <section class="kset-card">
                    <div class="kset-card-head">
                        <h2>Your last symptom check</h2>
                        <p>Submitted {{ $lastCheck->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="kset-check kset-check-{{ $lastCheck->severity }}">
                        <span class="kset-tag">{{ ucfirst($lastCheck->severity) }}</span>
                        <p class="kset-check-verdict">{{ $lastCheck->verdict }}</p>
                        <p class="kset-check-advice">{{ $lastCheck->advice }}</p>
                        <div class="kset-chips">
                            @foreach ($lastCheck->symptoms as $s)
                                <span>{{ $s }}</span>
                            @endforeach
                        </div>
                    </div>

                    <p class="kset-hint">
                        This is guidance only, not a diagnosis. A dentist confirms everything during your visit.
                    </p>
                </section>
            @endif
        </div>

        <!-- ============ APPOINTMENTS ============ -->
        <div class="kset-pane" id="tab-appointments">
            <section class="kset-card">
                <div class="kset-card-head">
                    <h2>Appointment history</h2>
                    <p>Everything you've booked with the clinic.</p>
                </div>

                @forelse ($appointments as $a)
                    <div class="kset-appt">
                        <div class="kset-appt-date">
                            <span class="d">{{ $a->appointment_date->format('d') }}</span>
                            <span class="m">{{ $a->appointment_date->format('M') }}</span>
                        </div>
                        <div class="kset-appt-body">
                            <strong>{{ $a->time_label }}</strong>
                            <span>
                                @if ($a->symptomCheck)
                                    {{ $a->symptomCheck->symptom_list }}
                                @else
                                    General visit
                                @endif
                            </span>
                        </div>
                        <div class="kset-appt-tags">
                            <span class="kset-badge b-{{ $a->urgency }}">{{ ucfirst($a->urgency) }}</span>
                            <span class="kset-badge s-{{ $a->status }}">{{ ucfirst(str_replace('_',' ',$a->status)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="kset-empty">
                        <i class='bx bx-calendar-x'></i>
                        <p>You haven't booked anything yet.</p>
                        <a href="{{ route('booking') }}" class="kset-btn kset-btn-dark">Book your first appointment</a>
                    </div>
                @endforelse
            </section>
        </div>

        <!-- ============ SECURITY ============ -->
        <div class="kset-pane" id="tab-security">
            <section class="kset-card">
                <div class="kset-card-head">
                    <h2>Change password</h2>
                    <p>Use at least 8 characters.</p>
                </div>

                @if ($errors->getBag('password')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('password')->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('settings.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="kset-field">
                        <span>Current password</span>
                        <div class="kset-input">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="current_password" required>
                        </div>
                    </label>

                    <label class="kset-field">
                        <span>New password</span>
                        <div class="kset-input">
                            <i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password" required>
                        </div>
                    </label>

                    <label class="kset-field">
                        <span>Confirm new password</span>
                        <div class="kset-input">
                            <i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password_confirmation" required>
                        </div>
                    </label>

                    <button type="submit" class="kset-btn kset-btn-dark">Update password</button>
                </form>
            </section>

            <section class="kset-card kset-danger">
                <div class="kset-card-head">
                    <h2>Delete account</h2>
                    <p>This removes your account and all your appointment records. It cannot be undone.</p>
                </div>

                @if ($errors->getBag('delete')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('delete')->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <button type="button" class="kset-btn kset-btn-danger" id="showDelete">
                    <i class='bx bx-trash'></i> Delete my account
                </button>

                <form action="{{ route('settings.destroy') }}" method="POST" id="deleteForm" style="display:none">
                    @csrf
                    @method('DELETE')

                    <label class="kset-field">
                        <span>Type your password to confirm</span>
                        <div class="kset-input">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="delete_password" required>
                        </div>
                    </label>

                    <div class="kset-danger-row">
                        <button type="button" class="kset-btn kset-btn-light" id="cancelDelete">Cancel</button>
                        <button type="submit" class="kset-btn kset-btn-danger">Yes, delete permanently</button>
                    </div>
                </form>
            </section>
        </div>

    </div>
</div>

<style>
:root{
    --vanilla:#FBF8F3;
    --buttercream:#EFE6DA;
    --powder:#B7C4B1;
    --walnut:#5E442E;
    --walnut-dark:#2f2318;
    --ink:#3a2c20;
    --muted:#7a6a5a;
}
.kset{ min-height:100vh; background:var(--buttercream); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); padding-bottom:60px; }
.kset-wrap{ max-width:820px; margin:0 auto; padding:0 20px; }
.kset h1,.kset h2{ font-family:'Fraunces',serif; letter-spacing:-.02em; margin:0; color:var(--walnut); }

.kset-top{ background:rgba(239,230,218,.92); backdrop-filter:blur(8px); border-bottom:1px solid rgba(94,68,46,.1); position:sticky; top:0; z-index:20; }
.kset-top-inner{ display:flex; align-items:center; justify-content:space-between; height:66px; }
.kset-back{ display:flex; align-items:center; gap:6px; text-decoration:none; color:var(--walnut); font-weight:700; font-size:.9rem; }
.kset-top-title{ font-family:'Fraunces',serif; font-weight:600; color:var(--walnut); font-size:1.05rem; }
.kset-top form{ margin:0; }
.kset-logout{ display:flex; align-items:center; gap:6px; background:none; border:none; color:var(--walnut); font-weight:700; font-size:.9rem; cursor:pointer; font-family:inherit; }
.kset-logout:hover{ color:var(--walnut-dark); }

.kset-main{ padding-top:32px; }

.kset-flash{ display:flex; align-items:center; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:14px 18px; font-size:.9rem; font-weight:600; margin-bottom:22px; }
.kset-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kset-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kset-errors p + p{ margin-top:5px; }

/* HERO */
.kset-hero{ display:flex; align-items:center; gap:18px; background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:24px 26px; margin-bottom:16px; flex-wrap:wrap; }
.kset-av{ width:64px; height:64px; flex-shrink:0; border-radius:50%; background:var(--walnut); color:var(--vanilla); display:flex; align-items:center; justify-content:center; font-family:'Fraunces',serif; font-size:1.4rem; font-weight:600; }
.kset-h1{ font-size:1.6rem; font-weight:600; }
.kset-hero-sub{ margin:4px 0 0; font-size:.87rem; color:var(--muted); }
.kset-hero .kset-btn{ margin-left:auto; }

/* SUMMARY */
.kset-summary{ display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
.kset-sum{ display:flex; align-items:center; gap:13px; background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:20px; padding:16px 18px; }
.kset-sum-ico{ width:42px; height:42px; flex-shrink:0; border-radius:13px; background:var(--buttercream); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-size:1.25rem; }
.kset-sum-num{ margin:0; font-family:'Fraunces',serif; font-size:1.5rem; font-weight:600; color:var(--walnut); line-height:1; }
.kset-sum-cap{ margin:3px 0 0; font-size:.78rem; color:var(--muted); }

/* TABS */
.kset-tabs{ display:flex; gap:4px; background:var(--vanilla); padding:5px; border-radius:999px; margin-bottom:20px; flex-wrap:wrap; border:1px solid rgba(94,68,46,.08); }
.kset-tabs button{ border:none; background:none; padding:10px 20px; border-radius:999px; font-size:.87rem; font-weight:700; color:var(--walnut); cursor:pointer; font-family:inherit; transition:background .2s ease; }
.kset-tabs button.active{ background:var(--walnut); color:var(--vanilla); }

.kset-pane{ display:none; }
.kset-pane.active{ display:block; animation:ksetFade .35s ease both; }
@keyframes ksetFade{ from{ opacity:0; transform:translateY(8px); } to{ opacity:1; transform:none; } }

/* CARDS */
.kset-card{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:28px; margin-bottom:16px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kset-card-head{ margin-bottom:22px; }
.kset-card-head h2{ font-size:1.2rem; font-weight:600; }
.kset-card-head p{ margin:5px 0 0; font-size:.86rem; color:var(--muted); line-height:1.55; }
.kset-card form{ margin:0; }

/* FIELDS */
.kset-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kset-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kset-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; background:#fff; transition:border-color .2s ease, box-shadow .2s ease; }
.kset-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kset-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kset-input input{ border:none; background:none; outline:none; width:100%; font-family:inherit; font-size:.92rem; color:var(--ink); }
.kset-field-hint{ font-size:.78rem; color:var(--muted); }
.kset-input-locked{ background:#f5efe3; }
.kset-input-locked input{ color:var(--muted); cursor:not-allowed; }

/* BUTTONS */
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; text-decoration:none; transition:transform .22s ease, box-shadow .22s ease, background .22s ease; }
.kset-btn:hover{ transform:translateY(-2px); }
.kset-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -14px rgba(94,68,46,.9); }
.kset-btn-dark:hover{ background:var(--walnut-dark); color:var(--vanilla); }
.kset-btn-light{ background:#fff; color:var(--walnut); border-color:rgba(94,68,46,.18); }
.kset-btn-danger{ background:#a13d3d; color:#fff; }
.kset-btn-danger:hover{ background:#8a2f2f; }

/* SYMPTOM CHECK BLOCK */
.kset-check{ border-radius:16px; padding:20px 22px; background:var(--buttercream); border-left:5px solid var(--walnut); }
.kset-check-urgent{ background:#fde3d0; border-left-color:#b5651d; }
.kset-check-critical{ background:#fbdada; border-left-color:#a13d3d; }
.kset-tag{ display:inline-block; font-size:.7rem; font-weight:800; letter-spacing:.07em; text-transform:uppercase; background:var(--walnut); color:var(--vanilla); padding:4px 12px; border-radius:999px; }
.kset-check-urgent .kset-tag{ background:#b5651d; }
.kset-check-critical .kset-tag{ background:#a13d3d; }
.kset-check-verdict{ margin:12px 0 5px; font-family:'Fraunces',serif; font-size:1.05rem; font-weight:600; color:var(--walnut); }
.kset-check-advice{ margin:0; font-size:.88rem; color:#5c4d3f; line-height:1.55; }
.kset-chips{ display:flex; flex-wrap:wrap; gap:7px; margin-top:14px; }
.kset-chips span{ background:#fff; border:1px solid rgba(94,68,46,.15); padding:6px 14px; border-radius:999px; font-size:.8rem; font-weight:600; color:var(--walnut); }
.kset-hint{ margin:16px 0 0; font-size:.8rem; color:var(--muted); line-height:1.5; }

/* APPOINTMENTS */
.kset-appt{ display:flex; align-items:center; gap:16px; padding:14px 0; border-bottom:1px solid rgba(94,68,46,.09); flex-wrap:wrap; }
.kset-appt:last-child{ border-bottom:none; }
.kset-appt-date{ width:52px; flex-shrink:0; text-align:center; background:#fff; border:1px solid rgba(94,68,46,.12); border-radius:14px; padding:8px 0; }
.kset-appt-date .d{ display:block; font-family:'Fraunces',serif; font-size:1.2rem; font-weight:600; color:var(--walnut); line-height:1; }
.kset-appt-date .m{ display:block; font-size:.68rem; font-weight:800; text-transform:uppercase; color:var(--muted); margin-top:2px; }
.kset-appt-body{ flex:1; min-width:140px; }
.kset-appt-body strong{ display:block; font-size:.92rem; color:var(--ink); }
.kset-appt-body span{ display:block; font-size:.82rem; color:var(--muted); margin-top:2px; }
.kset-appt-tags{ display:flex; gap:6px; flex-wrap:wrap; }
.kset-badge{ padding:5px 12px; border-radius:999px; font-size:.72rem; font-weight:800; }
.b-routine{ background:#eef3e9; color:#5a7a4a; }
.b-urgent{ background:#fde3d0; color:#a8611f; }
.b-critical{ background:#fbdada; color:#a13d3d; }
.s-pending{ background:#FDF1DC; color:#8a6a2e; }
.s-confirmed{ background:#dbe8ef; color:#3f6377; }
.s-completed{ background:#e3ece0; color:#4a7c4a; }
.s-cancelled,.s-no_show{ background:#efe6e0; color:#8a7060; }

.kset-empty{ text-align:center; padding:40px 20px; }
.kset-empty i{ font-size:2.4rem; color:var(--powder); }
.kset-empty p{ margin:12px 0 20px; color:var(--muted); font-size:.92rem; }

/* DANGER */
.kset-danger{ border-color:rgba(161,61,61,.25); }
.kset-danger h2{ color:#a13d3d; }
.kset-danger-row{ display:flex; gap:10px; flex-wrap:wrap; }

@media (max-width:640px){
    .kset-summary{ grid-template-columns:1fr; }
    .kset-hero{ flex-direction:column; align-items:flex-start; }
    .kset-hero .kset-btn{ margin-left:0; width:100%; }
    .kset-card{ padding:22px 18px; }
    .kset-tabs button{ padding:9px 14px; font-size:.82rem; }
}

/* ===== Mobile fixes ===== */
@media (max-width:640px){
    .kset{ padding-bottom:100px; }
    .kset-wrap{ padding:0 14px; }
    .kset-top-inner{ height:58px; gap:10px; }
    .kset-top-title{ font-size:.95rem; }
    .kset-main{ padding-top:20px; }
    .kset-hero{ padding:20px; border-radius:20px; }
    .kset-hero > div{ min-width:0; width:100%; }
    .kset-hero-sub{ overflow-wrap:anywhere; } /* long emails */
    .kset-h1{ font-size:1.35rem; }
    /* keep the 3 stats side by side instead of 3 tall rows */
    .kset-summary{ grid-template-columns:repeat(3,1fr); gap:8px; }
    .kset-sum{ flex-direction:column; align-items:center; text-align:center; gap:8px; padding:14px 6px; border-radius:16px; }
    .kset-sum-ico{ width:36px; height:36px; font-size:1.1rem; border-radius:11px; }
    .kset-sum-num{ font-size:1.3rem; }
    .kset-sum-cap{ font-size:.72rem; line-height:1.3; }
    .kset-tabs{ flex-wrap:nowrap; border-radius:16px; }
    .kset-tabs button{ flex:1; padding:9px 6px; font-size:.78rem; line-height:1.25; border-radius:12px; }
    .kset-card{ padding:20px 16px; }
    .kset-input input{ font-size:16px; } /* stops iOS zooming in on focus */
    .kset-card form > .kset-btn{ width:100%; }
    .kset-appt{ gap:12px; }
    .kset-appt-tags{ width:100%; padding-left:64px; }
    .kset-danger-row .kset-btn, #showDelete{ width:100%; }
}
@media (max-width:360px){
    .kset-top-title{ display:none; } /* the page heading already says it */
}
</style>

<script>
(function(){
    /* tabs */
    document.querySelectorAll('#setTabs button').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('#setTabs button').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            document.querySelectorAll('.kset-pane').forEach(function(p){ p.classList.remove('active'); });
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    /* reveal the delete confirmation only when asked for */
    var showBtn = document.getElementById('showDelete');
    var form = document.getElementById('deleteForm');
    showBtn.addEventListener('click', function(){
        showBtn.style.display = 'none';
        form.style.display = 'block';
    });
    document.getElementById('cancelDelete').addEventListener('click', function(){
        form.style.display = 'none';
        showBtn.style.display = 'inline-flex';
    });

    /* if a password or delete error came back, open the security tab */
    @if ($errors->getBag('password')->any() || $errors->getBag('delete')->any())
        document.querySelector('#setTabs button[data-tab="security"]').click();
    @endif
    @if ($errors->getBag('delete')->any())
        showBtn.click();
    @endif
})();
</script>
@endsection