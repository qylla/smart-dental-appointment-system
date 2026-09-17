@extends('layouts.app')

@section('title', 'Settings - KPZ Admin')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kad">

    <!-- ===== SIDEBAR ===== -->
    <aside class="kad-side" id="kadSide">
        <a href="{{ route('home') }}" class="kad-logo">
            <span class="kad-logo-dots"><i></i><i></i></span>
            <span>KPZ Admin</span>
        </a>

        <nav class="kad-menu">
            <a href="{{ route('admin.dashboard') }}" class="kad-menu-item"><i class='bx bx-grid-alt'></i> Dashboard</a>
            <a href="{{ route('admin.dashboard') }}#appointments" class="kad-menu-item"><i class='bx bx-calendar'></i> Appointments</a>
            <a href="{{ route('admin.dashboard') }}#symptoms" class="kad-menu-item"><i class='bx bx-message-dots'></i> Symptom Reports</a>
            <a href="{{ route('admin.settings') }}" class="kad-menu-item active"><i class='bx bx-cog'></i> Settings</a>
        </nav>

        <div class="kad-side-foot">
            <div class="kad-user">
                <span class="kad-avatar">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                <div>
                    <strong>{{ $admin->name }}</strong>
                    <span>Administrator</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="kad-logout"><i class='bx bx-log-out'></i> Log out</button>
            </form>
        </div>
    </aside>

    <div class="kad-overlay" id="kadOverlay"></div>

    <!-- ===== MAIN ===== -->
    <main class="kad-main">

        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">Settings</h1>
                <p class="kad-date">Manage your profile, clinic schedule and staff</p>
            </div>
        </header>

        @if (session('status'))
            <div class="kad-flash"><i class='bx bx-check-circle'></i> {{ session('status') }}</div>
        @endif

        <!-- TABS -->
        <div class="kset-tabs" id="setTabs">
            <button class="active" data-tab="profile">My profile</button>
            <button data-tab="schedule">Clinic schedule</button>
            <button data-tab="staff">Staff</button>
        </div>

        <!-- ============ PROFILE ============ -->
        <div class="kset-pane active" id="tab-profile">
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Your details</h2>
                    <p>Your name and how patients reach the front desk.</p>
                </div></div>

                @if ($errors->getBag('default')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('default')->all() as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.settings.profile') }}" method="POST">
                    @csrf @method('PATCH')
                    <label class="kset-field"><span>Full name</span>
                        <div class="kset-input"><i class='bx bxs-user'></i>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required></div>
                    </label>
                    <label class="kset-field"><span>Email</span>
                        <div class="kset-input"><i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required></div>
                    </label>
                    <label class="kset-field"><span>Phone</span>
                        <div class="kset-input"><i class='bx bxs-phone'></i>
                            <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}"></div>
                    </label>
                    <button type="submit" class="kset-btn kset-btn-dark">Save profile</button>
                </form>
            </section>

            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Change password</h2>
                    <p>Use at least 8 characters.</p>
                </div></div>

                @if ($errors->getBag('password')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('password')->all() as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.settings.password') }}" method="POST">
                    @csrf @method('PUT')
                    <label class="kset-field"><span>Current password</span>
                        <div class="kset-input"><i class='bx bxs-lock-alt'></i>
                            <input type="password" name="current_password" required></div>
                    </label>
                    <label class="kset-field"><span>New password</span>
                        <div class="kset-input"><i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password" required></div>
                    </label>
                    <label class="kset-field"><span>Confirm new password</span>
                        <div class="kset-input"><i class='bx bxs-lock-open-alt'></i>
                            <input type="password" name="password_confirmation" required></div>
                    </label>
                    <button type="submit" class="kset-btn kset-btn-dark">Update password</button>
                </form>
            </section>
        </div>

        <!-- ============ SCHEDULE ============ -->
        <div class="kset-pane" id="tab-schedule">
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Clinic schedule</h2>
                    <p>These control which days and times patients can book. Changes apply to new bookings right away.</p>
                </div></div>

                @if ($errors->getBag('default')->any() && session('tab') === 'schedule')
                    <div class="kset-errors">
                        @foreach ($errors->getBag('default')->all() as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.settings.schedule') }}" method="POST">
                    @csrf @method('PATCH')

                    <div class="kset-block">
                        <span class="kset-block-label">Open days</span>
                        <div class="kset-toggle-grid">
                            @foreach ([1,2,3,4,5,6,0] as $d)
                                <label class="kset-toggle">
                                    <input type="checkbox" name="open_days[]" value="{{ $d }}"
                                           {{ in_array($d, $openDays) ? 'checked' : '' }}>
                                    <span>{{ substr($dayNames[$d], 0, 3) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="kset-block">
                        <span class="kset-block-label">Available time slots</span>
                        <div class="kset-toggle-grid">
                            @foreach ($allSlots as $slot)
                                <label class="kset-toggle">
                                    <input type="checkbox" name="slots[]" value="{{ $slot }}"
                                           {{ in_array($slot, $slots) ? 'checked' : '' }}>
                                    <span>{{ date('g:i A', strtotime($slot)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="kset-btn kset-btn-dark">Save schedule</button>
                </form>
            </section>
        </div>

        <!-- ============ STAFF ============ -->
        <div class="kset-pane" id="tab-staff">
            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Staff accounts</h2>
                    <p>Doctors and admins. Patients register themselves and are not shown here.</p>
                </div></div>

                @if ($errors->getBag('staff')->any())
                    <div class="kset-errors">
                        @foreach ($errors->getBag('staff')->all() as $e) <p>{{ $e }}</p> @endforeach
                    </div>
                @endif

                <div class="kad-table-scroll">
                    <table class="kad-table">
                        <thead><tr><th>Name</th><th>Role</th><th>Email</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach ($staff as $member)
                                <tr>
                                    <td><div class="kad-patient">
                                        <span class="kad-avatar">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                        <div><strong>{{ $member->name }}</strong>
                                            <small>{{ $member->specialisation ?? $member->phone }}</small></div>
                                    </div></td>
                                    <td><span class="kad-badge {{ $member->role === 'doctor' ? 'b-urgent' : 'b-routine' }}">
                                        {{ ucfirst($member->role) }}</span></td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        @if ($member->id === $admin->id)
                                            <span class="kad-muted">You</span>
                                        @else
                                            <form action="{{ route('admin.settings.staff.destroy', $member) }}" method="POST"
                                                  onsubmit="return confirm('Remove {{ $member->name }}?')">
                                                @csrf @method('DELETE')
                                                <button class="kad-act no" title="Remove"><i class="bx bx-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="kad-panel">
                <div class="kad-panel-head"><div>
                    <h2>Add a staff member</h2>
                    <p>Creates a login for a new doctor or admin.</p>
                </div></div>

                <form action="{{ route('admin.settings.staff.store') }}" method="POST" id="staffForm">
                    @csrf
                    <div class="kset-field-row">
                        <label class="kset-field"><span>Full name</span>
                            <div class="kset-input"><i class='bx bxs-user'></i>
                                <input type="text" name="name" value="{{ old('name') }}" required></div>
                        </label>
                        <label class="kset-field"><span>Role</span>
                            <div class="kset-input"><i class='bx bxs-id-card'></i>
                                <select name="role" id="staffRole">
                                    <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select></div>
                        </label>
                    </div>

                    <div class="kset-field-row">
                        <label class="kset-field"><span>Email</span>
                            <div class="kset-input"><i class='bx bxs-envelope'></i>
                                <input type="email" name="email" value="{{ old('email') }}" required></div>
                        </label>
                        <label class="kset-field"><span>Phone</span>
                            <div class="kset-input"><i class='bx bxs-phone'></i>
                                <input type="text" name="phone" value="{{ old('phone') }}"></div>
                        </label>
                    </div>

                    <label class="kset-field" id="specField">
                        <span>Specialisation</span>
                        <div class="kset-input"><i class='bx bxs-been-here'></i>
                            <input type="text" name="specialisation" value="{{ old('specialisation') }}"
                                   placeholder="e.g. General Dentistry"></div>
                    </label>

                    <div class="kset-field-row">
                        <label class="kset-field"><span>Password</span>
                            <div class="kset-input"><i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password" required></div>
                        </label>
                        <label class="kset-field"><span>Confirm password</span>
                            <div class="kset-input"><i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password_confirmation" required></div>
                        </label>
                    </div>

                    <button type="submit" class="kset-btn kset-btn-dark"><i class='bx bx-plus'></i> Add staff member</button>
                </form>
            </section>
        </div>

    </main>
</div>

<style>
:root{
    --vanilla:#FFF8E2; --buttercream:#FFEFB9; --powder:#BCD1DE;
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
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(255,248,226,.6); font-weight:600; font-size:.92rem; transition:background .2s,color .2s; }
.kad-menu-item i{ font-size:1.2rem; }
.kad-menu-item:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }
.kad-menu-item.active{ background:var(--walnut); color:var(--vanilla); }
.kad-side-foot{ border-top:1px solid rgba(255,248,226,.12); padding-top:18px; }
.kad-user{ display:flex; align-items:center; gap:12px; padding:0 6px 16px; }
.kad-user strong{ display:block; font-size:.88rem; color:var(--vanilla); }
.kad-user span{ font-size:.75rem; color:rgba(255,248,226,.5); }
.kad-avatar{ width:38px; height:38px; flex-shrink:0; border-radius:50%; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.kad-logout{ display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; color:rgba(255,248,226,.6); font-size:.88rem; font-weight:600; width:100%; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kad-logout:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }
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
.kset-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(188,209,222,.4); }
.kset-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kset-input input, .kset-input select{ border:none; background:none; outline:none; width:100%; font-family:inherit; font-size:.92rem; color:var(--ink); }
.kset-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }

.kset-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kset-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kset-errors p + p{ margin-top:5px; }

/* SCHEDULE TOGGLES */
.kset-block{ margin-bottom:26px; }
.kset-block-label{ display:block; font-size:.8rem; font-weight:800; color:var(--walnut); margin-bottom:12px; }
.kset-toggle-grid{ display:flex; flex-wrap:wrap; gap:10px; }
.kset-toggle{ cursor:pointer; }
.kset-toggle input{ position:absolute; opacity:0; width:0; height:0; }
.kset-toggle span{ display:inline-flex; align-items:center; justify-content:center; padding:11px 18px; border-radius:12px; border:1.5px solid rgba(94,68,46,.25); background:#fff; font-size:.85rem; font-weight:700; color:var(--walnut); transition:all .18s; }
.kset-toggle input:checked + span{ background:var(--walnut); border-color:var(--walnut); color:var(--vanilla); }

/* BUTTONS */
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; gap:7px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; text-decoration:none; transition:transform .22s,box-shadow .22s,background .22s; }
.kset-btn:hover{ transform:translateY(-2px); }
.kset-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -14px rgba(94,68,46,.9); }
.kset-btn-dark:hover{ background:var(--walnut-dark); }

/* TABLE */
.kad-table-scroll{ overflow-x:auto; }
.kad-table{ width:100%; border-collapse:collapse; min-width:520px; }
.kad-table th{ text-align:left; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); padding:0 14px 12px; border-bottom:1px solid rgba(94,68,46,.1); }
.kad-table td{ padding:14px; font-size:.9rem; border-bottom:1px solid rgba(94,68,46,.07); vertical-align:middle; }
.kad-table tr:last-child td{ border-bottom:none; }
.kad-patient{ display:flex; align-items:center; gap:10px; }
.kad-patient .kad-avatar{ width:34px; height:34px; font-size:.75rem; background:var(--buttercream); }
.kad-patient strong{ font-size:.9rem; color:var(--ink); font-weight:700; }
.kad-patient small{ display:block; font-size:.75rem; color:var(--muted); }
.kad-badge{ display:inline-block; padding:5px 12px; border-radius:999px; font-size:.74rem; font-weight:800; }
.b-routine{ background:#eef3e9; color:#5a7a4a; }
.b-urgent{ background:#fde3d0; color:#a8611f; }
.kad-muted{ color:var(--muted); font-size:.85rem; }
.kad-act{ width:32px; height:32px; border-radius:10px; border:1px solid rgba(94,68,46,.15); background:#fff; color:var(--walnut); font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .18s; }
.kad-act.no:hover{ background:#a13d3d; border-color:#a13d3d; color:#fff; }
.kad-table form{ margin:0; }

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

    /* only show specialisation when adding a doctor */
    var roleSel = document.getElementById('staffRole');
    var specField = document.getElementById('specField');
    function toggleSpec(){ specField.style.display = roleSel.value === 'doctor' ? 'flex' : 'none'; }
    roleSel.addEventListener('change', toggleSpec);
    toggleSpec();

    /* reopen the tab that had the last action or an error */
    var tab = @json(session('tab', ''));
    if(tab){
        var b = document.querySelector('#setTabs button[data-tab="' + tab + '"]');
        if(b) b.click();
    }
})();
</script>
@endsection