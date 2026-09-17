@extends('layouts.app')

@section('title', 'KPZ Dentalcare - Smart Dental Appointment System')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kx">
<div class="kx-shell">

    <!-- ================= HERO CARD ================= -->
    <section class="kx-hero">
        <!-- atmosphere blobs -->
        <div class="kx-bloom" id="kxBloom"></div>
        <div class="kx-bloom-2"></div>
        <div class="kx-bloom-3"></div>

        <!-- FLOATING PILL NAV -->
        <div class="kx-nav-hold">
            <nav class="kx-nav" id="kxNav">
                <a href="{{ route('home') }}" class="kx-logo">
                    <span class="kx-logo-dots"><i></i><i></i></span>
                    <span>KPZ Dentalcare</span>
                </a>
                <div class="kx-nav-links">
                    <a href="#services">{{ __('site.nav.services') }}</a>
                    <a href="#about">{{ __('site.nav.about') }}</a>
                    <a href="#faqs">{{ __('site.nav.faqs') }}</a>
                    <a href="#contact">{{ __('site.nav.contact') }}</a>
                </div>
                <div class="kx-nav-right">

                    <a href="{{ route('lang.switch', app()->getLocale() === 'ms' ? 'en' : 'ms') }}"
                       class="kx-lang"
                       title="{{ __('site.nav.switch_language') }}">
                        <i class='bx bx-globe'></i> {{ strtoupper(app()->getLocale()) }}
                    </a>

                    @auth
                        <div class="kx-user" id="kxUser">
                            <button class="kx-user-btn" id="kxUserBtn">
                                <span class="kx-user-av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                <span class="kx-user-name">{{ explode(' ', auth()->user()->name)[0] }}</span>
                                <i class='bx bx-chevron-down'></i>
                            </button>

                            <div class="kx-drop" id="kxDrop">
                                <div class="kx-drop-head">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <span>{{ auth()->user()->email }}</span>
                                </div>

                                @if (auth()->user()->isPatient())
                                    <a href="{{ route('booking') }}"><i class='bx bx-calendar-plus'></i> {{ __('site.nav.book_appointment') }}</a>
                                    <a href="{{ route('settings') }}"><i class='bx bx-cog'></i> {{ __('site.nav.account_settings') }}</a>
                                @elseif (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"><i class='bx bx-grid-alt'></i> {{ __('site.nav.admin_dashboard') }}</a>
                                @elseif (auth()->user()->isDoctor())
                                    <a href="{{ route('doctor.dashboard') }}"><i class='bx bx-calendar'></i> {{ __('site.nav.my_schedule') }}</a>
                                @endif

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"><i class='bx bx-log-out'></i> {{ __('site.nav.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="kx-btn kx-btn-dark kx-nav-cta">
                            {{ __('site.nav.book_now') }} <i class='bx bx-right-arrow-alt'></i>
                        </a>
                    @endauth
                    <button class="kx-burger" id="kxBurger"><i class='bx bx-menu'></i></button>
                </div>
            </nav>
        </div>

        <!-- mobile sheet -->
        <div class="kx-mobile" id="kxMobile">
            <a href="#services">{{ __('site.nav.services') }}</a>
            <a href="#about">{{ __('site.nav.about') }}</a>
            <a href="#faqs">{{ __('site.nav.faqs') }}</a>
            <a href="#contact">{{ __('site.nav.contact') }}</a>

            <a href="{{ route('lang.switch', app()->getLocale() === 'ms' ? 'en' : 'ms') }}">
                <i class='bx bx-globe'></i> {{ __('site.nav.other_language') }}
            </a>

            @auth
                @if (auth()->user()->isPatient())
                    <a href="{{ route('booking') }}">{{ __('site.nav.book_appointment') }}</a>
                    <a href="{{ route('settings') }}">{{ __('site.nav.account_settings') }}</a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">{{ __('site.nav.admin_dashboard') }}</a>
                @elseif (auth()->user()->isDoctor())
                    <a href="{{ route('doctor.dashboard') }}">{{ __('site.nav.my_schedule') }}</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="kx-mobile-form">
                    @csrf
                    <button type="submit" class="kx-btn kx-btn-dark">{{ __('site.nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="kx-btn kx-btn-dark">{{ __('site.nav.book_now') }}</a>
            @endauth
        </div>

        <!-- HERO CONTENT -->
        <div class="kx-hero-body">
            <div class="kx-badge" data-rise>
                <i class='bx bx-check-shield'></i> Klinik Pergigian Dr. Zuraida
            </div>

            <div class="kx-head-hold">
                <h1 class="kx-h1" data-rise style="transition-delay:.12s">
                    {{ __('site.hero.title_1') }}<br>
                    {{ __('site.hero.title_2') }} <span class="kx-serif">{{ __('site.hero.title_em') }}</span>
                </h1>

                <!-- floating tiles -->
                <div class="kx-tile kx-tile-1" data-drift>
                    <span style="background:#7fa88a"><i class='bx bx-calendar-check'></i></span>
                </div>
                <div class="kx-tile kx-tile-2" data-drift style="transition-delay:.2s">
                    <span style="background:var(--powder); color:var(--walnut)"><i class='bx bx-message-dots'></i></span>
                </div>
                <div class="kx-tile kx-tile-3" data-drift style="transition-delay:.4s">
                    <span style="background:var(--buttercream); color:var(--walnut)"><i class='bx bx-star'></i></span>
                </div>
            </div>

            <p class="kx-lead" data-rise style="transition-delay:.28s">
                {{ __('site.hero.lead') }}
            </p>

             <div class="kx-cta-row" data-rise style="transition-delay:.42s">
                <a href="{{ route('symptom-checker') }}" class="kx-btn kx-btn-dark kx-btn-lg">
                    {{ __('site.hero.cta_symptom') }} <i class='bx bx-right-arrow-alt'></i>
                </a>
                <a href="@auth{{ \App\Http\Controllers\AuthController::homeFor(auth()->user()) }}@else{{ route('login') }}@endauth"
                   class="kx-btn kx-btn-light kx-btn-lg">
                    {{ __('site.hero.cta_book') }}
                </a>
            </div>

            <div class="kx-trust" data-rise style="transition-delay:.58s">
                <p class="kx-trust-label">{{ __('site.hero.trust_label') }}</p>
                <div class="kx-trust-row">
                    <span>{{ __('site.hero.trust_1') }}</span>
                    <span>{{ __('site.hero.trust_2') }}</span>
                    <span>{{ __('site.hero.trust_3') }}</span>
                </div>
            </div>
        </div>

        <!-- corner chip -->
        <div class="kx-chip" data-rise style="transition-delay:.7s">
            <span class="kx-chip-icon"><i class='bx bx-time-five'></i></span>
            <div>
                <p class="kx-chip-title">{{ __('site.hero.chip_title') }}</p>
                <p class="kx-chip-sub">{{ __('site.hero.chip_sub') }}</p>
            </div>
        </div>
    </section>

    <!-- ================= TREATMENTS ================= -->
    @include('partials.services')

    <!-- ================= HOW IT WORKS (BENTO) ================= -->
    <section id="features" class="kx-section">
        <div class="kx-sec-head">
            <p class="kx-eyebrow">{{ __('site.features.eyebrow') }}</p>
            <h2 class="kx-h2">{{ __('site.features.title') }}</h2>
            <p class="kx-sec-sub">{{ __('site.features.sub') }}</p>
        </div>

        <div class="kx-bento">
            <!-- big card -->
            <div class="kx-card kx-card-wide" data-reveal>
                <span class="kx-card-ico"><i class='bx bx-calendar-check'></i></span>
                <h3>{{ __('site.features.booking_title') }}</h3>
                <p>{{ __('site.features.booking_desc') }}</p>
                <div class="kx-mini-slots">
                    <span class="on">9:00 AM</span>
                    <span class="off">10:00 AM</span>
                    <span class="on">11:00 AM</span>
                    <span class="on">2:00 PM</span>
                </div>
            </div>

            <!-- tall card -->
            <div class="kx-card kx-card-dark" data-reveal style="transition-delay:.12s">
                <span class="kx-card-ico kx-ico-light"><i class='bx bx-message-dots'></i></span>
                <h3>{{ __('site.features.ai_title') }}</h3>
                <p>{{ __('site.features.ai_desc') }}</p>
                <div class="kx-urgency">
                    <span class="u-routine">{{ __('site.features.ai_routine') }}</span>
                    <span class="u-urgent">{{ __('site.features.ai_urgent') }}</span>
                    <span class="u-critical">{{ __('site.features.ai_critical') }}</span>
                </div>
                <a href="{{ route('symptom-checker') }}" class="kx-card-link">{{ __('site.features.ai_link') }} <i class='bx bx-right-arrow-alt'></i></a>
            </div>

            <div class="kx-card" data-reveal style="transition-delay:.2s">
                <span class="kx-card-ico"><i class='bx bx-map'></i></span>
                <h3>{{ __('site.features.info_title') }}</h3>
                <p>{{ __('site.features.info_desc') }}</p>
            </div>

            <div class="kx-card" data-reveal style="transition-delay:.28s">
                <span class="kx-card-ico"><i class='bx bx-bell'></i></span>
                <h3>{{ __('site.features.remind_title') }}</h3>
                <p>{{ __('site.features.remind_desc') }}</p>
            </div>
        </div>
    </section>

    <!-- ================= STAT PILLS ================= -->
    <section class="kx-section kx-section-tight">
        <div class="kx-proof">
            <div class="kx-pill kx-pill-l" data-reveal>
                <p class="kx-pill-num">68<span>%</span></p>
                <p class="kx-pill-cap">{{ __('site.stats.cap_1') }}</p>
            </div>
            <div class="kx-pill kx-pill-c" data-reveal style="transition-delay:.12s">
                <p class="kx-pill-num">50<span>%</span></p>
                <p class="kx-pill-cap">{{ __('site.stats.cap_2') }}</p>
            </div>
            <div class="kx-pill kx-pill-r" data-reveal style="transition-delay:.24s">
                <p class="kx-pill-num">55<span>%</span></p>
                <p class="kx-pill-cap">{{ __('site.stats.cap_3') }}</p>
            </div>
        </div>
        <p class="kx-proof-note">{{ __('site.stats.note') }}</p>
    </section>

    <!-- ================= ABOUT (DARK BENTO) ================= -->
    <section id="about" class="kx-section">
        <div class="kx-sec-head">
            <p class="kx-eyebrow">{{ __('site.about.eyebrow') }}</p>
            <h2 class="kx-h2">{{ __('site.about.title') }}</h2>
        </div>

        <div class="kx-dark-wrap">
            <div class="kx-dark-grid">
                <div class="kx-dcard kx-dcard-wide" data-reveal>
                    <h3>{{ __('site.about.flow_title_1') }} <span>{{ __('site.about.flow_title_2') }}</span></h3>
                    <p>{{ __('site.about.flow_desc') }}</p>
                    <div class="kx-flow">
                        <div class="kx-flow-step"><i class='bx bx-phone-off'></i><span>{{ __('site.about.flow_phone') }}</span></div>
                        <div class="kx-flow-step"><i class='bx bxl-whatsapp'></i><span>{{ __('site.about.flow_whatsapp') }}</span></div>
                        <div class="kx-flow-step"><i class='bx bx-walk'></i><span>{{ __('site.about.flow_walkin') }}</span></div>
                        <span class="kx-flow-arrow"><i class='bx bx-right-arrow-alt'></i></span>
                        <div class="kx-flow-step kx-flow-active"><i class='bx bx-check'></i><span>{{ __('site.about.flow_booked') }}</span></div>
                    </div>
                </div>

                <div class="kx-dcard" data-reveal style="transition-delay:.12s">
                    <span class="kx-dtag">{{ __('site.about.faster_tag') }}</span>
                    <div class="kx-bars">
                        <span style="height:26px"></span>
                        <span style="height:42px"></span>
                        <span style="height:74px" class="hot"></span>
                        <span style="height:38px"></span>
                        <span style="height:22px"></span>
                    </div>
                    <p class="kx-dcard-note">{{ __('site.about.faster_note') }}</p>
                </div>

                <div class="kx-dcard" data-reveal style="transition-delay:.2s">
                    <span class="kx-card-ico kx-ico-light"><i class='bx bx-shield-quarter'></i></span>
                    <h3>{{ __('site.about.records_1') }} <span>{{ __('site.about.records_2') }}</span></h3>
                    <p>{{ __('site.about.records_desc') }}</p>
                </div>

                <div class="kx-dcard kx-dcard-wide" data-reveal style="transition-delay:.28s">
                    <div class="kx-quote-row">
                        <span class="kx-quote-mark"><i class='bx bxs-quote-alt-left'></i></span>
                        <div>
                            <h3>{{ __('site.about.guidance_1') }} <span>{{ __('site.about.guidance_2') }}</span></h3>
                            <p>{{ __('site.about.guidance_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FAQS ================= -->
    <section id="faqs" class="kx-section">
        <div class="kx-sec-head">
            <p class="kx-eyebrow">{{ __('site.faqs.eyebrow') }}</p>
            <h2 class="kx-h2">{{ __('site.faqs.title') }}</h2>
        </div>

        <div class="kx-faqs">
            <div class="kx-faq" data-reveal>
                <button class="kx-faq-q"><span>{{ __('site.faqs.q_1') }}</span><i class='bx bx-plus'></i></button>
                <div class="kx-faq-a"><p>{{ __('site.faqs.a_1') }}</p></div>
            </div>
            <div class="kx-faq" data-reveal style="transition-delay:.08s">
                <button class="kx-faq-q"><span>{{ __('site.faqs.q_2') }}</span><i class='bx bx-plus'></i></button>
                <div class="kx-faq-a"><p>{{ __('site.faqs.a_2') }}</p></div>
            </div>
            <div class="kx-faq" data-reveal style="transition-delay:.16s">
                <button class="kx-faq-q"><span>{{ __('site.faqs.q_3') }}</span><i class='bx bx-plus'></i></button>
                <div class="kx-faq-a"><p>{{ __('site.faqs.a_3') }}</p></div>
            </div>
            <div class="kx-faq" data-reveal style="transition-delay:.24s">
                <button class="kx-faq-q"><span>{{ __('site.faqs.q_4') }}</span><i class='bx bx-plus'></i></button>
                <div class="kx-faq-a"><p>{{ __('site.faqs.a_4') }}</p></div>
            </div>
        </div>
    </section>

    <!-- ================= CONTACT / FOOTER CARD ================= -->
    <footer id="contact" class="kx-footer">
        <div class="kx-foot-bloom-1"></div>
        <div class="kx-foot-bloom-2"></div>

        <div class="kx-foot-body">
            <h2 class="kx-h2 kx-foot-h">{{ __('site.footer.title_1') }}<br>{{ __('site.footer.title_2') }}</h2>
            <p class="kx-foot-sub">{{ __('site.footer.sub') }}</p>

            <form class="kx-foot-form" onsubmit="event.preventDefault()">
                <input type="email" placeholder="{{ __('site.footer.placeholder') }}" required>
                <button type="submit" class="kx-btn kx-btn-dark">{{ __('site.footer.cta') }} <i class='bx bx-right-arrow-alt'></i></button>
            </form>

            <div class="kx-foot-contacts">
                <span><i class='bx bx-phone'></i> 014-915 3244</span>
                <span><i class='bx bx-map'></i> Klinik Pergigian Dr. Zuraida</span>
                <span><i class='bx bx-time-five'></i> {{ __('site.footer.hours') }}</span>
            </div>
        </div>

        <div class="kx-foot-bottom">
            <a href="{{ route('home') }}" class="kx-logo">
                <span class="kx-logo-dots"><i></i><i></i></span>
                <span>KPZ Dentalcare</span>
            </a>
            <div class="kx-foot-links">
                <a href="#services">{{ __('site.nav.services') }}</a>
                <a href="#about">{{ __('site.nav.about') }}</a>
                <a href="#faqs">{{ __('site.nav.faqs') }}</a>
                <a href="#contact">{{ __('site.nav.contact') }}</a>
            </div>
            <p class="kx-foot-copy">{{ __('site.footer.copy') }}</p>
        </div>
    </footer>

</div>
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

.kx{ background:var(--buttercream); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); min-height:100vh; }
.kx-shell{ max-width:1440px; margin:0 auto; padding:8px 8px 8px; }
.kx h1,.kx h2,.kx h3{ font-family:'Fraunces',serif; letter-spacing:-0.025em; margin:0; color:var(--walnut); }
.kx-eyebrow{ font-size:.85rem; font-weight:700; color:var(--walnut); opacity:.6; margin:0 0 10px; letter-spacing:.02em; }

/* variable weight hover */
.wchar{ display:inline-block; font-variation-settings:'wght' 600; transition:font-variation-settings .15s linear; will-change:font-variation-settings; }

/* ---------- BUTTONS ---------- */
.kx-btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px; border-radius:999px; font-weight:600; font-size:.92rem; text-decoration:none; border:1px solid transparent; cursor:pointer; padding:11px 22px; transition:transform .3s ease, box-shadow .3s ease, background .3s ease; }
.kx-btn:hover{ transform:translateY(-2px); }
.kx-btn-lg{ padding:14px 28px; font-size:1rem; }
.kx-btn-dark{ background:var(--walnut); color:var(--vanilla); box-shadow:0 1px 0 rgba(255,255,255,.2) inset, 0 12px 24px -12px rgba(94,68,46,.8); }
.kx-btn-dark:hover{ background:var(--walnut-dark); color:var(--vanilla); box-shadow:0 1px 0 rgba(255,255,255,.25) inset, 0 18px 30px -12px rgba(94,68,46,.9); }
.kx-btn-light{ background:rgba(255,255,255,.92); color:var(--walnut); border-color:rgba(94,68,46,.14); box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 10px 22px -14px rgba(94,68,46,.5); }
.kx-btn-light:hover{ background:#fff; color:var(--walnut); }

/* ---------- HERO CARD ---------- */
.kx-hero{ position:relative; overflow:hidden; border-radius:40px; background:var(--vanilla); min-height:100svh; display:flex; flex-direction:column; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 20px 60px -30px rgba(94,68,46,.4); }
.kx-bloom{ position:absolute; right:-160px; top:-40px; width:720px; height:720px; border-radius:50%; filter:blur(60px); opacity:.75; transition:transform .7s ease-out; background:radial-gradient(circle at 55% 45%, rgba(188,209,222,.85), rgba(188,209,222,.35) 42%, rgba(255,248,226,0) 70%); }
.kx-bloom-2{ position:absolute; left:-200px; top:120px; width:620px; height:620px; border-radius:50%; filter:blur(60px); opacity:.65; background:radial-gradient(circle, rgba(255,239,185,.9), rgba(255,239,185,.3) 48%, rgba(255,248,226,0) 72%); }
.kx-bloom-3{ position:absolute; right:22%; bottom:-180px; width:560px; height:420px; border-radius:50%; filter:blur(60px); opacity:.5; background:radial-gradient(circle, rgba(94,68,46,.18), rgba(255,248,226,0) 70%); }

/* ---------- FLOATING PILL NAV ---------- */
.kx-nav-hold{ position:sticky; top:14px; z-index:50; display:flex; justify-content:center; padding:18px 14px 0; }
.kx-nav{ display:flex; align-items:center; justify-content:space-between; width:100%; max-width:920px; border-radius:999px; border:1px solid rgba(255,255,255,.8); background:rgba(255,255,255,.75); backdrop-filter:blur(18px); padding:9px 10px 9px 20px; transition:box-shadow .5s ease; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 10px 30px -14px rgba(94,68,46,.45); }
.kx-nav.scrolled{ box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 18px 44px -14px rgba(94,68,46,.6); }
.kx-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; font-size:1rem; color:var(--walnut); }
.kx-logo-dots{ position:relative; display:flex; width:34px; height:22px; align-items:center; }
.kx-logo-dots i{ position:absolute; width:22px; height:22px; border-radius:50%; }
.kx-logo-dots i:first-child{ left:0; background:var(--walnut); }
.kx-logo-dots i:last-child{ left:13px; background:var(--powder); }
.kx-nav-links{ display:flex; align-items:center; gap:2px; font-size:.9rem; font-weight:500; }
.kx-nav-links a{ color:#5c4d3f; text-decoration:none; padding:8px 14px; border-radius:999px; transition:background .2s ease; }
.kx-nav-links a:hover{ background:rgba(94,68,46,.07); color:var(--walnut); }
.kx-nav-right{ display:flex; align-items:center; gap:8px; }
.kx-nav-cta i{ opacity:0; margin-left:-14px; transition:all .3s ease; }
.kx-nav-cta:hover i{ opacity:1; margin-left:0; }

/* language toggle */
.kx-lang{ display:flex; align-items:center; gap:6px; height:36px; padding:0 14px; border-radius:999px; border:1px solid rgba(94,68,46,.14); background:#fff; color:var(--walnut); font-size:.82rem; font-weight:700; text-decoration:none; white-space:nowrap; transition:box-shadow .25s ease, background .25s ease; }
.kx-lang:hover{ color:var(--walnut); background:var(--buttercream); box-shadow:0 8px 18px -10px rgba(94,68,46,.6); }
.kx-lang i{ font-size:1rem; }
.kx-lang:focus-visible{ outline:3px solid var(--powder); outline-offset:2px; }

/* signed-in user menu */
.kx-user{ position:relative; }
.kx-user-btn{ display:flex; align-items:center; gap:8px; border:1px solid rgba(94,68,46,.12); background:#fff; padding:5px 12px 5px 5px; border-radius:999px; cursor:pointer; font-family:inherit; transition:box-shadow .25s ease; }
.kx-user-btn:hover{ box-shadow:0 8px 18px -10px rgba(94,68,46,.6); }
.kx-user-av{ width:30px; height:30px; border-radius:50%; background:var(--walnut); color:var(--vanilla); display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:800; }
.kx-user-name{ font-size:.88rem; font-weight:700; color:var(--walnut); }
.kx-user-btn i{ font-size:1.1rem; color:var(--walnut); transition:transform .25s ease; }
.kx-user.open .kx-user-btn i{ transform:rotate(180deg); }

.kx-drop{ position:absolute; right:0; top:calc(100% + 10px); min-width:230px; background:#fff; border:1px solid rgba(94,68,46,.1); border-radius:18px; padding:8px; box-shadow:0 20px 40px -16px rgba(94,68,46,.5); opacity:0; visibility:hidden; transform:translateY(-6px); transition:all .22s ease; z-index:80; }
.kx-user.open .kx-drop{ opacity:1; visibility:visible; transform:none; }
.kx-drop-head{ padding:12px 14px 14px; border-bottom:1px solid rgba(94,68,46,.09); margin-bottom:6px; }
.kx-drop-head strong{ display:block; font-size:.9rem; color:var(--walnut); }
.kx-drop-head span{ display:block; font-size:.78rem; color:var(--muted); margin-top:2px; word-break:break-all; }
.kx-drop a,.kx-drop button{ display:flex; align-items:center; gap:10px; width:100%; padding:10px 14px; border-radius:12px; text-decoration:none; color:var(--ink); font-size:.88rem; font-weight:600; background:none; border:none; cursor:pointer; font-family:inherit; text-align:left; }
.kx-drop a:hover,.kx-drop button:hover{ background:rgba(94,68,46,.06); color:var(--walnut); }
.kx-drop i{ font-size:1.1rem; color:var(--walnut); }
.kx-drop form{ margin:0; border-top:1px solid rgba(94,68,46,.09); margin-top:6px; padding-top:6px; }
.kx-mobile-form{ margin:0; }
.kx-mobile-form .kx-btn{ width:100%; justify-content:center; margin-top:6px; }
.kx-burger{ display:none; width:40px; height:40px; border-radius:50%; border:1px solid rgba(94,68,46,.1); background:#fff; color:var(--walnut); font-size:1.3rem; align-items:center; justify-content:center; }
.kx-mobile{ display:none; position:relative; z-index:40; margin:12px 16px 0; border-radius:24px; border:1px solid rgba(255,255,255,.8); background:rgba(255,255,255,.94); backdrop-filter:blur(18px); padding:12px; flex-direction:column; box-shadow:0 18px 40px -20px rgba(94,68,46,.5); }
.kx-mobile.open{ display:flex; }
.kx-mobile a{ display:flex; align-items:center; gap:8px; padding:12px 16px; border-radius:14px; text-decoration:none; color:var(--ink); font-weight:600; font-size:.95rem; }
.kx-mobile a:hover{ background:rgba(94,68,46,.06); }
.kx-mobile .kx-btn{ margin-top:6px; justify-content:center; }

/* ---------- HERO BODY ---------- */
.kx-hero-body{ position:relative; z-index:10; flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; max-width:960px; margin:0 auto; padding:56px 24px 110px; }
[data-rise]{ opacity:0; transform:translateY(16px); transition:opacity .7s ease, transform .7s ease; }
[data-rise].in{ opacity:1; transform:none; }
.kx-badge{ display:inline-flex; align-items:center; gap:8px; border-radius:999px; border:1px solid rgba(255,255,255,.85); background:rgba(255,255,255,.82); padding:9px 18px; font-size:.85rem; font-weight:600; color:var(--walnut); backdrop-filter:blur(6px); box-shadow:0 6px 16px -8px rgba(94,68,46,.4); }
.kx-head-hold{ position:relative; margin-top:28px; }
.kx-h1{ font-size:clamp(2.6rem, 7vw, 5.5rem); line-height:1.02; font-weight:600; }
.kx-serif{ font-style:italic; font-weight:400; position:relative; display:inline-block; }
.kx-serif::after{ content:''; position:absolute; left:0; right:0; bottom:.1em; height:.14em; background:var(--powder); opacity:.75; z-index:-1; border-radius:999px; }

.kx-tile{ position:absolute; width:64px; height:64px; border-radius:20px; background:#fff; border:1px solid rgba(94,68,46,.07); display:none; align-items:center; justify-content:center; box-shadow:0 18px 35px -14px rgba(94,68,46,.5); opacity:0; transition:opacity .7s ease; }
.kx-tile.in{ opacity:1; }
.kx-tile span{ width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.25rem; }
.kx-tile-1{ right:-30px; top:-44px; transform:rotate(6deg); }
.kx-tile-2{ right:-104px; top:66px; transform:rotate(-6deg); }
.kx-tile-3{ right:24px; bottom:-56px; transform:rotate(3deg); }
@media (min-width:900px){ .kx-tile{ display:flex; } }

.kx-lead{ margin:36px auto 0; max-width:540px; font-size:1.1rem; line-height:1.65; color:#5c4d3f; }
.kx-cta-row{ margin-top:34px; display:flex; flex-wrap:wrap; gap:12px; justify-content:center; }
.kx-trust{ margin-top:52px; }
.kx-trust-label{ font-size:.72rem; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:var(--muted); margin:0 0 16px; }
.kx-trust-row{ display:flex; flex-wrap:wrap; justify-content:center; gap:10px 26px; }
.kx-trust-row span{ font-size:.95rem; font-weight:600; color:#5c4d3f; opacity:.65; }

.kx-chip{ position:absolute; right:24px; bottom:24px; z-index:20; display:none; align-items:center; gap:12px; border-radius:20px; border:1px solid rgba(255,255,255,.8); background:rgba(255,255,255,.88); backdrop-filter:blur(10px); padding:10px 18px 10px 10px; box-shadow:0 14px 30px -14px rgba(94,68,46,.55); }
@media (min-width:1024px){ .kx-chip{ display:flex; } }
.kx-chip-icon{ width:52px; height:40px; border-radius:12px; background:linear-gradient(135deg, var(--walnut), #7a5c3e); color:var(--vanilla); display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
.kx-chip-title{ margin:0; font-size:.88rem; font-weight:700; color:var(--walnut); }
.kx-chip-sub{ margin:0; font-size:.78rem; color:var(--muted); }

/* ---------- SECTIONS ---------- */
.kx-section{ padding:96px 24px; max-width:1180px; margin:0 auto; }
.kx-section-tight{ padding-top:0; }
.kx-sec-head{ max-width:640px; margin-bottom:52px; }
.kx-h2{ font-size:clamp(2rem, 4vw, 3rem); font-weight:600; line-height:1.1; }
.kx-sec-sub{ margin:16px 0 0; font-size:1.05rem; line-height:1.7; color:#5c4d3f; }

[data-reveal]{ opacity:0; transform:translateY(16px); transition:opacity .7s ease, transform .7s ease; }
[data-reveal].in{ opacity:1; transform:none; }

/* ---------- BENTO ---------- */
.kx-bento{ display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
.kx-card{ background:var(--vanilla); border:1px solid rgba(94,68,46,.08); border-radius:28px; padding:30px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 14px 30px -18px rgba(94,68,46,.4); transition:transform .3s ease, box-shadow .3s ease, opacity .7s ease; }
.kx-card:hover{ transform:translateY(-5px); box-shadow:0 24px 44px -18px rgba(94,68,46,.45); }
.kx-card-wide{ grid-column:span 2; }
.kx-card-ico{ display:flex; width:48px; height:48px; border-radius:16px; background:var(--buttercream); border:1px solid rgba(94,68,46,.08); color:var(--walnut); align-items:center; justify-content:center; font-size:1.35rem; }
.kx-ico-light{ background:rgba(255,248,226,.14); border-color:rgba(255,248,226,.15); color:var(--buttercream); }
.kx-card h3{ margin-top:20px; font-size:1.3rem; font-weight:600; }
.kx-card p{ margin:10px 0 0; font-size:1rem; line-height:1.6; color:#5c4d3f; }
.kx-mini-slots{ display:flex; flex-wrap:wrap; gap:8px; margin-top:22px; }
.kx-mini-slots span{ padding:8px 16px; border-radius:999px; font-size:.82rem; font-weight:600; }
.kx-mini-slots .on{ background:#fff; border:1px solid rgba(94,68,46,.15); color:var(--walnut); }
.kx-mini-slots .off{ background:rgba(94,68,46,.07); color:#a3937f; text-decoration:line-through; }

.kx-card-dark{ grid-row:span 2; background:var(--walnut); border-color:rgba(255,248,226,.08); display:flex; flex-direction:column; }
.kx-card-dark h3{ color:var(--vanilla); }
.kx-card-dark p{ color:rgba(255,248,226,.68); }
.kx-urgency{ display:flex; flex-direction:column; gap:10px; margin-top:26px; }
.kx-urgency span{ padding:11px 18px; border-radius:14px; font-size:.86rem; font-weight:700; }
.u-routine{ background:rgba(255,239,185,.16); color:var(--buttercream); }
.u-urgent{ background:rgba(214,140,74,.24); color:#f0c08a; }
.u-critical{ background:rgba(200,90,90,.24); color:#f0a6a6; }
.kx-card-link{ margin-top:auto; padding-top:26px; display:inline-flex; align-items:center; gap:6px; color:var(--buttercream); font-weight:700; font-size:.92rem; text-decoration:none; }
.kx-card-link:hover{ color:var(--powder); }

/* ---------- STAT PILLS ---------- */
.kx-proof{ display:flex; flex-direction:column; gap:14px; }
@media (min-width:900px){ .kx-proof{ flex-direction:row; align-items:center; gap:0; } .kx-proof > * + *{ margin-left:-22px; } }
.kx-pill{ flex:1; border-radius:999px; border:1px solid rgba(94,68,46,.08); background:var(--vanilla); padding:30px 32px; text-align:center; box-shadow:0 14px 30px -16px rgba(94,68,46,.45); }
.kx-pill-l{ transform:rotate(-1deg); }
.kx-pill-c{ position:relative; z-index:5; transform:scale(1.05); }
.kx-pill-r{ transform:rotate(1deg); }
.kx-pill-num{ margin:0; font-family:'Fraunces',serif; font-size:clamp(2rem,4vw,3rem); font-weight:600; color:var(--walnut); }
.kx-pill-num span{ color:var(--powder); }
.kx-pill-cap{ margin:6px 0 0; font-size:1rem; color:#5c4d3f; }
.kx-proof-note{ margin-top:28px; text-align:center; font-size:.8rem; color:var(--muted); }

/* ---------- DARK BENTO ---------- */
.kx-dark-wrap{ border-radius:38px; background:var(--walnut-dark); padding:18px; box-shadow:0 1px 0 rgba(255,255,255,.06) inset, 0 30px 60px -30px rgba(47,35,24,.9); }
.kx-dark-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.kx-dcard{ background:#3b2d20; border:1px solid rgba(255,248,226,.06); border-radius:28px; padding:32px; }
.kx-dcard-wide{ grid-column:span 2; }
.kx-dcard h3{ color:var(--vanilla); font-size:1.5rem; font-weight:600; }
.kx-dcard h3 span{ color:rgba(255,248,226,.45); }
.kx-dcard p{ margin:12px 0 0; color:rgba(255,248,226,.6); font-size:1rem; line-height:1.6; max-width:420px; }
.kx-dcard-note{ margin-top:24px !important; text-align:center; font-size:.88rem; }
.kx-dtag{ display:inline-block; background:var(--powder); color:var(--walnut); font-size:.75rem; font-weight:800; padding:6px 14px; border-radius:999px; }
.kx-bars{ display:flex; align-items:flex-end; justify-content:center; gap:10px; margin-top:26px; }
.kx-bars span{ width:18px; border-radius:999px; background:rgba(255,248,226,.14); }
.kx-bars span.hot{ background:linear-gradient(to bottom, var(--buttercream), var(--powder)); }
.kx-flow{ display:flex; align-items:center; justify-content:center; gap:10px; margin-top:34px; flex-wrap:wrap; }
.kx-flow-step{ display:flex; flex-direction:column; align-items:center; gap:8px; width:74px; padding:16px 6px; border-radius:18px; background:#463526; border:1px solid rgba(255,248,226,.05); color:rgba(255,248,226,.5); font-size:.72rem; font-weight:600; }
.kx-flow-step i{ font-size:1.3rem; }
.kx-flow-active{ background:linear-gradient(to bottom,#5c4630,#3f2f21); border-color:rgba(255,248,226,.16); color:var(--buttercream); }
.kx-flow-arrow{ width:32px; height:32px; border-radius:50%; border:1px solid rgba(255,239,185,.4); background:var(--walnut-dark); color:var(--buttercream); display:flex; align-items:center; justify-content:center; }
.kx-quote-row{ display:flex; gap:20px; align-items:flex-start; }
.kx-quote-mark{ flex-shrink:0; width:48px; height:48px; border-radius:16px; background:var(--buttercream); color:var(--walnut); display:flex; align-items:center; justify-content:center; font-size:1.3rem; }

/* ---------- FAQS ---------- */
.kx-faqs{ display:flex; flex-direction:column; gap:12px; max-width:820px; }
.kx-faq{ background:var(--vanilla); border:1px solid rgba(94,68,46,.09); border-radius:22px; overflow:hidden; }
.kx-faq-q{ width:100%; display:flex; align-items:center; justify-content:space-between; gap:14px; background:none; border:none; padding:22px 26px; font-family:'Fraunces',serif; font-size:1.1rem; font-weight:600; color:var(--walnut); text-align:left; cursor:pointer; }
.kx-faq-q i{ flex-shrink:0; font-size:1.35rem; transition:transform .3s ease; }
.kx-faq.open .kx-faq-q i{ transform:rotate(45deg); }
.kx-faq-a{ max-height:0; overflow:hidden; transition:max-height .35s ease; }
.kx-faq.open .kx-faq-a{ max-height:320px; }
.kx-faq-a p{ margin:0; padding:0 26px 24px; color:#5c4d3f; font-size:.98rem; line-height:1.65; }

/* ---------- FOOTER ---------- */
.kx-footer{ position:relative; overflow:hidden; border-radius:40px; background:var(--vanilla); padding:96px 32px 40px; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 20px 60px -30px rgba(94,68,46,.4); }
.kx-foot-bloom-1{ position:absolute; left:-160px; bottom:-80px; width:520px; height:520px; border-radius:50%; filter:blur(60px); opacity:.6; background:radial-gradient(circle, rgba(188,209,222,.8), transparent 70%); }
.kx-foot-bloom-2{ position:absolute; right:-160px; top:-90px; width:440px; height:440px; border-radius:50%; filter:blur(60px); opacity:.55; background:radial-gradient(circle, rgba(255,239,185,.9), transparent 70%); }
.kx-foot-body{ position:relative; max-width:720px; margin:0 auto; text-align:center; }
.kx-foot-h{ font-size:clamp(2rem,4.5vw,3.4rem); }
.kx-foot-sub{ margin:22px auto 0; max-width:520px; font-size:1.05rem; line-height:1.7; color:#5c4d3f; }
.kx-foot-form{ display:flex; flex-direction:column; gap:12px; max-width:520px; margin:36px auto 0; }
@media (min-width:640px){ .kx-foot-form{ flex-direction:row; } }
.kx-foot-form input{ flex:1; border-radius:999px; border:1px solid rgba(94,68,46,.14); background:#fff; padding:14px 24px; font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; color:var(--ink); outline:none; box-shadow:0 1px 0 rgba(255,255,255,.9) inset, 0 8px 20px -14px rgba(94,68,46,.4); }
.kx-foot-form input:focus{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(188,209,222,.4); }
.kx-foot-contacts{ display:flex; flex-wrap:wrap; justify-content:center; gap:12px 28px; margin-top:32px; font-size:.92rem; color:#5c4d3f; }
.kx-foot-contacts span{ display:inline-flex; align-items:center; gap:7px; }
.kx-foot-contacts i{ color:var(--walnut); }
.kx-foot-bottom{ position:relative; max-width:1080px; margin:70px auto 0; padding-top:30px; border-top:1px solid rgba(94,68,46,.1); display:flex; flex-direction:column; align-items:center; gap:20px; text-align:center; }
@media (min-width:768px){ .kx-foot-bottom{ flex-direction:row; justify-content:space-between; text-align:left; } }
.kx-foot-links{ display:flex; flex-wrap:wrap; justify-content:center; gap:22px; font-size:.92rem; font-weight:600; }
.kx-foot-links a{ color:#5c4d3f; text-decoration:none; }
.kx-foot-links a:hover{ color:var(--walnut); }
.kx-foot-copy{ margin:0; font-size:.78rem; color:var(--muted); }

/* ---------- RESPONSIVE ---------- */
@media (max-width:1000px){
    .kx-bento{ grid-template-columns:repeat(2,1fr); }
    .kx-card-wide{ grid-column:span 2; }
    .kx-card-dark{ grid-row:auto; grid-column:span 2; }
    .kx-dark-grid{ grid-template-columns:repeat(2,1fr); }
    .kx-dcard-wide{ grid-column:span 2; }
}
@media (max-width:820px){
    .kx-nav-links{ display:none; }
    .kx-burger{ display:flex; }
    .kx-nav-cta{ display:none; }
    .kx-lang{ display:none; }
}
@media (max-width:680px){
    .kx-shell{ padding:6px; }
    .kx-hero,.kx-footer{ border-radius:28px; }
    .kx-bento,.kx-dark-grid{ grid-template-columns:1fr; }
    .kx-card-wide,.kx-card-dark,.kx-dcard-wide{ grid-column:span 1; }
    .kx-section{ padding:64px 18px; }
    .kx-pill-c{ transform:none; }
    .kx-pill-l,.kx-pill-r{ transform:none; }
    .kx-quote-row{ flex-direction:column; }
}
</style>

<script>
(function(){
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* rise-in on load */
    window.addEventListener('load', function(){
        document.querySelectorAll('[data-rise]').forEach(function(el){ el.classList.add('in'); });
        document.querySelectorAll('[data-drift]').forEach(function(el){ el.classList.add('in'); });
    });

    /* scroll reveals */
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); }
        });
    }, { threshold:0.15 });
    document.querySelectorAll('[data-reveal]').forEach(function(el){ io.observe(el); });

    /* nav shadow */
    var nav = document.getElementById('kxNav');
    window.addEventListener('scroll', function(){
        nav.classList.toggle('scrolled', window.scrollY > 24);
    }, { passive:true });

    /* mobile menu */
    document.getElementById('kxBurger').addEventListener('click', function(){
        document.getElementById('kxMobile').classList.toggle('open');
    });
    document.querySelectorAll('#kxMobile a').forEach(function(a){
        a.addEventListener('click', function(){ document.getElementById('kxMobile').classList.remove('open'); });
    });

    /* signed-in user dropdown */
    var userWrap = document.getElementById('kxUser');
    if(userWrap){
        var userBtn = document.getElementById('kxUserBtn');
        userBtn.addEventListener('click', function(e){
            e.stopPropagation();
            userWrap.classList.toggle('open');
        });
        document.addEventListener('click', function(e){
            if(!userWrap.contains(e.target)) userWrap.classList.remove('open');
        });
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape') userWrap.classList.remove('open');
        });
    }

    /* FAQ accordion */
    document.querySelectorAll('.kx-faq-q').forEach(function(btn){
        btn.addEventListener('click', function(){
            var item = btn.closest('.kx-faq');
            var wasOpen = item.classList.contains('open');
            document.querySelectorAll('.kx-faq.open').forEach(function(el){ el.classList.remove('open'); });
            if(!wasOpen) item.classList.add('open');
        });
    });

    if(reduce) return;

    /* tile drift + bloom parallax */
    var tiles = document.querySelectorAll('[data-drift]');
    var baseRot = [6, -6, 3];
    var t = 0;
    (function drift(){
        t += 0.008;
        tiles.forEach(function(el, i){
            el.style.transform = 'translateY(' + (Math.sin(t + i*2)*6) + 'px) rotate(' + (baseRot[i] + Math.sin(t*0.7 + i)*2) + 'deg)';
        });
        requestAnimationFrame(drift);
    })();

    var bloom = document.getElementById('kxBloom');
    window.addEventListener('pointermove', function(e){
        var x = (e.clientX / window.innerWidth - 0.5) * 24;
        var y = (e.clientY / window.innerHeight - 0.5) * 24;
        bloom.style.transform = 'translate(' + x + 'px,' + y + 'px)';
    }, { passive:true });

    /* variable-weight hover on headings */
    function splitChars(el){
        var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
        var nodes = []; while(walker.nextNode()) nodes.push(walker.currentNode);
        nodes.forEach(function(node){
            if(!node.textContent.trim()) return;
            var frag = document.createDocumentFragment();
            node.textContent.split('').forEach(function(ch){
                if(ch === ' '){ frag.appendChild(document.createTextNode(' ')); return; }
                var s = document.createElement('span'); s.className = 'wchar'; s.textContent = ch;
                frag.appendChild(s);
            });
            node.parentNode.replaceChild(frag, node);
        });
    }
    document.querySelectorAll('.kx-h1, .kx-h2').forEach(splitChars);

    var chars = Array.prototype.slice.call(document.querySelectorAll('.wchar'));
    var mx = -9999, my = -9999;
    window.addEventListener('pointermove', function(e){ mx = e.clientX; my = e.clientY; }, { passive:true });
    (function tick(){
        for(var i=0;i<chars.length;i++){
            var s = chars[i], r = s.getBoundingClientRect();
            if(r.bottom < -80 || r.top > innerHeight + 80) continue;
            var d = Math.hypot(mx - (r.left + r.width/2), my - (r.top + r.height/2));
            var w = d < 200 ? 600 + (1 - d/200) * 300 : 600;
            s.style.fontVariationSettings = "'wght' " + Math.round(w);
        }
        requestAnimationFrame(tick);
    })();
})();
</script>

@endsection