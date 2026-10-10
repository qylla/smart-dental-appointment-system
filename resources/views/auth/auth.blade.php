@extends('layouts.app')

@section('title', 'Login / Register - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kauth">
    <a href="{{ url('/') }}" class="kauth-back"><i class='bx bx-arrow-back'></i> Back to home</a>

    <div class="kauth-wrap">
        <div class="kauth-card" id="authCard">

            <!-- LEFT INFO PANEL -->
            <div class="kauth-side">
                <div class="kauth-side-inner">
                    <div class="kauth-logo" aria-hidden="true"><i></i><i></i></div>
                    <div class="kauth-side-login">
                        <h1>Welcome back.</h1>
                        <p>Log in to view your appointment slip, check symptoms, and manage your bookings.</p>
                        <div class="kauth-punchline">
                            <span>New here?</span>
                            <button type="button" class="kauth-link-btn" id="toRegister">Create an account</button>
                        </div>
                    </div>
                    <div class="kauth-side-register">
                        <h1>Let's get you booked.</h1>
                        <p>Register once, and skip the phone queue for every visit after.</p>
                        <div class="kauth-punchline">
                            <span>Already registered?</span>
                            <button type="button" class="kauth-link-btn" id="toLogin">Log in instead</button>
                        </div>
                    </div>
                </div>
                <div class="kauth-blob"></div>
            </div>

            <!-- RIGHT FORM PANEL -->
            <div class="kauth-form-panel">

                <!-- LOGIN FORM -->
                <form action="{{ route('login.submit') }}" method="POST" class="kauth-form kauth-form-login active">
                    @csrf
                    <span class="kauth-eyebrow">Patient Login</span>
                    <h2>Sign in</h2>

                    @if (session('status'))
                        <p class="kauth-flash">{{ session('status') }}</p>
                    @endif

                    @if ($errors->any() && !old('name'))
                        <div class="kauth-errors">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Login may use the browser's saved password; that's a convenience for patients. --}}
                    <label class="kauth-field">
                        <span>Email</span>
                        <div class="kauth-input">
                            <i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="you@example.com" autocomplete="username" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Password</span>
                        <div class="kauth-input">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" placeholder="Enter your password"
                                   autocomplete="current-password" required>
                        </div>
                        <a href="{{ route('password.request') }}" class="kauth-forgot">Forgot password?</a>
                    </label>

                    <button type="submit" class="kpz-btn kpz-btn-brown kauth-submit">Log In</button>

                    <p class="kauth-switch-mobile">Don't have an account? <button type="button" class="kauth-link-btn" data-switch="register">Register</button></p>
                </form>

                <!-- REGISTER FORM -->
                {{-- autocomplete="new-password" tells the browser this is a NEW account,
                     so it doesn't fill in a saved login here. --}}
                <form action="{{ route('register') }}" method="POST" class="kauth-form kauth-form-register" autocomplete="off">
                    @csrf
                    <span class="kauth-eyebrow">New Patient</span>
                    <h2>Create your account</h2>

                    @if ($errors->any() && old('name'))
                        <div class="kauth-errors">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <label class="kauth-field">
                        <span>Full Name</span>
                        <div class="kauth-input">
                            <i class='bx bxs-user'></i>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="As per IC" autocomplete="off" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Email</span>
                        <div class="kauth-input">
                            <i class='bx bxs-envelope'></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="you@example.com" autocomplete="off" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Phone Number</span>
                        <div class="kauth-input">
                            <i class='bx bxs-phone'></i>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="01X-XXX XXXX" autocomplete="off" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Date of Birth</span>
                        <div class="kauth-input">
                            <i class='bx bxs-calendar'></i>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   max="{{ now()->subDay()->toDateString() }}" autocomplete="off" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Password</span>
                        <div class="kauth-input">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" placeholder="At least 8 characters"
                                   autocomplete="new-password" required>
                        </div>
                    </label>

                    <label class="kauth-field">
                        <span>Confirm Password</span>
                        <div class="kauth-input">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password_confirmation" placeholder="Type it again"
                                   autocomplete="new-password" required>
                        </div>
                    </label>

                    <button type="submit" class="kpz-btn kpz-btn-brown kauth-submit">Create Account</button>

                    <p class="kauth-switch-mobile">Already registered? <button type="button" class="kauth-link-btn" data-switch="login">Log in</button></p>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
:root{
    --vanilla:#FBF8F3;
    --buttercream:#EFE6DA;
    --powder:#B7C4B1;
    --walnut:#5E442E;
    --walnut-dark:#3f2e1f;
    --ink:#3a2c20;
}

.kauth{ min-height:100vh; background:var(--vanilla); font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); position:relative; padding:90px 20px 40px; }
.kauth-back{ position:absolute; top:28px; left:28px; display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--walnut); font-weight:700; font-size:.9rem; }
.kauth-back:hover{ color:var(--walnut-dark); }

.kauth-wrap{ max-width:960px; margin:0 auto; }
.kauth-card{ position:relative; display:block; overflow:hidden; background:#fff; border-radius:26px; box-shadow:0 30px 60px rgba(94,68,46,.18); height:560px; transition:height .7s cubic-bezier(.65,0,.35,1); }
.kauth-card.show-register{ height:660px; }

/* LEFT SIDE (slides as a curtain) */
.kauth-side{ position:absolute; top:0; left:0; width:42.5%; height:100%; background:var(--walnut); color:var(--vanilla); padding:48px 40px; display:flex; flex-direction:column; justify-content:center; overflow:hidden; z-index:3; transition:left .7s cubic-bezier(.65,0,.35,1); }
.kauth-card.show-register .kauth-side{ left:57.5%; }
.kauth-blob{ position:absolute; bottom:-70px; right:-60px; width:220px; height:220px; background:rgba(239,230,218,.12); border-radius:62% 38% 55% 45% / 48% 45% 55% 52%; }
.kauth-logo{ position:relative; z-index:2; display:flex; align-items:center; width:54px; height:34px; margin-bottom:26px; }
.kauth-logo i{ position:absolute; width:34px; height:34px; border-radius:50%; }
.kauth-logo i:first-child{ left:0; background:var(--buttercream); }
.kauth-logo i:last-child{ left:20px; background:var(--powder); opacity:.92; }.kauth-logo{ width:56px; height:56px; border-radius:50%; object-fit:cover; background:var(--vanilla); padding:6px; margin-bottom:26px; position:relative; z-index:2; }
.kauth-side-inner{ position:relative; z-index:2; }
.kauth-side h1{ font-family:'Fraunces',serif; font-size:2rem; font-weight:600; color:var(--vanilla); margin-bottom:14px; line-height:1.15; }
.kauth-side p{ color:rgba(251,248,243,.75); font-size:.95rem; line-height:1.6; margin-bottom:30px; max-width:280px; }
.kauth-punchline{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:.88rem; color:rgba(251,248,243,.75); }
.kauth-link-btn{ background:none; border:none; color:var(--buttercream); font-weight:700; text-decoration:underline; cursor:pointer; padding:0; font-size:inherit; font-family:inherit; }
.kauth-link-btn:hover{ color:var(--powder); }

.kauth-side-register{ display:none; }
.kauth-card.show-register .kauth-side-login{ display:none; }
.kauth-card.show-register .kauth-side-register{ display:block; }

/* RIGHT FORM PANEL (also slides, opposite direction) */
.kauth-form-panel{ position:absolute; top:0; left:42.5%; width:57.5%; height:100%; padding:44px; display:flex; overflow-y:auto; overflow-x:hidden; z-index:1; transition:left .7s cubic-bezier(.65,0,.35,1); }
.kauth-card.show-register .kauth-form-panel{ left:0; }

.kauth-form{ width:100%; margin:auto 0; display:none; flex-direction:column; }
.kauth-form.active{ display:flex; animation:kauthFormIn .55s cubic-bezier(.2,.8,.3,1) both; animation-delay:.25s; }

@keyframes kauthFormIn{
    from{ opacity:0; transform:translateY(16px); }
    to{ opacity:1; transform:translateY(0); }
}

.kauth-eyebrow{ font-size:.72rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--walnut); opacity:.65; }
.kauth-form h2{ font-family:'Fraunces',serif; font-size:1.7rem; color:var(--walnut); margin:6px 0 26px; }

.kauth-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kauth-field span{ font-size:.8rem; font-weight:700; color:var(--walnut); }
.kauth-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.25); border-radius:12px; padding:12px 14px; background:var(--vanilla); transition:border-color .2s ease, box-shadow .2s ease; }
.kauth-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kauth-input i{ color:var(--walnut); font-size:1.1rem; flex-shrink:0; }
.kauth-input input{ border:none; background:none; outline:none; width:100%; font-family:'Plus Jakarta Sans',sans-serif; font-size:.92rem; color:var(--ink); }
.kauth-input input::placeholder{ color:#a3937f; }

/* Keep Chrome's autofill from painting fields blue */
.kauth-input input:-webkit-autofill,
.kauth-input input:-webkit-autofill:hover,
.kauth-input input:-webkit-autofill:focus{
    -webkit-box-shadow:0 0 0 1000px var(--vanilla) inset;
    -webkit-text-fill-color:var(--ink);
    transition:background-color 9999s ease-in-out 0s;
}

/* Show / hide password toggle (added to every password field by the script below) */
.kauth-eye{ background:none; border:none; padding:0 2px; cursor:pointer; color:var(--walnut); font-size:1.2rem; line-height:1; opacity:.6; flex-shrink:0; transition:opacity .2s ease; }
.kauth-eye:hover,.kauth-eye:focus-visible{ opacity:1; }
.kauth-eye:focus-visible{ outline:2px solid var(--powder); outline-offset:3px; border-radius:6px; }
/* Hide Edge's own built-in reveal button so there aren't two eyes */
.kauth-input input::-ms-reveal,
.kauth-input input::-ms-clear{ display:none; }

.kauth-forgot{ align-self:flex-end; font-size:.83rem; color:var(--walnut); text-decoration:none; margin:-8px 0 22px; font-weight:600; }
.kauth-forgot:hover{ text-decoration:underline; }

.kauth-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kauth-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; line-height:1.5; font-weight:600; }
.kauth-errors p + p{ margin-top:5px; }

.kauth-flash{ background:var(--buttercream); border-left:4px solid var(--walnut); border-radius:12px; padding:12px 16px; margin-bottom:20px; font-size:.85rem; color:var(--walnut); font-weight:600; }

.kauth-remember{ display:flex; align-items:center; gap:9px; margin:-4px 0 22px; cursor:pointer; }
.kauth-remember input{ width:17px; height:17px; accent-color:var(--walnut); cursor:pointer; }
.kauth-remember span{ font-size:.85rem; color:#5c4d3f; font-weight:600; }

.kpz-btn{ display:inline-flex; align-items:center; justify-content:center; padding:11px 22px; border-radius:999px; font-weight:700; font-size:.92rem; text-decoration:none; border:2px solid transparent; transition:transform .18s ease, box-shadow .18s ease; cursor:pointer; }
.kpz-btn-brown{ background:var(--walnut); color:var(--vanilla); }
.kpz-btn-brown:hover{ background:var(--walnut-dark); box-shadow:0 8px 20px rgba(94,68,46,.3); transform:translateY(-2px); }
.kauth-submit{ width:100%; padding:13px; font-size:.98rem; }

.kauth-switch-mobile{ display:none; text-align:center; margin:20px 0 0; font-size:.87rem; color:#5c4d3f; }

/* RESPONSIVE */
@media (max-width:820px){
    .kauth-card{ display:block; position:relative; height:auto; overflow:visible; }
    .kauth-side, .kauth-form-panel{ position:relative !important; left:0 !important; width:100% !important; height:auto; transition:none; }
    .kauth-side{ padding:36px 30px; }
    .kauth-side p{ max-width:none; }
    .kauth-form-panel{ padding:36px 28px; }
    .kauth-form.active{ animation:kauthFormIn .4s ease both; animation-delay:0s; }
}
@media (max-width:600px){
    .kauth{ padding:80px 14px 30px; }
    .kauth-back{ top:18px; left:18px; }
    .kauth-side h1{ font-size:1.6rem; }
    .kauth-switch-mobile{ display:block; }
    .kauth-punchline{ display:none; }
}

/* ===== Mobile fixes ===== */
@media (max-width:820px){
    /* The register height (660px) still applied on phones, so the form spilled out of the white card */
    .kauth-card, .kauth-card.show-register{ height:auto; }
    .kauth-form-panel{ overflow:visible; }
}
@media (max-width:600px){
    .kauth{ padding:62px 12px 100px; }
    .kauth-back{ top:20px; left:16px; }
    .kauth-card{ border-radius:22px; }
    /* shorter brown panel so the form is visible sooner */
    .kauth-side{ padding:24px 22px; }
    .kauth-logo{ margin-bottom:14px; }
    .kauth-side h1{ font-size:1.45rem; margin-bottom:8px; }
    .kauth-side p{ margin-bottom:0; font-size:.9rem; }
    .kauth-blob{ width:150px; height:150px; bottom:-60px; right:-50px; }
    .kauth-form-panel{ padding:26px 20px 28px; }
    .kauth-form h2{ font-size:1.5rem; margin-bottom:20px; }
    .kauth-field{ margin-bottom:14px; }
    .kauth-input input{ font-size:16px; } /* stops iOS zooming in on focus */
    .kauth-forgot{ margin:2px 0 18px; }
    /* the switch link sits on white here, so it needs the dark colour */
    .kauth-switch-mobile .kauth-link-btn{ color:var(--walnut); }
}
</style>

<script>
(function(){
    var card = document.getElementById('authCard');
    var loginForm = document.querySelector('.kauth-form-login');
    var registerForm = document.querySelector('.kauth-form-register');

    function showLogin(){
        card.classList.remove('show-register');
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
    }
    function showRegister(){
        card.classList.add('show-register');
        registerForm.classList.add('active');
        loginForm.classList.remove('active');
    }

    document.getElementById('toRegister').addEventListener('click', showRegister);
    document.getElementById('toLogin').addEventListener('click', showLogin);
    /* On phones the card is stacked, so after switching jump back to the top of the card. */
    function jumpToCard(){
        if (window.matchMedia('(max-width:820px)').matches) {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.querySelectorAll('[data-switch="register"]').forEach(function(b){ b.addEventListener('click', function(){ showRegister(); jumpToCard(); }); });
    document.querySelectorAll('[data-switch="login"]').forEach(function(b){ b.addEventListener('click', function(){ showLogin(); jumpToCard(); }); });

    /* Show / hide password: add an eye button to every password field. */
    document.querySelectorAll('.kauth-input input[type="password"]').forEach(function(input){
        var btn = document.createElement('button');
        btn.type = 'button';                        // never submits the form
        btn.className = 'kauth-eye bx bx-show';
        btn.setAttribute('aria-label', 'Show password');

        btn.addEventListener('click', function(e){
            e.preventDefault();                     // the field sits inside a <label>
            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.classList.toggle('bx-show', showing);
            btn.classList.toggle('bx-hide', !showing);
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            input.focus();
        });

        input.insertAdjacentElement('afterend', btn);
    });

    /* Before submitting, put every field back to type="password",
       so the browser never offers to save it as plain text. */
    document.querySelectorAll('.kauth-form').forEach(function(form){
        form.addEventListener('submit', function(){
            form.querySelectorAll('.kauth-eye').forEach(function(btn){
                var input = btn.previousElementSibling;
                if(input && input.type === 'text') input.type = 'password';
            });
        });
    });

    // If registration failed validation, reopen the register side so the
    // person can see their errors instead of landing back on the login form.
    @if ($errors->any() && old('name'))
        showRegister();
    @endif
})();
</script>
@endsection