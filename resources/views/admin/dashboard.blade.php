@extends('layouts.app')

@section('title', 'Admin Dashboard - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="kad">

     @include('admin.partials.sidebar')

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
                                <div class="kad-notif" id="kadNotif">
                    <button type="button" class="kad-icon-btn" id="kadNotifBtn"
                            aria-label="Notifications" aria-expanded="false">
                        <i class='bx bx-bell'></i>
                        @if (count($notifications ?? []))
                            <span class="kad-notif-count">{{ count($notifications) }}</span>
                        @endif
                    </button>

                    <div class="kad-notif-panel" id="kadNotifPanel">
                        <div class="kad-notif-head">Notifications</div>

                        @forelse ($notifications ?? [] as $n)
                            <a href="{{ $n['url'] }}" class="kad-notif-item">
                                <span class="kad-notif-ico t-{{ $n['tone'] }}"><i class='bx {{ $n['icon'] }}'></i></span>
                                <span class="kad-notif-text">
                                    <strong>{{ $n['title'] }}</strong>
                                    <small>{{ $n['text'] }}</small>
                                </span>
                            </a>
                        @empty
                            <div class="kad-notif-empty">
                                <i class='bx bx-check-circle'></i>
                                <p>All caught up. Nothing needs your attention.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
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

        <!-- ===== PATIENT AGE SPREAD + BOOKING CHANNELS ===== -->
        <section class="kad-charts">
            <div class="kad-panel" id="ages">
                <div class="kad-panel-head">
                    <div>
                        <h2>Patient age groups</h2>
                        <p>Worked out from date of birth, so it stays current</p>
                    </div>
                </div>
                <div class="kad-canvas-wrap"><canvas id="ageChart"></canvas></div>
            </div>

            <div class="kad-panel" id="channels">
                <div class="kad-panel-head">
                    <div>
                        <h2>Booking channels</h2>
                        <p>How appointments reach the clinic</p>
                    </div>
                </div>
                <div class="kad-canvas-wrap kad-canvas-sm"><canvas id="channelChart"></canvas></div>
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
                <a href="{{ route('admin.walkin') }}" class="kad-new-btn"><i class='bx bx-plus'></i> Walk-in / phone booking</a>
            </div>

            <div class="kad-head-controls kad-head-controls-block">
                <div class="kad-filters">
                    <a href="{{ route('admin.dashboard', ['day' => 'today', 'filter' => $filter, 'channel' => $channel]) }}#appointments"
                       class="{{ $day === 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('admin.dashboard', ['day' => 'upcoming', 'filter' => $filter, 'channel' => $channel]) }}#appointments"
                       class="{{ $day === 'upcoming' ? 'active' : '' }}">Upcoming</a>
                    <a href="{{ route('admin.dashboard', ['day' => 'all', 'filter' => $filter, 'channel' => $channel]) }}#appointments"
                       class="{{ $day === 'all' ? 'active' : '' }}">All dates</a>
                </div>
                <div class="kad-filters">
                    @foreach (['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed'] as $key => $label)
                        <a href="{{ route('admin.dashboard', ['filter' => $key, 'day' => $day, 'channel' => $channel]) }}#appointments"
                           class="{{ $filter === $key ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
                <div class="kad-filters">
                    @foreach (['all' => 'All channels'] + $channels as $key => $label)
                        <a href="{{ route('admin.dashboard', ['channel' => $key, 'day' => $day, 'filter' => $filter]) }}#appointments"
                           class="{{ $channel === $key ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div class="kad-table-scroll">
                <table class="kad-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Channel</th>
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
                                    <span class="kad-ch ch-{{ $a->channel ?? 'online' }}">
                                        <i class='bx {{ ['online' => 'bx-globe', 'walk_in' => 'bx-walk', 'phone' => 'bx-phone'][$a->channel ?? 'online'] ?? 'bx-globe' }}'></i>
                                        {{ $channels[$a->channel ?? 'online'] ?? 'Online' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $a->time_label }}</strong><br>
                                    <small>{{ $a->appointment_date->format('D, j M') }}</small>
                                </td>
                                <td>
                                                                        @if ($a->symptomCheck)
                                        {{-- Chips the patient picked, then a short preview of what they typed.
                                             Hover shows the full text; the dentist sees it all on their dashboard. --}}
                                        @if ($a->symptomCheck->symptom_list !== '')
                                            {{ $a->symptomCheck->symptom_list }}
                                        @endif
                                        @if ($a->symptomCheck->description)
                                            <small class="kad-muted" style="display:block" title="{{ $a->symptomCheck->description }}">
                                                &ldquo;{{ \Illuminate\Support\Str::limit($a->symptomCheck->description, 50) }}&rdquo;
                                            </small>
                                        @elseif ($a->symptomCheck->symptom_list === '')
                                            <span class="kad-muted">No details given</span>
                                        @endif
                                    @elseif ($a->admin_note)
                                        <span class="kad-muted">{{ \Illuminate\Support\Str::limit($a->admin_note, 40) }}</span>
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
    @if (in_array($a->status, ['pending', 'confirmed']))
        <a href="{{ route('admin.appointment.reschedule', $a) }}" class="kad-act" title="Change date / time">
            <i class="bx bx-calendar-edit"></i>
        </a>

        {{-- Confirm: only a pending booking can be confirmed --}}
        @if ($a->status === 'pending')
            <form action="{{ route('admin.appointment.status', $a) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="confirmed">
                <button class="kad-act ok" title="Confirm"><i class="bx bx-check"></i></button>
            </form>
        @endif

        {{-- Complete: pending is allowed too, for walk-ins treated straight away --}}
        <form action="{{ route('admin.appointment.status', $a) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="completed">
            <button class="kad-act" title="Mark completed"><i class="bx bx-check-double"></i></button>
        </form>

        <form action="{{ route('admin.appointment.status', $a) }}" method="POST"
              onsubmit="return confirm('Cancel this appointment?')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">
            <button class="kad-act no" title="Cancel"><i class="bx bx-x"></i></button>
        </form>
    @else
        {{-- Completed, cancelled or no-show: final, nothing left to do --}}
        <span class="kad-muted">—</span>
    @endif
</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
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
    --vanilla:#FBF8F3;
    --buttercream:#EFE6DA;
    --powder:#B7C4B1;
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
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(251,248,243,.6); font-weight:600; font-size:.92rem; transition:background .2s ease, color .2s ease; }
.kad-menu-item i{ font-size:1.2rem; }
.kad-menu-item:hover{ background:rgba(251,248,243,.07); color:var(--vanilla); }
.kad-menu-item.active{ background:var(--walnut); color:var(--vanilla); }

.kad-side-foot{ border-top:1px solid rgba(251,248,243,.12); padding-top:18px; }
.kad-user{ display:flex; align-items:center; gap:12px; padding:0 6px 16px; }
.kad-user strong{ display:block; font-size:.88rem; color:var(--vanilla); }
.kad-user span{ font-size:.75rem; color:rgba(251,248,243,.5); }
.kad-avatar{ width:38px; height:38px; flex-shrink:0; border-radius:50%; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.kad-logout{ display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; text-decoration:none; color:rgba(251,248,243,.6); font-size:.88rem; font-weight:600; width:100%; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kad-logout:hover{ background:rgba(251,248,243,.07); color:var(--vanilla); }

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
.kad-charts-full{ grid-template-columns:1fr; }
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
.kad-table{ width:100%; border-collapse:collapse; min-width:820px; }
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
.b-pending{ background:#FDF1DC; color:#8a6a2e; }
.b-confirmed{ background:#dbe8ef; color:#3f6377; }
.b-completed{ background:#e3ece0; color:#4a7c4a; }

/* Booking channel tags */
.kad-ch{ display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:999px; font-size:.74rem; font-weight:800; white-space:nowrap; }
.kad-ch i{ font-size:.95rem; }
.ch-online{ background:#dbe8ef; color:#3f6377; }
.ch-walk_in{ background:#f3e6d6; color:#7a5230; }
.ch-phone{ background:#e6e2f0; color:#5b4f7d; }

.kad-new-btn{ display:inline-flex; align-items:center; gap:6px; background:var(--walnut); color:var(--vanilla); text-decoration:none; padding:10px 18px; border-radius:999px; font-size:.85rem; font-weight:700; box-shadow:0 12px 24px -14px rgba(94,68,46,.9); transition:transform .2s ease, background .2s ease; }
.kad-new-btn:hover{ background:var(--walnut-dark); transform:translateY(-2px); }

.kad-actions{ display:flex; gap:6px; }
.kad-act{ width:32px; height:32px; border-radius:10px; border:1px solid rgba(94,68,46,.15); background:#fff; color:var(--walnut); font-size:1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .18s ease; }
.kad-act:hover{ background:var(--walnut); color:var(--vanilla); border-color:var(--walnut); }
.kad-act.ok:hover{ background:#4a7c4a; border-color:#4a7c4a; }
.kad-act.no:hover{ background:#a13d3d; border-color:#a13d3d; }

.kad-flash{ display:flex; align-items:center; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 18px; font-size:.88rem; font-weight:600; margin-bottom:20px; }
.kad-head-controls{ display:flex; gap:8px; flex-wrap:wrap; }
.kad-head-controls-block{ margin:-6px 0 18px; }
.kad-filters a{ padding:7px 15px; border-radius:999px; font-size:.8rem; font-weight:700; color:var(--walnut); text-decoration:none; transition:background .2s ease; }
.kad-filters a.active{ background:var(--walnut); color:var(--vanilla); }
.kad-patient small{ display:block; font-size:.75rem; color:var(--muted); margin-top:1px; }
.kad-table td small{ font-size:.75rem; color:var(--muted); }
.kad-muted{ color:var(--muted); font-size:.85rem; }
.kad-actions form{ margin:0; }
a.kad-act{text-decoration:none;}
.kad-empty{ text-align:center; padding:40px 20px; }
.kad-empty i{ font-size:2.2rem; color:var(--powder); }
.kad-empty p{ margin:10px 0 0; color:var(--muted); font-size:.9rem; }
.s-pending{ background:#FDF1DC; color:#8a6a2e; }
.s-confirmed{ background:#dbe8ef; color:#3f6377; }
.s-completed{ background:#e3ece0; color:#4a7c4a; }
.s-cancelled,.s-no_show{ background:#efe6e0; color:#8a7060; }

.kad-reassign{ margin:0; }
.kad-reassign select{ border:1px solid rgba(94,68,46,.2); border-radius:10px; padding:6px 10px; font-family:inherit; font-size:.82rem; color:var(--walnut); background:#fff; cursor:pointer; max-width:130px; }
.kad-reassign select:focus{ outline:none; border-color:var(--powder); }
.kad-flash-err{ background:#fbdada; color:#8a2f2f; }

/* ===== NOTIFICATIONS ===== */
.kad-notif{ position:relative; }
.kad-notif-count{ position:absolute; top:-4px; right:-4px; min-width:20px; height:20px; padding:0 5px; border-radius:999px; background:#c0603c; color:#fff; font-size:.7rem; font-weight:800; display:flex; align-items:center; justify-content:center; border:2px solid var(--buttercream); }
.kad-notif-panel{ display:none; position:absolute; right:0; top:calc(100% + 12px); width:360px; max-width:calc(100vw - 32px); background:var(--vanilla); border:1px solid rgba(94,68,46,.12); border-radius:20px; padding:8px; box-shadow:0 24px 48px -20px rgba(94,68,46,.55); z-index:70; }
.kad-notif.open .kad-notif-panel{ display:block; animation:kadNotifIn .18s ease both; }
@keyframes kadNotifIn{ from{ opacity:0; transform:translateY(-6px); } to{ opacity:1; transform:none; } }
.kad-notif-head{ padding:10px 12px 8px; font-family:'Fraunces',serif; font-weight:600; font-size:1rem; color:var(--walnut); }
.kad-notif-item{ display:flex; gap:12px; padding:12px; border-radius:14px; text-decoration:none; color:var(--ink); transition:background .18s ease; }
.kad-notif-item:hover{ background:var(--buttercream); }
.kad-notif-ico{ width:36px; height:36px; flex-shrink:0; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.15rem; }
.t-crit{ background:#fbdada; color:#a13d3d; }
.t-warn{ background:#fde3d0; color:#a8611f; }
.t-pend{ background:#FDF1DC; color:#8a6a2e; }
.t-info{ background:#dbe8ef; color:#3f6377; }
.kad-notif-text strong{ display:block; font-size:.88rem; font-weight:700; }
.kad-notif-text small{ display:block; margin-top:2px; font-size:.79rem; color:var(--muted); line-height:1.45; }
.kad-notif-empty{ text-align:center; padding:26px 16px 22px; color:var(--muted); }
.kad-notif-empty i{ font-size:2rem; color:var(--powder); }
.kad-notif-empty p{ margin:8px 0 0; font-size:.86rem; }

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

/* ===== Mobile fixes ===== */
@media (max-width:1100px){
    /* minmax(0,1fr) stops the charts from pushing the page wider than the screen */
    .kad-charts{ grid-template-columns:minmax(0,1fr); }
}
@media (max-width:820px){
    .kad-side{ overflow-y:auto; }
    .kad-top{ align-items:flex-start; gap:12px; margin-bottom:20px; }
    .kad-top > div:not([class]){ min-width:0; }
    .kad-panel{ padding:18px 16px; border-radius:20px; }
    /* 16px stops iOS zooming in when a field is tapped */
    .kset-input input, .kset-input select, .kset-input textarea, .kad-search input{ font-size:16px; }
}
@media (max-width:820px){
    /* the notification list opens as a full-width sheet instead of hanging off the left edge */
    .kad-notif-panel{ position:fixed; top:72px; left:12px; right:12px; width:auto; max-width:none; max-height:70vh; overflow-y:auto; }
}
@media (max-width:540px){
    /* 2 x 2 stat cards instead of four tall rows */
    .kad-stats{ grid-template-columns:repeat(2,1fr); gap:10px; }
    .kad-stat{ padding:16px 14px; border-radius:18px; }
    .kad-stat-top{ flex-wrap:wrap; gap:8px; margin-bottom:12px; }
    .kad-stat-ico{ width:38px; height:38px; font-size:1.15rem; border-radius:12px; }
    .kad-trend{ font-size:.66rem; padding:4px 8px; }
    .kad-stat-num{ font-size:1.8rem; }
    .kad-stat-cap{ font-size:.78rem; line-height:1.35; }
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

    /* ---------- NOTIFICATIONS ---------- */
    var notif = document.getElementById('kadNotif');
    var notifBtn = document.getElementById('kadNotifBtn');
    notifBtn.addEventListener('click', function(e){
        e.stopPropagation();
        var open = notif.classList.toggle('open');
        notifBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function(e){
        if(!notif.contains(e.target)){
            notif.classList.remove('open');
            notifBtn.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') notif.classList.remove('open');
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
    var WALNUT = '#5E442E', POWDER = '#B7C4B1', BUTTER = '#EFE6DA', MUTED = '#7a6a5a';
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
                borderColor:'#FBF8F3', borderWidth:4, hoverOffset:8
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
            scales:{ y:{ beginAtZero:true, grid:gridCfg, ticks:{ precision:0 } }, x:{ grid:{ display:false } } }
        }
    });

    /* Patient age groups — guarded so the page still works if the
       controller has not been updated with the age series yet. */
    if (CHARTS.age) {
        new Chart(document.getElementById('ageChart'), {
            type:'bar',
            data:{
                labels: CHARTS.age.labels,
                datasets:[{
                    data: CHARTS.age.data,
                    backgroundColor: POWDER,
                    hoverBackgroundColor: WALNUT,
                    borderRadius:10, borderSkipped:false, barThickness:44
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{ display:false } },
                scales:{
                    y:{ beginAtZero:true, grid:gridCfg, ticks:{ precision:0 } },
                    x:{ grid:{ display:false } }
                }
            }
        });
    } else {
        document.getElementById('ages').style.display = 'none';
    }

    /* Booking channels — same guard as the age chart. */
    if (CHARTS.channel) {
        new Chart(document.getElementById('channelChart'), {
            type:'doughnut',
            data:{
                labels: CHARTS.channel.labels,
                datasets:[{
                    data: CHARTS.channel.data,
                    backgroundColor:[POWDER, '#c9a27e', '#a99bc9'],
                    borderColor:'#FBF8F3', borderWidth:4, hoverOffset:8
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false, cutout:'62%',
                plugins:{ legend:{ position:'bottom', labels:{ padding:16, usePointStyle:true, pointStyle:'circle' } } }
            }
        });
    } else {
        document.getElementById('channels').style.display = 'none';
    }

})();
</script>
@endsection