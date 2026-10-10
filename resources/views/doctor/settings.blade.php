@extends('layouts.app')

@section('title', 'Settings - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@php
    /* The controller validates profile and password with the same error bag,
       so split the messages by field to show each one in the right panel. */
    $profileErrors  = collect(['name', 'email', 'phone', 'specialisation'])->flatMap(fn ($f) => $errors->get($f));
    $passwordErrors = collect(['current_password', 'password'])->flatMap(fn ($f) => $errors->get($f));

    $flash = [
        'profile-updated'       => 'Profile updated.',
        'password-updated'      => 'Password changed.',
        'notifications-updated' => 'Notification preferences saved.',
    ][session('status')] ?? (session('status') ? 'Saved.' : null);

    $openTab = session('status') === 'notifications-updated' ? 'notifications' : 'profile';
@endphp

<div class="kad">

    <!-- ===== SIDEBAR ===== -->
    @include('doctor.partials.sidebar')

    <!-- ===== MAIN ===== -->
    <main class="kad-main">

        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">Settings</h1>
                <p class="kad-date">Manage your profile, password and email notifications</p>
            </div>
        </header>

        @if ($flash)
            <div class="kad-flash"><i class='bx bx-check-circle'></i> {{ $flash }}</div>
        @endif

        <!-- TABS -->
        <div class="kset-tabs" id="setTabs">
            <button class="{{ $openTab === 'profile' ? 'active' : '' }}" data-tab="profile">My profile</button>
            <button class="{{ $openTab === 'notifications' ? 'active' : '' }}" data-tab="notifications">Notifications</button>
        </div>

        <!-- ============ PROFILE ============ -->
        <div class="kset-pane {{ $openTab === 'profile' ? 'active' : '' }}" id="tab-profile">
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Your details</h2>
                    <p>Shown to the front desk, and your specialisation appears under your name in the sidebar.</p>
                </div></div>

                @if ($profileErrors->isNotEmpty())
                    <div class="kset-errors">
                        @foreach ($profileErrors as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ route('doctor.settings.profile') }}" method="POST">
                    @csrf @method('PATCH')
                    <label class="kset-field"><span>Full name</span>
                        <div class="kset-input"><i class='bx bxs-user'></i>
                            <input type="text" name="name" value="{{ old('name', $doctor->name) }}" required></div>
                    </label>
                    <label class="kset-field"><span>Email</span>
                        <div class="kset-input"><i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email', $doctor->email) }}" required></div>
                    </label>
                    <div class="kset-field-row">
                        <label class="kset-field"><span>Phone</span>
                            <div class="kset-input"><i class='bx bxs-phone'></i>
                                <input type="text" name="phone" value="{{ old('phone', $doctor->phone) }}"></div>
                        </label>
                        <label class="kset-field"><span>Specialisation</span>
                            <div class="kset-input"><i class='bx bxs-badge-check'></i>
                                <input type="text" name="specialisation" value="{{ old('specialisation', $doctor->specialisation) }}"
                                       placeholder="e.g. General Dentistry"></div>
                        </label>
                    </div>
                    <button type="submit" class="kset-btn kset-btn-dark">Save profile</button>
                </form>
            </section>

            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Change password</h2>
                    <p>Use at least 8 characters.</p>
                </div></div>

                @if ($passwordErrors->isNotEmpty())
                    <div class="kset-errors">
                        @foreach ($passwordErrors as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ route('doctor.settings.password') }}" method="POST">
                    @csrf @method('PUT')
                    <label class="kset-field"><span>Current password</span>
                        <div class="kset-input"><i class='bx bxs-lock-alt'></i>
                            <input type="password" name="current_password" autocomplete="current-password" required></div>
                    </label>
                    <label class="kset-field"><span>New password</span>
                        <div class="kset-input"><i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password" autocomplete="new-password" required></div>
                    </label>
                    <label class="kset-field"><span>Confirm new password</span>
                        <div class="kset-input"><i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password_confirmation" autocomplete="new-password" required></div>
                    </label>
                    <button type="submit" class="kset-btn kset-btn-dark">Update password</button>
                </form>
            </section>
        </div>

        <!-- ============ NOTIFICATIONS ============ -->
        <div class="kset-pane {{ $openTab === 'notifications' ? 'active' : '' }}" id="tab-notifications">
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Email notifications</h2>
                    <p>Sent to {{ $doctor->email }}.</p>
                </div></div>

                <form action="{{ route('doctor.settings.notifications') }}" method="POST">
                    @csrf @method('PATCH')

                    <label class="kset-switch-row">
                        <span class="kset-switch-text">
                            <strong>New appointment</strong>
                           <small>An email when a patient is booked with you, or when one of your appointments is moved.</small>e
                        </span>
                        <input type="checkbox" name="notify_new_appointment" value="1" @checked($doctor->notify_new_appointment)>
                        <span class="kset-switch" aria-hidden="true"></span>
                    </label>

                    {{-- These two are stored but no email uses them yet, so they are shown as coming soon.
                         The hidden inputs keep the saved value unchanged when the form is submitted. --}}
                    <div class="kset-switch-row is-soon">
                        <span class="kset-switch-text">
                            <strong>Cancellations <em class="kset-soon">Coming soon</em></strong>
                            <small>An email when one of your appointments is cancelled.</small>
                        </span>
                        <input type="hidden" name="notify_cancellation" value="{{ $doctor->notify_cancellation ? 1 : 0 }}">
                        <span class="kset-switch" aria-hidden="true"></span>
                    </div>

                    <div class="kset-switch-row is-soon">
                        <span class="kset-switch-text">
                            <strong>Daily summary <em class="kset-soon">Coming soon</em></strong>
                            <small>Tomorrow's appointments in one email each evening.</small>
                        </span>
                        <input type="hidden" name="notify_daily_summary" value="{{ $doctor->notify_daily_summary ? 1 : 0 }}">
                        <span class="kset-switch" aria-hidden="true"></span>
                    </div>

                    <button type="submit" class="kset-btn kset-btn-dark">Save preferences</button>
                </form>
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

/* SIDEBAR (same as the other doctor pages) */
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
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:22px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:2px 0 0; font-size:.85rem; color:var(--muted); }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }
.kad-flash{ display:flex; align-items:center; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 18px; font-size:.88rem; font-weight:600; margin-bottom:20px; }

/* PANELS */
.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:26px; margin-bottom:16px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ margin-bottom:22px; }
.kad-panel-head h2{ font-size:1.2rem; font-weight:600; }
.kad-panel-head p{ margin:5px 0 0; font-size:.85rem; color:var(--muted); line-height:1.55; }
.kad-panel form{ margin:0; }

/* TABS */
.kset-tabs{ display:flex; gap:4px; background:var(--vanilla); padding:5px; border-radius:999px; margin-bottom:20px; flex-wrap:wrap; border:1px solid rgba(94,68,46,.08); width:fit-content; }
.kset-tabs button{ border:none; background:none; padding:10px 22px; border-radius:999px; font-size:.87rem; font-weight:700; color:var(--walnut); cursor:pointer; font-family:inherit; transition:background .2s; }
.kset-tabs button.active{ background:var(--walnut); color:var(--vanilla); }
.kset-pane{ display:none; }
.kset-pane.active{ display:block; animation:ksetFade .35s ease both; }
@keyframes ksetFade{ from{ opacity:0; transform:translateY(8px); } to{ opacity:1; transform:none; } }

/* FIELDS */
.kset-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kset-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kset-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; background:#fff; transition:border-color .2s,box-shadow .2s; }
.kset-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kset-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kset-input input{ border:none; background:none; outline:none; width:100%; min-width:0; font-family:inherit; font-size:.92rem; color:var(--ink); }
.kset-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }

.kset-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kset-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kset-errors p + p{ margin-top:5px; }

/* NOTIFICATION SWITCHES */
.kset-switch-row{ display:flex; align-items:center; gap:16px; padding:16px 0; border-bottom:1px solid rgba(94,68,46,.08); cursor:pointer; position:relative; }
.kset-switch-row:last-of-type{ margin-bottom:22px; }
.kset-switch-text{ flex:1; min-width:0; }
.kset-switch-text strong{ display:flex; align-items:center; flex-wrap:wrap; gap:8px; font-size:.92rem; color:var(--ink); }
.kset-switch-text small{ display:block; margin-top:3px; font-size:.82rem; color:var(--muted); line-height:1.5; }
.kset-switch-row input[type="checkbox"]{ position:absolute; opacity:0; width:0; height:0; }
.kset-switch{ position:relative; width:46px; height:26px; flex-shrink:0; border-radius:999px; background:rgba(94,68,46,.2); transition:background .2s; }
.kset-switch::after{ content:''; position:absolute; top:3px; left:3px; width:20px; height:20px; border-radius:50%; background:#fff; box-shadow:0 2px 4px rgba(47,35,24,.25); transition:transform .2s; }
.kset-switch-row input:checked + .kset-switch{ background:var(--walnut); }
.kset-switch-row input:checked + .kset-switch::after{ transform:translateX(20px); }
.kset-switch-row input:focus-visible + .kset-switch{ outline:3px solid var(--powder); outline-offset:2px; }
.kset-switch-row.is-soon{ cursor:default; }
.kset-switch-row.is-soon .kset-switch-text strong,
.kset-switch-row.is-soon .kset-switch-text small{ opacity:.55; }
.kset-switch-row.is-soon .kset-switch{ opacity:.35; }
.kset-soon{ font-style:normal; font-size:.66rem; font-weight:800; letter-spacing:.06em; text-transform:uppercase; background:var(--buttercream); color:var(--walnut); padding:3px 9px; border-radius:999px; }

/* BUTTONS */
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; text-decoration:none; transition:transform .22s,box-shadow .22s,background .22s; }
.kset-btn:hover{ transform:translateY(-2px); }
.kset-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -14px rgba(94,68,46,.9); }
.kset-btn-dark:hover{ background:var(--walnut-dark); }

@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; overflow-y:auto; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 60px; }
    .kad-top{ align-items:flex-start; gap:12px; margin-bottom:20px; }
    .kad-top > div{ min-width:0; }
    .kad-panel{ padding:18px 16px; border-radius:20px; }
    .kset-input input{ font-size:16px; } /* stops iOS zooming in on focus */
}
@media (max-width:540px){
    .kad-title{ font-size:1.4rem; }
    .kset-field-row{ grid-template-columns:1fr; gap:0; }
    .kset-tabs{ width:100%; display:grid; grid-template-columns:repeat(2,1fr); border-radius:16px; }
    .kset-tabs button{ padding:9px 6px; font-size:.82rem; border-radius:12px; }
    .kad-panel form > .kset-btn{ width:100%; }
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

    /* tabs */
    document.querySelectorAll('#setTabs button').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('#setTabs button').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            document.querySelectorAll('.kset-pane').forEach(function(p){ p.classList.remove('active'); });
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });
})();
</script>
@endsection