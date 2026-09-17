@extends('layouts.app')

@section('title', 'Doctor Portal - KPZ Dentalcare')

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
            <span>KPZ Doctor</span>
        </a>

        <nav class="kad-menu">
            <a href="{{ route('doctor.dashboard') }}" class="kad-menu-item active"><i class='bx bx-calendar'></i> My Schedule</a>
            <a href="{{ route('doctor.dashboard', ['tab' => 'notes']) }}" class="kad-menu-item"><i class='bx bx-user-voice'></i> Consultations</a>
            <a href="{{ route('doctor.dashboard', ['tab' => 'history']) }}" class="kad-menu-item"><i class='bx bx-folder-open'></i> Patient Records</a>
            <a href="#" class="kad-menu-item kad-menu-item-disabled"><i class='bx bx-time-five'></i> My Availability</a>
        </nav>

        <div class="kad-side-foot">
            <div class="kad-user">
                <span class="kad-avatar">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') }}</span>
                <div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->specialisation ?: 'Dental Surgeon' }}</span>
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
                <h1 class="kad-title">My Schedule</h1>
                <p class="kad-date" id="kadDate"></p>
            </div>
            <div class="kad-top-right">
                <span class="kdoc-count"><i class='bx bx-user'></i> <strong id="remainCount">{{ $stats['remain'] }}</strong> left today</span>
                <div class="kad-avatar-dropdown">
                    <button type="button" class="kad-avatar-trigger" id="docAvatarBtn">
                        <span class="kad-avatar">{{ collect(explode(' ', auth()->user()->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('') }}</span>
                    </button>
                    <div class="kad-dropdown-menu" id="docAvatarMenu">
                        <a href="{{ route('doctor.settings') }}" class="kad-dropdown-item">
                            <i class='bx bx-cog'></i> Settings
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="kad-dropdown-item kad-dropdown-item-danger">
                                <i class='bx bx-log-out'></i> Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="kdoc-flash"><i class='bx bx-check-circle'></i> {{ session('status') }}</div>
        @endif

        <!-- quick stats -->
        <section class="kdoc-stats">
            <div class="kdoc-stat">
                <span class="kad-stat-ico"><i class='bx bx-list-ul'></i></span>
                <div><p class="kdoc-num" id="statTotal">{{ $stats['total'] }}</p><p class="kdoc-cap">Booked today</p></div>
            </div>
            <div class="kdoc-stat">
                <span class="kad-stat-ico"><i class='bx bx-check-double'></i></span>
                <div><p class="kdoc-num" id="statDone">{{ $stats['done'] }}</p><p class="kdoc-cap">Completed</p></div>
            </div>
            <div class="kdoc-stat">
                <span class="kad-stat-ico"><i class='bx bx-error-circle'></i></span>
                <div><p class="kdoc-num" id="statUrgent">{{ $stats['urgent'] }}</p><p class="kdoc-cap">Flagged urgent</p></div>
            </div>
        </section>

        <!-- ===== MASTER / DETAIL ===== -->
        <div class="kdoc-grid">

            <!-- SCHEDULE LIST -->
            <section class="kad-panel kdoc-schedule">
                <div class="kad-panel-head">
                    <div>
                        <h2>Today's queue</h2>
                        <p>Select a patient to prepare</p>
                    </div>
                </div>
                <div class="kdoc-queue" id="queueList"></div>
            </section>

            <!-- DETAIL PANEL -->
            <section class="kad-panel kdoc-detail">
                <div class="kdoc-detail-head">
                    <div class="kdoc-patient-head">
                        <span class="kad-avatar kdoc-avatar-lg" id="dName0">—</span>
                        <div>
                            <h2 id="dName">Select a patient</h2>
                            <p id="dMeta">Their symptom report will show up here</p>
                        </div>
                    </div>
                    <span class="kad-badge" id="dUrgency" style="display:none"></span>
                </div>

                <div class="kdoc-tabs" id="docTabs">
                    <button class="active" data-tab="prep">Case prep</button>
                    <button data-tab="notes">Consultation notes</button>
                    <button data-tab="history">Patient history</button>
                </div>

                <!-- TAB: CASE PREP -->
                <div class="kdoc-tabpane active" id="tab-prep">
                    <div class="kdoc-empty" id="prepEmpty">
                        <i class='bx bx-user-plus'></i>
                        <p>Pick someone from today's queue to see what they reported before their visit.</p>
                    </div>

                    <div id="prepBody" style="display:none">
                        <div class="kdoc-ai-card" id="aiCard">
                            <div class="kdoc-ai-top">
                                <span><i class='bx bx-message-dots'></i> Reported via symptom checker</span>
                                <span class="kdoc-ai-time" id="dSubmitted"></span>
                            </div>
                            <p class="kdoc-ai-verdict" id="dVerdict"></p>
                            <p class="kdoc-ai-note" id="dAdvice"></p>
                        </div>

                        <div class="kdoc-block">
                            <h4>Selected symptoms</h4>
                            <div class="kdoc-symptoms" id="dSymptoms"></div>
                        </div>

                        <div class="kdoc-block">
                            <h4>In their own words</h4>
                            <blockquote class="kdoc-quote" id="dDescription"></blockquote>
                        </div>

                        <div class="kdoc-block">
                            <h4>At a glance</h4>
                            <div class="kdoc-facts">
                                <div><span>Last visit</span><strong id="dLastVisit"></strong></div>
                                <div><span>Total visits</span><strong id="dVisits"></strong></div>
                                <div><span>Contact</span><strong id="dAllergies"></strong></div>
                                <div><span>Slot</span><strong id="dSlot"></strong></div>
                            </div>
                        </div>

                        <div class="kdoc-cta-row">
                            <button class="kx-btn kx-btn-dark" id="startBtn"><i class='bx bx-play'></i> Start consultation</button>
                        </div>
                    </div>
                </div>

                <!-- TAB: NOTES -->
                <div class="kdoc-tabpane" id="tab-notes">
                    <div class="kdoc-empty" id="notesEmpty">
                        <i class='bx bx-edit'></i>
                        <p>Select a patient first, then record what you found and what you did.</p>
                    </div>

                    <form id="notesBody" method="POST" action="" style="display:none">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="complete" id="nComplete" value="0">
                        <label class="kdoc-field">
                            <span>Diagnosis</span>
                            <input type="text" id="nDiagnosis" name="diagnosis" placeholder="e.g. Deep caries on tooth 36">
                        </label>

                        <label class="kdoc-field">
                            <span>Treatment given</span>
                            <textarea id="nTreatment" name="treatment" rows="3" placeholder="e.g. Temporary filling placed, patient advised on pain management..."></textarea>
                        </label>

                        <div class="kdoc-field-row">
                            <label class="kdoc-field">
                                <span>Follow-up needed?</span>
                                <select id="nFollowup" name="followup">
                                    <option value="none">No follow-up</option>
                                    <option value="1w">In 1 week</option>
                                    <option value="2w">In 2 weeks</option>
                                    <option value="1m">In 1 month</option>
                                    <option value="6m">Routine, 6 months</option>
                                </select>
                            </label>
                            <label class="kdoc-field">
                                <span>Outcome</span>
                                <select id="nOutcome" name="outcome">
                                    <option value="resolved">Resolved</option>
                                    <option value="ongoing">Ongoing treatment</option>
                                    <option value="referred">Referred out</option>
                                </select>
                            </label>
                        </div>

                        <label class="kdoc-field">
                            <span>Private notes</span>
                            <textarea id="nPrivate" name="private_notes" rows="2" placeholder="Anything worth remembering for next time..."></textarea>
                        </label>

                        <div class="kdoc-cta-row">
                            <button type="submit" class="kx-btn kx-btn-light" id="saveDraft">Save draft</button>
                            <button type="submit" class="kx-btn kx-btn-dark" id="completeBtn"><i class='bx bx-check'></i> Save &amp; mark completed</button>
                        </div>
                    </form>
                </div>

                <!-- TAB: HISTORY -->
                <div class="kdoc-tabpane" id="tab-history">
                    <div class="kdoc-empty" id="histEmpty">
                        <i class='bx bx-folder-open'></i>
                        <p>Select a patient to see their past visits and reported symptoms.</p>
                    </div>

                    <div id="histBody" style="display:none">
                        <div class="kdoc-alert" id="histAlert" style="display:none">
                            <i class='bx bx-repeat'></i>
                            <span id="histAlertText"></span>
                        </div>
                        <div class="kdoc-timeline" id="histTimeline"></div>
                    </div>
                </div>

            </section>
        </div>
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
.kad h4{ font-family:'Fraunces',serif; margin:0; color:var(--walnut); }

/* SIDEBAR (shared with admin) */
.kad-side{ width:250px; flex-shrink:0; background:var(--walnut-dark); color:var(--vanilla); padding:24px 18px; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; z-index:60; transition:left .3s ease; }
.kad-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; font-size:1.05rem; color:var(--vanilla); padding:0 8px 26px; }
.kad-logo-dots{ position:relative; display:flex; width:32px; height:20px; align-items:center; }
.kad-logo-dots i{ position:absolute; width:20px; height:20px; border-radius:50%; }
.kad-logo-dots i:first-child{ left:0; background:var(--buttercream); }
.kad-logo-dots i:last-child{ left:12px; background:var(--powder); }
.kad-menu{ display:flex; flex-direction:column; gap:4px; flex:1; }
.kad-menu-item{ display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; text-decoration:none; color:rgba(255,248,226,.6); font-weight:600; font-size:.92rem; transition:background .2s ease,color .2s ease; }
.kad-menu-item i{ font-size:1.2rem; }
.kad-menu-item:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }
.kad-menu-item.active{ background:var(--walnut); color:var(--vanilla); }
.kad-menu-item-disabled{ opacity:.4; cursor:not-allowed; pointer-events:none; }
.kad-side-foot{ border-top:1px solid rgba(255,248,226,.12); padding-top:18px; }
.kad-user{ display:flex; align-items:center; gap:12px; padding:0 6px 16px; }
.kad-user strong{ display:block; font-size:.88rem; color:var(--vanilla); }
.kad-user span{ font-size:.75rem; color:rgba(255,248,226,.5); }
.kad-avatar{ width:38px; height:38px; flex-shrink:0; border-radius:50%; background:var(--powder); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.kad-logout{ display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; text-decoration:none; color:rgba(255,248,226,.6); font-size:.88rem; font-weight:600; width:100%; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kad-logout:hover{ background:rgba(255,248,226,.07); color:var(--vanilla); }
.kad-overlay{ display:none; position:fixed; inset:0; background:rgba(47,35,24,.5); z-index:55; }
.kad-overlay.show{ display:block; }

/* MAIN */
.kad-main{ flex:1; min-width:0; padding:26px 30px 50px; }
.kad-top{ display:flex; align-items:center; gap:16px; margin-bottom:24px; }
.kad-title{ font-size:1.7rem; font-weight:600; }
.kad-date{ margin:2px 0 0; font-size:.85rem; color:var(--muted); }
.kad-top-right{ margin-left:auto; display:flex; align-items:center; gap:14px; }
.kdoc-count{ display:inline-flex; align-items:center; gap:6px; background:var(--vanilla); border:1px solid rgba(94,68,46,.12); padding:9px 16px; border-radius:999px; font-size:.85rem; color:var(--muted); }
.kdoc-count strong{ color:var(--walnut); }
.kad-burger{ display:none; width:42px; height:42px; border-radius:12px; border:1px solid rgba(94,68,46,.12); background:var(--vanilla); color:var(--walnut); font-size:1.4rem; cursor:pointer; }

/* AVATAR DROPDOWN */
.kad-avatar-dropdown{ position:relative; }
.kad-avatar-trigger{ background:none; border:none; padding:0; cursor:pointer; }
.kad-dropdown-menu{
    display:none; position:absolute; right:0; top:calc(100% + 10px);
    background:var(--vanilla); border:1px solid rgba(94,68,46,.12);
    border-radius:14px; min-width:170px; padding:6px;
    box-shadow:0 12px 26px -14px rgba(94,68,46,.5); z-index:70;
}
.kad-dropdown-menu.show{ display:block; }
.kad-dropdown-item{
    display:flex; align-items:center; gap:9px; width:100%;
    padding:10px 14px; border-radius:10px; text-decoration:none;
    background:none; border:none; cursor:pointer; text-align:left;
    font-family:inherit; font-size:.88rem; font-weight:600; color:var(--ink);
}
.kad-dropdown-item:hover{ background:var(--buttercream); }
.kad-dropdown-item-danger{ color:#a13d3d; }

/* QUICK STATS */
.kdoc-stats{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px; }
.kdoc-stat{ display:flex; align-items:center; gap:14px; background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:20px; padding:18px 20px; }
.kad-stat-ico{ width:44px; height:44px; flex-shrink:0; border-radius:14px; background:var(--buttercream); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
.kdoc-num{ margin:0; font-family:'Fraunces',serif; font-size:1.7rem; font-weight:600; color:var(--walnut); line-height:1; }
.kdoc-cap{ margin:3px 0 0; font-size:.8rem; color:var(--muted); }

/* PANELS */
.kad-panel{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:24px; padding:24px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 12px 26px -18px rgba(94,68,46,.5); }
.kad-panel-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:18px; }
.kad-panel-head h2{ font-size:1.15rem; font-weight:600; }
.kad-panel-head p{ margin:3px 0 0; font-size:.82rem; color:var(--muted); }

.kdoc-grid{ display:grid; grid-template-columns:340px 1fr; gap:16px; align-items:start; }

/* QUEUE */
.kdoc-queue{ display:flex; flex-direction:column; gap:8px; }
.kdoc-item{ display:flex; align-items:center; gap:12px; padding:13px 14px; border-radius:16px; border:1.5px solid rgba(94,68,46,.12); background:#fff; cursor:pointer; text-align:left; transition:all .18s ease; width:100%; font-family:inherit; }
.kdoc-item:hover{ border-color:var(--walnut); }
.kdoc-item.active{ background:var(--walnut); border-color:var(--walnut); }
.kdoc-item.active .kdoc-item-name,.kdoc-item.active .kdoc-item-time{ color:var(--vanilla); }
.kdoc-item.active .kdoc-item-sym{ color:rgba(255,248,226,.7); }
.kdoc-item.done{ opacity:.5; }
.kdoc-item-time{ font-size:.78rem; font-weight:800; color:var(--walnut); width:56px; flex-shrink:0; }
.kdoc-item-body{ flex:1; min-width:0; }
.kdoc-item-name{ display:block; font-weight:700; font-size:.92rem; color:var(--ink); }
.kdoc-item-sym{ display:block; font-size:.78rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.kdoc-flag{ width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.f-routine{ background:#8fae7e; } .f-urgent{ background:#d68c4a; } .f-critical{ background:#b55b5b; }

/* DETAIL */
.kdoc-detail{ min-height:520px; }
.kdoc-detail-head{ display:flex; align-items:flex-start; justify-content:space-between; gap:14px; padding-bottom:20px; border-bottom:1px solid rgba(94,68,46,.1); }
.kdoc-patient-head{ display:flex; align-items:center; gap:14px; }
.kdoc-avatar-lg{ width:52px; height:52px; font-size:1rem; background:var(--buttercream); }
.kdoc-detail-head h2{ font-size:1.3rem; }
.kdoc-detail-head p{ margin:3px 0 0; font-size:.85rem; color:var(--muted); }

.kdoc-tabs{ display:flex; gap:4px; background:var(--buttercream); padding:4px; border-radius:999px; margin:20px 0; flex-wrap:wrap; }
.kdoc-tabs button{ border:none; background:none; padding:9px 18px; border-radius:999px; font-size:.85rem; font-weight:700; color:var(--walnut); cursor:pointer; font-family:inherit; transition:background .2s ease; }
.kdoc-tabs button.active{ background:var(--walnut); color:var(--vanilla); }

.kdoc-tabpane{ display:none; }
.kdoc-tabpane.active{ display:block; animation:kdocFade .35s ease both; }
@keyframes kdocFade{ from{ opacity:0; transform:translateY(8px); } to{ opacity:1; transform:none; } }

.kdoc-empty{ text-align:center; padding:56px 20px; color:var(--muted); }
.kdoc-empty i{ font-size:2.4rem; color:var(--powder); }
.kdoc-empty p{ margin:12px auto 0; max-width:300px; font-size:.92rem; line-height:1.6; }

/* AI CARD */
.kdoc-ai-card{ border-radius:18px; padding:20px 22px; background:var(--buttercream); border-left:5px solid var(--walnut); margin-bottom:24px; }
.kdoc-ai-card.urgent{ background:#fde3d0; border-left-color:#b5651d; }
.kdoc-ai-card.critical{ background:#fbdada; border-left-color:#a13d3d; }
.kdoc-ai-top{ display:flex; justify-content:space-between; align-items:center; gap:10px; font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--walnut); opacity:.75; flex-wrap:wrap; }
.kdoc-ai-verdict{ margin:12px 0 6px; font-family:'Fraunces',serif; font-size:1.1rem; font-weight:600; color:var(--walnut); }
.kdoc-ai-note{ margin:0; font-size:.9rem; line-height:1.55; color:#5c4d3f; }

.kdoc-block{ margin-bottom:24px; }
.kdoc-block h4{ font-size:.95rem; margin-bottom:12px; }
.kdoc-symptoms{ display:flex; flex-wrap:wrap; gap:8px; }
.kdoc-symptoms span{ padding:8px 16px; border-radius:999px; background:#fff; border:1px solid rgba(94,68,46,.15); font-size:.84rem; font-weight:600; color:var(--walnut); }
.kdoc-quote{ margin:0; padding:16px 20px; border-radius:14px; background:#fff; border:1px dashed rgba(94,68,46,.3); font-size:.92rem; line-height:1.6; color:#5c4d3f; font-style:italic; }
.kdoc-facts{ display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
.kdoc-facts div{ background:#fff; border:1px solid rgba(94,68,46,.1); border-radius:14px; padding:14px; }
.kdoc-facts span{ display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); font-weight:700; }
.kdoc-facts strong{ display:block; margin-top:5px; font-size:.9rem; color:var(--ink); }

/* BUTTONS */
.kx-btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; border-radius:999px; font-weight:700; font-size:.9rem; border:1px solid transparent; cursor:pointer; padding:12px 24px; font-family:inherit; transition:transform .25s ease, box-shadow .25s ease, background .25s ease; text-decoration:none; }
.kx-btn:hover{ transform:translateY(-2px); }
.kx-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 12px 24px -12px rgba(94,68,46,.8); }
.kx-btn-dark:hover{ background:var(--walnut-dark); }
.kx-btn-light{ background:#fff; color:var(--walnut); border-color:rgba(94,68,46,.15); }
.kdoc-cta-row{ display:flex; gap:10px; flex-wrap:wrap; margin-top:6px; }

/* NOTES FORM */
.kdoc-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kdoc-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kdoc-field input,.kdoc-field textarea,.kdoc-field select{ border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; font-family:inherit; font-size:.92rem; color:var(--ink); background:#fff; resize:vertical; outline:none; }
.kdoc-field input:focus,.kdoc-field textarea:focus,.kdoc-field select:focus{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(188,209,222,.4); }
.kdoc-field-row{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.kdoc-flash{ display:flex; align-items:center; gap:9px; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 18px; font-size:.88rem; font-weight:600; margin-bottom:18px; }
.kdoc-saved{ display:none; align-items:center; gap:7px; margin:16px 0 0; font-size:.88rem; font-weight:700; color:#4a7c4a; }
.kdoc-saved.show{ display:flex; }

/* HISTORY */
.kdoc-alert{ display:flex; align-items:center; gap:10px; background:#fde3d0; color:#8a4e14; border-radius:14px; padding:14px 18px; font-size:.88rem; font-weight:600; margin-bottom:22px; }
.kdoc-alert i{ font-size:1.2rem; flex-shrink:0; }
.kdoc-timeline{ position:relative; padding-left:26px; }
.kdoc-timeline::before{ content:''; position:absolute; left:7px; top:6px; bottom:6px; width:2px; background:rgba(94,68,46,.15); }
.kdoc-hitem{ position:relative; margin-bottom:20px; }
.kdoc-hitem::before{ content:''; position:absolute; left:-24px; top:5px; width:12px; height:12px; border-radius:50%; background:var(--powder); border:2px solid var(--vanilla); }
.kdoc-hitem.recent::before{ background:var(--walnut); }
.kdoc-hdate{ font-size:.76rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
.kdoc-hcard{ margin-top:8px; background:#fff; border:1px solid rgba(94,68,46,.1); border-radius:16px; padding:16px 18px; }
.kdoc-hcard strong{ display:block; font-size:.95rem; color:var(--walnut); font-family:'Fraunces',serif; }
.kdoc-hcard p{ margin:6px 0 0; font-size:.87rem; color:#5c4d3f; line-height:1.55; }
.kdoc-htags{ display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
.kdoc-htags span{ font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:999px; background:var(--buttercream); color:var(--walnut); }

.kad-badge{ display:inline-block; padding:6px 14px; border-radius:999px; font-size:.75rem; font-weight:800; }
.b-routine{ background:#eef3e9; color:#5a7a4a; }
.b-urgent{ background:#fde3d0; color:#a8611f; }
.b-critical{ background:#fbdada; color:#a13d3d; }

/* RESPONSIVE */
@media (max-width:1050px){
    .kdoc-grid{ grid-template-columns:1fr; }
    .kdoc-facts{ grid-template-columns:repeat(2,1fr); }
}
@media (max-width:820px){
    .kad-side{ position:fixed; left:-260px; top:0; }
    .kad-side.open{ left:0; }
    .kad-burger{ display:flex; align-items:center; justify-content:center; }
    .kad-main{ padding:20px 18px 40px; }
    .kdoc-stats{ grid-template-columns:1fr; }
    .kdoc-count{ display:none; }
}
@media (max-width:540px){
    .kdoc-facts,.kdoc-field-row{ grid-template-columns:1fr; }
    .kad-title{ font-size:1.4rem; }
}
</style>

<script>
(function(){
    document.getElementById('kadDate').textContent = new Date().toLocaleDateString('en-GB', {
        weekday:'long', day:'numeric', month:'long', year:'numeric'
    });

    var side = document.getElementById('kadSide');
    var overlay = document.getElementById('kadOverlay');
    document.getElementById('kadBurger').addEventListener('click', function(){
        side.classList.add('open'); overlay.classList.add('show');
    });
    overlay.addEventListener('click', function(){
        side.classList.remove('open'); overlay.classList.remove('show');
    });

    /* Avatar dropdown (Settings / Log out) */
    var docBtn = document.getElementById('docAvatarBtn');
    var docMenu = document.getElementById('docAvatarMenu');
    docBtn.addEventListener('click', function(e){
        e.stopPropagation();
        docMenu.classList.toggle('show');
    });
    document.addEventListener('click', function(){
        docMenu.classList.remove('show');
    });

    /* Real appointments for today, from DoctorController. */
    var patients = @json($queue);
    var NOTES_URL = '{{ url('doctor/consultation') }}';

    var selected = null;

    function initials(n){ return n.split(' ').map(function(w){ return w[0]; }).slice(0,2).join('').toUpperCase(); }
    function cap(s){ return s.charAt(0).toUpperCase() + s.slice(1); }

    /* ---------- QUEUE ---------- */
    function renderQueue(){
        var wrap = document.getElementById('queueList');
        wrap.innerHTML = '';
        patients.forEach(function(p, i){
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kdoc-item' + (p.status === 'done' ? ' done' : '') + (selected === i ? ' active' : '');
            btn.innerHTML =
                '<span class="kdoc-item-time">' + p.time + '</span>' +
                '<span class="kdoc-item-body">' +
                    '<span class="kdoc-item-name">' + p.name + '</span>' +
                    '<span class="kdoc-item-sym">' + p.symptoms.join(', ') + '</span>' +
                '</span>' +
                '<span class="kdoc-flag f-' + p.urgency + '"></span>';
            btn.addEventListener('click', function(){ select(i); });
            wrap.appendChild(btn);
        });
    }

    /* ---------- SELECT PATIENT ---------- */
    function select(i){
        selected = i;
        var p = patients[i];
        renderQueue();

        document.getElementById('dName0').textContent = initials(p.name);
        document.getElementById('dName').textContent = p.name;
        document.getElementById('dMeta').textContent = p.time + ' · ' + p.phone +
            (p.status === 'done' ? ' · Completed' : '');

        var ub = document.getElementById('dUrgency');
        ub.style.display = 'inline-block';
        ub.className = 'kad-badge b-' + p.urgency;
        ub.textContent = cap(p.urgency);

        /* --- case prep --- */
        document.getElementById('prepEmpty').style.display = 'none';
        document.getElementById('prepBody').style.display = 'block';

        var ai = document.getElementById('aiCard');
        ai.className = 'kdoc-ai-card' + (p.urgency !== 'routine' ? ' ' + p.urgency : '');
        document.getElementById('dSubmitted').textContent = p.submitted;
        document.getElementById('dVerdict').textContent = p.verdict;
        document.getElementById('dAdvice').textContent = p.advice;

        var symWrap = document.getElementById('dSymptoms');
        symWrap.innerHTML = '';
        p.symptoms.forEach(function(s){
            var el = document.createElement('span'); el.textContent = s; symWrap.appendChild(el);
        });

        document.getElementById('dDescription').textContent = '"' + p.description + '"';
        document.getElementById('dLastVisit').textContent = p.lastVisit;
        document.getElementById('dVisits').textContent = p.visits === 0 ? 'New patient' : p.visits;
        document.getElementById('dAllergies').textContent = p.phone;
        document.getElementById('dSlot').textContent = p.time;

        /* --- notes --- */
        document.getElementById('notesEmpty').style.display = 'none';
        document.getElementById('notesBody').style.display = 'block';
        var form = document.getElementById('notesBody');
        form.action = NOTES_URL + '/' + p.id;
        document.getElementById('nDiagnosis').value = p.notes.diagnosis;
        document.getElementById('nTreatment').value = p.notes.treatment;
        document.getElementById('nPrivate').value   = p.notes.private_notes;
        document.getElementById('nFollowup').value  = p.notes.followup;
        document.getElementById('nOutcome').value   = p.notes.outcome;

        /* --- history --- */
        document.getElementById('histEmpty').style.display = 'none';
        document.getElementById('histBody').style.display = 'block';
        renderHistory(p);
    }

    /* ---------- HISTORY ---------- */
    function renderHistory(p){
        var tl = document.getElementById('histTimeline');
        var alert = document.getElementById('histAlert');
        tl.innerHTML = '';

        if(!p.history.length){
            alert.style.display = 'none';
            tl.innerHTML = '<p style="color:var(--muted); font-size:.9rem;">No previous visits on record — this is their first appointment.</p>';
            return;
        }

        /* flag recurring complaints */
        var counts = {};
        p.history.forEach(function(h){
            h.tags.forEach(function(t){
                if(['Resolved','Ongoing','Referred','New patient','Check-up'].indexOf(t) > -1) return;
                counts[t] = (counts[t] || 0) + 1;
            });
        });
        var repeat = Object.keys(counts).filter(function(k){ return counts[k] >= 2; });
        if(repeat.length){
            alert.style.display = 'flex';
            document.getElementById('histAlertText').textContent =
                'Recurring issue: "' + repeat[0] + '" has come up ' + counts[repeat[0]] + ' times before.';
        } else {
            alert.style.display = 'none';
        }

        p.history.forEach(function(h, i){
            var d = document.createElement('div');
            d.className = 'kdoc-hitem' + (i === 0 ? ' recent' : '');
            var tags = h.tags.map(function(t){ return '<span>' + t + '</span>'; }).join('');
            d.innerHTML =
                '<p class="kdoc-hdate">' + h.date + '</p>' +
                '<div class="kdoc-hcard">' +
                    '<strong>' + h.title + '</strong>' +
                    '<p>' + h.note + '</p>' +
                    '<div class="kdoc-htags">' + tags + '</div>' +
                '</div>';
            tl.appendChild(d);
        });
    }

    /* ---------- TABS ---------- */
    document.querySelectorAll('#docTabs button').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('#docTabs button').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            document.querySelectorAll('.kdoc-tabpane').forEach(function(p){ p.classList.remove('active'); });
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    function goTab(name){
        document.querySelectorAll('#docTabs button').forEach(function(b){
            b.classList.toggle('active', b.dataset.tab === name);
        });
        document.querySelectorAll('.kdoc-tabpane').forEach(function(p){ p.classList.remove('active'); });
        document.getElementById('tab-' + name).classList.add('active');
    }

    /* ---------- ACTIONS ---------- */
    document.getElementById('startBtn').addEventListener('click', function(){ goTab('notes'); });

    document.getElementById('saveDraft').addEventListener('click', function(){
        document.getElementById('nComplete').value = '0';
    });

    document.getElementById('completeBtn').addEventListener('click', function(){
        document.getElementById('nComplete').value = '1';
    });

    /* ---------- DEEP LINK: jump to a tab via ?tab=notes / ?tab=history ---------- */
    var urlParams = new URLSearchParams(window.location.search);
    var initialTab = urlParams.get('tab');
    if (initialTab && document.getElementById('tab-' + initialTab)) {
        goTab(initialTab);
    }

    renderQueue();
})();
</script>
@endsection