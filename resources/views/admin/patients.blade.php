@extends('layouts.app')

@section('title', 'Patients - KPZ Dentalcare')

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
                <h1 class="kad-title">Patients</h1>
                <p class="kad-date">{{ $patients->count() }} {{ Str::plural('patient', $patients->count()) }} registered</p>
            </div>
        </header>

        <section class="kad-panel kad-table-panel">
            <div class="kad-panel-head">
                <div>
                    <h2>All patients</h2>
                    <p>Search by name, phone, or email</p>
                </div>
                <form method="GET" action="{{ route('admin.patients') }}" class="kad-search">
                    <i class='bx bx-search'></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search patients...">
                    @if ($search !== '')
                        <a href="{{ route('admin.patients') }}" class="kad-search-clear" title="Clear search"><i class='bx bx-x'></i></a>
                    @endif
                </form>
            </div>

            <div class="kad-table-scroll">
                <table class="kad-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Total visits</th>
                            <th>Last appointment</th>
                            <th>Last symptom check</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patients as $patient)
                            <tr>
                                <td>
                                    <div class="kad-patient">
                                        <span class="kad-avatar">{{ strtoupper(substr($patient->name, 0, 2)) }}</span>
                                        <div>
                                            <strong>{{ $patient->name }}</strong>
                                            <small>Joined {{ $patient->created_at->format('M Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $patient->email }}<br>
                                    <small>{{ $patient->phone ?? 'No phone on file' }}</small>
                                </td>
                                <td>
                                    <span class="kad-badge {{ $patient->total_appointments > 0 ? 'b-routine' : 's-cancelled' }}">
                                        {{ $patient->total_appointments }} {{ Str::plural('visit', $patient->total_appointments) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($patient->last_appointment)
                                        <strong>{{ $patient->last_appointment->appointment_date->format('D, j M Y') }}</strong><br>
                                        <small>{{ ucfirst($patient->last_appointment->status) }}</small>
                                    @else
                                        <span class="kad-muted">No appointments yet</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($patient->last_symptom_check)
                                        <span class="kad-badge b-{{ $patient->last_symptom_check->severity }}">
                                            {{ ucfirst($patient->last_symptom_check->severity) }}
                                        </span><br>
                                        <small>{{ $patient->last_symptom_check->created_at->diffForHumans() }}</small>
                                    @else
                                        <span class="kad-muted">No symptom checks</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="kad-empty">
                                        <i class='bx bx-user-x'></i>
                                        <p>
                                            @if ($search !== '')
                                                No patients match "{{ $search }}".
                                            @else
                                                No patients registered yet.
                                            @endif
                                        </p>
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

.kad-main{ flex:1; min-width:0; padding:26px 30px 50px; }
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:26px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:2px 0 0; font-size:.85rem; color:var(--muted); }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }

.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:24px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:20px; flex-wrap:wrap; }
.kad-panel-head h2{ font-size:1.15rem; font-weight:600; }
.kad-panel-head p{ margin:3px 0 0; font-size:.82rem; color:var(--muted); }

.kad-search{ position:relative; display:flex; align-items:center; }
.kad-search i.bx-search{ position:absolute; left:14px; color:var(--muted); font-size:1.1rem; pointer-events:none; }
.kad-search input{ border:1px solid rgba(94,68,46,.2); border-radius:999px; padding:10px 40px 10px 38px; font-family:inherit; font-size:.85rem; color:var(--ink); background:#fff; min-width:220px; outline:none; }
.kad-search input:focus{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kad-search-clear{ position:absolute; right:10px; color:var(--muted); font-size:1.2rem; text-decoration:none; display:flex; }

.kad-table-panel{ padding-bottom:12px; }
.kad-table-scroll{ overflow-x:auto; }
.kad-table{ width:100%; border-collapse:collapse; min-width:720px; }
.kad-table th{ text-align:left; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); padding:0 14px 12px; border-bottom:1px solid rgba(94,68,46,.1); }
.kad-table td{ padding:14px; font-size:.9rem; border-bottom:1px solid rgba(94,68,46,.07); vertical-align:middle; }
.kad-table tr:last-child td{ border-bottom:none; }
.kad-table td small{ font-size:.75rem; color:var(--muted); }
.kad-patient{ display:flex; align-items:center; gap:10px; }
.kad-patient .kad-avatar{ width:34px; height:34px; font-size:.75rem; background:var(--buttercream); }
.kad-patient strong{ font-size:.9rem; color:var(--ink); font-weight:700; }
.kad-patient small{ display:block; font-size:.75rem; color:var(--muted); margin-top:1px; }

.kad-badge{ display:inline-block; padding:5px 12px; border-radius:999px; font-size:.74rem; font-weight:800; }
.b-routine{ background:#eef3e9; color:#5a7a4a; }
.b-urgent{ background:#fde3d0; color:#a8611f; }
.b-critical{ background:#fbdada; color:#a13d3d; }
.s-cancelled{ background:#efe6e0; color:#8a7060; }

.kad-muted{ color:var(--muted); font-size:.85rem; }
.kad-empty{ text-align:center; padding:40px 20px; }
.kad-empty i{ font-size:2.2rem; color:var(--powder); }
.kad-empty p{ margin:10px 0 0; color:var(--muted); font-size:.9rem; }

@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
    .kad-panel-head{ flex-direction:column; align-items:stretch; }
    .kad-search input{ min-width:0; width:100%; }
}
@media (max-width:540px){
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