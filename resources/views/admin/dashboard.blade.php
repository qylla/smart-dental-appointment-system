@extends('layouts.app')

@section('title', 'Admin Dashboard - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="kad">

    <!-- ===== SIDEBAR ===== -->
    <aside class="kad-side" id="kadSide">
        <a href="{{ route('home') }}" class="kad-logo">
            <span class="kad-logo-dots"><i></i><i></i></span>
            <span>KPZ Admin</span>
        </a>

        <nav class="kad-menu">
            <a href="{{ route('admin.dashboard') }}" class="kad-menu-item active"><i class='bx bx-grid-alt'></i> Dashboard</a>
            <a href="{{ route('admin.dashboard') }}#appointments" class="kad-menu-item"><i class='bx bx-calendar'></i> Appointments</a>
            <a href="{{ route('admin.dashboard') }}#symptoms" class="kad-menu-item"><i class='bx bx-message-dots'></i> Symptom Reports</a>
            <a href="{{ route('admin.patients') }}" class="kad-menu-item"><i class='bx bx-user'></i> Patients</a>
            <a href="{{ route('admin.settings') }}" class="kad-menu-item"><i class='bx bx-cog'></i> Settings</a>
        </nav>

        <div class="kad-side-foot">
            <div class="kad-user">
                <span class="kad-avatar">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') }}</span>
                <div>
                    <strong>{{ auth()->user()->name }}</strong>
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

        <!-- topbar -->
        <header class="kad-top">
            <button class="kad-burger" id="kadBurger"><i class='bx bx-menu'></i></button>
            <div>
                <h1 class="kad-title">Dashboard</h1>
                <p class="kad-date" id="kadDate"></p>
            </div>
            <div class="kad-top-right">
                <button class="kad-icon-btn"><i class='bx bx-bell'></i><span class="kad-dot"></span></button>
                <span class="kad-avatar">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') }}</span>
            </div>
        </header>

        <!-- ===== STAT CARDS ===== -->
        <section class="kad-stats">
            <div class="kad-stat">
                <div class="kad-stat-top">
                    <span class="kad-stat-ico"><i class='bx bx-calendar-check'></i></span>
                    @if ($stats['change'] >= 0)
                        <span class="kad-trend up"><i class='bx bx-up-arrow-alt'></i> {{ $stats['change'] }}%</span>
                    @else
                        <span class="kad-trend down"><i class='bx bx-down-arrow-alt'></i> {{ abs($stats['change']) }}%</span>
                    @endif
                </div>
                <p class="kad-stat-num" data-count="{{ $stats['week'] }}">0</p>
                <p class="kad-stat-cap">Appointments this week</p>
            </div>

            <div class="kad-stat">
                <div class="kad-stat-top">
                    <span class="kad-stat-ico"><i class='bx bx-time-five'></i></span>
                    <span class="kad-trend warn">Needs action</span>
                </div>
                <p class="kad-stat-num" data-count="{{ $stats['pending'] }}">0</p>
                <p class="kad-stat-cap">Pending approval</p>
            </div>

            <div class="kad-stat">
                <div class="kad-stat-top">
                    <span class="kad-stat-ico"><i class='bx bx-error-circle'></i></span>
                    <span class="kad-trend crit">Urgent</span>
                </div>
                <p class="kad-stat-num" data-count="{{ $stats['critical'] }}">0</p>
                <p class="kad-stat-cap">Critical symptom flags</p>
            </div>

            <div class="kad-stat">
                <div class="kad-stat-top">
                    <span class="kad-stat-ico"><i class='bx bx-x-circle'></i></span>
                    <span class="kad-trend down">This week</span>
                </div>
                <p class="kad-stat-num" data-count="{{ $stats['no_show'] }}">0</p>
                <p class="kad-stat-cap">No-shows this week</p>
            </div>
        </section>

        <!-- ===== CHARTS ===== -->
        <section class="kad-charts">
            <div class="kad-panel kad-panel-wide">
                <div class="kad-panel-head">
                    <div>
                        <h2>Bookings over time</h2>
                        <p>Appointments created per day</p>
                    </div>
                    <div class="kad-toggle" id="rangeToggle">
                        <button class="active" data-range="week">Week</button>
                        <button data-range="month">Month</button>
                    </div>
                </div>
                <div class="kad-canvas-wrap"><canvas id="bookingsChart"></canvas></div>
            </div>

            <div class="kad-panel" id="symptoms">
                <div class="kad-panel-head">
                    <div>
                        <h2>Reported symptoms</h2>
                        <p>Most common this month</p>
                    </div>
                </div>
                <div class="kad-canvas-wrap kad-canvas-sm"><canvas id="symptomChart"></canvas></div>
            </div>
        </section>

        <section class="kad-charts">
            <div class="kad-panel">
                <div class="kad-panel-head">
                    <div>
                        <h2>Urgency mix</h2>
                        <p>From symptom checker results</p>
                    </div>
                </div>
                <div class="kad-canvas-wrap kad-canvas-sm"><canvas id="urgencyChart"></canvas></div>
            </div>

            <div class="kad-panel kad-panel-wide">
                <div class="kad-panel-head">
                    <div>
                        <h2>Peak hours</h2>
                        <p>When patients prefer to book</p>
                    </div>
                </div>
                <div class="kad-canvas-wrap"><canvas id="peakChart"></canvas></div>
            </div>
        </section>

        <!-- ===== APPOINTMENTS TABLE ===== -->
        <section class="kad-panel kad-table-panel" id="appointments">
            @if (session('status'))
                <div class="kad-flash"><i class='bx bx-check-circle'></i> {{ session('status') }}</div>
            @endif
            @if ($errors->has('reassign'))
                <div class="kad-flash kad-flash-err"><i class='bx bx-error-circle'></i> {{ $errors->first('reassign') }}</div>
            @endif

            <div class="kad-panel-head">
                <div>
                    <h2>{{ $day === 'today' ? "Today's appointments" : ($day === 'upcoming' ? 'Upcoming appointments' : 'All appointments') }}</h2>
                    <p>Approve, reject, or update status</p>
                </div>
                <div class="kad-head-controls">
                    <div class="kad-filters">
                        <a href="{{ route('admin.dashboard', ['day' => 'today', 'filter' => $filter]) }}#appointments"
                           class="{{ $day === 'today' ? 'active' : '' }}">Today</a>
                        <a href="{{ route('admin.dashboard', ['day' => 'upcoming', 'filter' => $filter]) }}#appointments"
                           class="{{ $day === 'upcoming' ? 'active' : '' }}">Upcoming</a>
                        <a href="{{ route('admin.dashboard', ['day' => 'all', 'filter' => $filter]) }}#appointments"
                           class="{{ $day === 'all' ? 'active' : '' }}">All dates</a>
                    </div>
                    <div class="kad-filters">
                        @foreach (['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed'] as $key => $label)
                            <a href="{{ route('admin.dashboard', ['filter' => $key, 'day' => $day]) }}#appointments"
                               class="{{ $filter === $key ? 'active' : '' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="kad-table-scroll">
                <table class="kad-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date &amp; time</th>
                            <th>Reported symptom</th>
                            <th>Dentist</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $a)
                            <tr>
                                <td>
                                    <div class="kad-patient">
                                        <span class="kad-avatar">{{ strtoupper(substr($a->patient->name, 0, 2)) }}</span>
                                        <div>
                                            <strong>{{ $a->patient->name }}</strong>
                                            <small>{{ $a->patient->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $a->time_label }}</strong><br>
                                    <small>{{ $a->appointment_date->format('D, j M') }}</small>
                                </td>
                                <td>
                                    @if ($a->symptomCheck)
                                        {{ $a->symptomCheck->symptom_list }}
                                    @else
                                        <span class="kad-muted">No symptom check</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($a->status === 'completed' || $a->status === 'cancelled')
                                        <span class="kad-muted">{{ $a->doctor?->name ?? 'Unassigned' }}</span>
                                    @else
                                        <form action="{{ route('admin.appointment.reassign', $a) }}" method="POST" class="kad-reassign">
                                            @csrf @method('PATCH')
                                            <select name="doctor_id" onchange="this.form.submit()">
                                                @foreach ($doctors as $doc)
                                                    <option value="{{ $doc->id }}" {{ $a->doctor_id === $doc->id ? 'selected' : '' }}>
                                                        {{ $doc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td><span class="kad-badge b-{{ $a->urgency }}">{{ ucfirst($a->urgency) }}</span></td>
                                <td><span class="kad-badge s-{{ $a->status }}">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span></td>
                                <td>
                                    <div class="kad-actions">
                                        @if ($a->status !== 'confirmed' && $a->status !== 'completed')
                                            <form action="{{ route('admin.appointment.status', $a) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button class="kad-act ok" title="Confirm"><i class="bx bx-check"></i></button>
                                            </form>
                                        @endif

                                        @if ($a->status !== 'completed')
                                            <form action="{{ route('admin.appointment.status', $a) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button class="kad-act" title="Mark completed"><i class="bx bx-check-double"></i></button>
                                            </form>
                                        @endif

                                        @if ($a->status !== 'cancelled')
                                            <form action="{{ route('admin.appointment.status', $a) }}" method="POST"
                                                  onsubmit="return confirm('Cancel this appointment?')">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button class="kad-act no" title="Cancel"><i class="bx bx-x"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="kad-empty">
                                        <i class='bx bx-calendar-x'></i>
                                        <p>No appointments match this view.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</div>

<style>
:root{
    --vanilla:#FFF8E2;
    --buttercream:#FFEFB9;
    --powder:#BCD1DE;
    --walnut:#5E442E;
    --walnut-dark:#2f2318;
    --ink:#3a2c20;
    --muted:#7a6a5a;
}
.kad{ display:flex; min-height:100vh; background:var(--buttercream); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); }
.kad h1,.kad h2{ font-family:'Fraunces',serif; letter-spacing:-.02em; margin:0; color:var(--walnut); }

/* ===== SIDEBAR ===== */
.kad-side{ width:250px; flex-shrink:0; background:var(--walnut-dark); color:var(--vanilla); padding:24px 18px; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; z-index:60; transition:left .3s ease; }
.kad-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; font-size:1.05rem; color:var(--vanilla); padding:0 8px 26px; }
.kad-logo-dots{ position:relative; display:flex; width:32px; height:20px; align-items:center; }
.kad-logo-dots i{ position:absolute; width:20px; height:20px; border-radius:50%; }
.kad-logo-dots i:first-child{ left:0; background:var(--buttercream); }
.kad-logo-dots i:last-child{ left:12px; background:var(--powder); }

.kad-menu{ display:flex; flex-direction:column; gap:4px; flex:1; }
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(255,248,226,.6); font-weight:600; font-size:.92rem; transition:background .2s ease, color .2s ease; }
.kad-menu-item i{ font-size:1.2rem; }
.kad-menu-item:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }
.kad-menu-item.active{ background:var(--walnut); color:var(--vanilla); }

.kad-side-foot{ border-top:1px solid rgba(255,248,226,.12); padding-top:18px; }
.kad-user{ display:flex; align-items:center; gap:12px; padding:0 6px 16px; }
.kad-user strong{ display:block; font-size:.88rem; color:var(--vanilla); }
.kad-user span{ font-size:.75rem; color:rgba(255,248,226,.5); }
.kad-avatar{ width:38px; height:38px; flex-shrink:0; border-radius:50%; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.kad-logout{ display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; text-decoration:none; color:rgba(255,248,226,.6); font-size:.88rem; font-weight:600; width:100%; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kad-logout:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }

.kad-overlay{ display:none; position:fixed; inset:0; background:rgba(47,35,24,.5); z-index:55; }
.kad-overlay.show{ display:block; }

/* ===== MAIN ===== */
.kad-main{ flex:1; min-width:0; padding:26px 30px 50px; }
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:26px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:2px 0 0; font-size:.85rem; color:var(--muted); }
.kad-top-right{ margin-left:auto; display:flex; align-items:center; gap:12px; }
.kad-icon-btn{ position:relative; width:42px; height:42px; border-radius:50%; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.25rem; cursor:pointer; }
.kad-dot{ position:absolute; top:9px; right:10px; width:8px; height:8px; border-radius:50%; background:#c0603c; border:2px solid var(--vanilla); }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }

/* ===== STAT CARDS ===== */
.kad-stats{ display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px; }
.kad-stat{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:22px; padding:22px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); transition:transform .25s ease, box-shadow .25s ease; }
.kad-stat:hover{ transform:translateY(-4px); box-shadow:0 20px 36px -18px rgba(94,68,46,.5); }
.kad-stat-top{ display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.kad-stat-ico{ width:44px; height:44px; border-radius:14px; background:var(--buttercream); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
.kad-trend{ font-size:.72rem; font-weight:800; padding:5px 10px; border-radius:999px; display:inline-flex; align-items:center; gap:2px; }
.kad-trend.up{ background:#dff0d8; color:#4a7c4a; }
.kad-trend.down{ background:#e6ecef; color:#5c7382; }
.kad-trend.warn{ background:#fde3d0; color:#a8611f; }
.kad-trend.crit{ background:#fbdada; color:#a13d3d; }
.kad-stat-num{ margin:0; font-family:'Fraunces',serif; font-size:2.2rem; font-weight:600; color:var(--walnut); line-height:1; }
.kad-stat-cap{ margin:6px 0 0; font-size:.85rem; color:var(--muted); }

/* ===== PANELS / CHARTS ===== */
.kad-charts{ display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:20px; }
.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:24px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:20px; flex-wrap:wrap; }
.kad-panel-head h2{ font-size:1.15rem; font-weight:600; }
.kad-panel-head p{ margin:3px 0 0; font-size:.82rem; color:var(--muted); }
.kad-canvas-wrap{ height:260px; position:relative; }
.kad-canvas-sm{ height:240px; }

.kad-toggle,.kad-filters{ display:flex; gap:4px; background:var(--buttercream); padding:4px; border-radius:999px; flex-wrap:wrap; }
.kad-toggle button,.kad-filters button{ border:none; background:none; padding:7px 15px; border-radius:999px; font-size:.8rem; font-weight:700; color:var(--walnut); cursor:pointer; font-family:inherit; transition:background .2s ease; }
.kad-toggle button.active,.kad-filters button.active{ background:var(--walnut); color:var(--vanilla); }

/* ===== TABLE ===== */
.kad-table-panel{ padding-bottom:12px; }
.kad-table-scroll{ overflow-x:auto; }
.kad-table{ width:100%; border-collapse:collapse; min-width:720px; }
.kad-table th{ text-align:left; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); padding:0 14px 12px; border-bottom:1px solid rgba(94,68,46,.1); }
.kad-table td{ padding:14px; font-size:.9rem; border-bottom:1px solid rgba(94,68,46,.07); vertical-align:middle; }
.kad-table tr:last-child td{ border-bottom:none; }
.kad-patient{ display:flex; align-items:center; gap:10px; }
.kad-patient .kad-avatar{ width:34px; height:34px; font-size:.75rem; background:var(--buttercream); }
.kad-patient strong{ font-size:.9rem; color:var(--ink); font-weight:700; }

.kad-badge{ display:inline-block; padding:5px 12px; border-radius:999px; font-size:.74rem; font-weight:800; }
.b-routine{ background:#eef3e9; color:#5a7a4a; }
.b-urgent{ background:#fde3d0; color:#a8611f; }
.b-critical{ background:#fbdada; color:#a13d3d; }
.b-pending{ background:var(--buttercream); color:#8a6a2e; }
.b-confirmed{ background:#dbe8ef; color:#3f6377; }
.b-completed{ background:#e3ece0; color:#4a7c4a; }

.kad-actions{ display:flex; gap:6px; }
.kad-act{ width:32px; height:32px; border-radius:10px; border:1px solid rgba(94,68,46,.15); background:#fff; color:var(--walnut); font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .18s ease; }
.kad-act:hover{ background:var(--walnut); color:var(--vanilla); border-color:var(--walnut); }
.kad-act.ok:hover{ background:#4a7c4a; border-color:#4a7c4a; }
.kad-act.no:hover{ background:#a13d3d; border-color:#a13d3d; }

.kad-flash{ display:flex; align-items:center; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 18px; font-size:.88rem; font-weight:600; margin-bottom:20px; }
.kad-head-controls{ display:flex; gap:8px; flex-wrap:wrap; }
.kad-filters a{ padding:7px 15px; border-radius:999px; font-size:.8rem; font-weight:700; color:var(--walnut); text-decoration:none; transition:background .2s ease; }
.kad-filters a.active{ background:var(--walnut); color:var(--vanilla); }
.kad-patient small{ display:block; font-size:.75rem; color:var(--muted); margin-top:1px; }
.kad-table td small{ font-size:.75rem; color:var(--muted); }
.kad-muted{ color:var(--muted); font-size:.85rem; }
.kad-actions form{ margin:0; }
.kad-empty{ text-align:center; padding:40px 20px; }
.kad-empty i{ font-size:2.2rem; color:var(--powder); }
.kad-empty p{ margin:10px 0 0; color:var(--muted); font-size:.9rem; }
.s-pending{ background:var(--buttercream); color:#8a6a2e; }
.s-confirmed{ background:#dbe8ef; color:#3f6377; }
.s-completed{ background:#e3ece0; color:#4a7c4a; }
.s-cancelled,.s-no_show{ background:#efe6e0; color:#8a7060; }

.kad-reassign{ margin:0; }
.kad-reassign select{ border:1px solid rgba(94,68,46,.2); border-radius:10px; padding:6px 10px; font-family:inherit; font-size:.82rem; color:var(--walnut); background:#fff; cursor:pointer; max-width:130px; }
.kad-reassign select:focus{ outline:none; border-color:var(--powder); }
.kad-flash-err{ background:#fbdada; color:#8a2f2f; }

/* ===== RESPONSIVE ===== */
@media (max-width:1100px){
    .kad-stats{ grid-template-columns:repeat(2,1fr); }
    .kad-charts{ grid-template-columns:1fr; }
}
@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
}
@media (max-width:540px){
    .kad-stats{ grid-template-columns:1fr; }
    .kad-title{ font-size:1.4rem; }
}
</style>

<script>
(function(){
    /* ---------- DATE ---------- */
    document.getElementById('kadDate').textContent = new Date().toLocaleDateString('en-GB', {
        weekday:'long', day:'numeric', month:'long', year:'numeric'
    });

    /* ---------- SIDEBAR ---------- */
    var side = document.getElementById('kadSide');
    var overlay = document.getElementById('kadOverlay');
    document.getElementById('kadBurger').addEventListener('click', function(){
        side.classList.add('open'); overlay.classList.add('show');
    });
    overlay.addEventListener('click', function(){
        side.classList.remove('open'); overlay.classList.remove('show');
    });

    /* ---------- COUNT-UP STATS ---------- */
    document.querySelectorAll('.kad-stat-num').forEach(function(el){
        var target = parseInt(el.dataset.count, 10), cur = 0;
        var step = Math.max(1, Math.round(target / 30));
        var timer = setInterval(function(){
            cur += step;
            if(cur >= target){ cur = target; clearInterval(timer); }
            el.textContent = cur;
        }, 30);
    });

    /* ---------- CHART DEFAULTS ---------- */
    var WALNUT = '#5E442E', POWDER = '#BCD1DE', BUTTER = '#FFEFB9', MUTED = '#7a6a5a';
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = MUTED;

    var gridCfg = { color:'rgba(94,68,46,.08)', drawBorder:false };

    /* Bookings over time */
    var CHARTS    = @json($charts);
    var weekData  = CHARTS.bookings.week;
    var monthData = CHARTS.bookings.month;

    var bookingsChart = new Chart(document.getElementById('bookingsChart'), {
        type:'line',
        data:{
            labels: weekData.labels,
            datasets:[{
                label:'Appointments',
                data: weekData.data,
                borderColor: WALNUT,
                backgroundColor:'rgba(94,68,46,.10)',
                borderWidth:3, tension:.4, fill:true,
                pointBackgroundColor: WALNUT, pointBorderColor:'#fff',
                pointBorderWidth:2, pointRadius:5, pointHoverRadius:7
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ y:{ beginAtZero:true, grid:gridCfg }, x:{ grid:{ display:false } } }
        }
    });

    document.querySelectorAll('#rangeToggle button').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('#rangeToggle button').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            var d = btn.dataset.range === 'week' ? weekData : monthData;
            bookingsChart.data.labels = d.labels;
            bookingsChart.data.datasets[0].data = d.data;
            bookingsChart.update();
        });
    });

    /* Symptom distribution */
    new Chart(document.getElementById('symptomChart'), {
        type:'bar',
        data:{
            labels: CHARTS.symptoms.labels,
            datasets:[{
                data: CHARTS.symptoms.data,
                backgroundColor:[WALNUT,'#7a5c3e','#9a7a58',POWDER,BUTTER],
                borderRadius:8, borderSkipped:false, barThickness:18
            }]
        },
        options:{
            indexAxis:'y', responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ x:{ beginAtZero:true, grid:gridCfg }, y:{ grid:{ display:false } } }
        }
    });

    /* Urgency mix */
    new Chart(document.getElementById('urgencyChart'), {
        type:'doughnut',
        data:{
            labels:['Routine','Urgent','Critical'],
            datasets:[{
                data:[CHARTS.urgency.routine, CHARTS.urgency.urgent, CHARTS.urgency.critical],
                backgroundColor:[BUTTER, '#d68c4a', '#b55b5b'],
                borderColor:'#FFF8E2', borderWidth:4, hoverOffset:8
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false, cutout:'62%',
            plugins:{ legend:{ position:'bottom', labels:{ padding:16, usePointStyle:true, pointStyle:'circle' } } }
        }
    });

    /* Peak hours */
    new Chart(document.getElementById('peakChart'), {
        type:'bar',
        data:{
            labels: CHARTS.peak.labels,
            datasets:[{
                data: CHARTS.peak.data,
                backgroundColor: POWDER,
                hoverBackgroundColor: WALNUT,
                borderRadius:10, borderSkipped:false
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ y:{ beginAtZero:true, grid:gridCfg }, x:{ grid:{ display:false } } }
        }
    });

})();
</script>
@endsection