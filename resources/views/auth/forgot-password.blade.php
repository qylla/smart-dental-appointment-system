@extends('layouts.app')

@section('title', 'Forgot password - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="kpw">
    <div class="kpw-card">
        <a href="{{ route('home') }}" class="kpw-logo">
            <span class="kpw-dots"><i></i><i></i></span> KPZ Dentalcare
        </a>

        <h1>Forgot your password?</h1>
        <p class="kpw-sub">Enter the email you registered with. We'll send you a link to set a new password.</p>

        @if (session('status'))
            <div class="kpw-flash"><i class='bx bx-envelope'></i> <span>{{ session('status') }}</span></div>
        @endif

        @if ($errors->any())
            <div class="kpw-errors">
                @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <label class="kpw-field"><span>Email address</span>
                <div class="kpw-input"><i class='bx bxs-envelope'></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            </label>
            <button type="submit" class="kpw-btn"><i class='bx bx-send'></i> Send reset link</button>
        </form>

        <a href="{{ route('login') }}" class="kpw-back"><i class='bx bx-arrow-back'></i> Back to log in</a>
    </div>
</div>

<style>
:root{
    --vanilla:#FBF8F3; --buttercream:#EFE6DA; --powder:#B7C4B1;
    --walnut:#5E442E; --walnut-dark:#2f2318; --ink:#3a2c20; --muted:#7a6a5a;
}
.kpw{ min-height:100vh; background:var(--walnut-dark); display:flex; align-items:center; justify-content:center; padding:30px 18px; font-family:'Plus Jakarta Sans',sans-serif; color:var(--ink); }
.kpw-card{ width:100%; max-width:430px; background:var(--vanilla); border-radius:26px; padding:34px 30px; box-shadow:0 30px 60px -30px rgba(0,0,0,.6); }
.kpw-logo{ display:flex; align-items:center; gap:10px; text-decoration:none; font-family:'Fraunces',serif; font-weight:600; color:var(--walnut); margin-bottom:24px; }
.kpw-dots{ position:relative; display:flex; width:32px; height:20px; align-items:center; }
.kpw-dots i{ position:absolute; width:20px; height:20px; border-radius:50%; }
.kpw-dots i:first-child{ left:0; background:var(--buttercream); border:1px solid rgba(94,68,46,.2); }
.kpw-dots i:last-child{ left:12px; background:var(--powder); }
.kpw h1{ font-family:'Fraunces',serif; font-size:1.6rem; font-weight:600; color:var(--walnut); margin:0 0 8px; letter-spacing:-.02em; }
.kpw-sub{ font-size:.9rem; color:var(--muted); line-height:1.55; margin:0 0 24px; }
.kpw-flash{ display:flex; gap:9px; align-items:flex-start; background:#dff0d8; color:#3f6b3f; border-radius:14px; padding:13px 16px; font-size:.87rem; font-weight:600; margin-bottom:20px; line-height:1.5; }
.kpw-flash i{ font-size:1.1rem; margin-top:1px; }
.kpw-errors{ background:#fbdada; border-left:4px solid #a13d3d; border-radius:12px; padding:12px 16px; margin-bottom:20px; }
.kpw-errors p{ margin:0; font-size:.85rem; color:#8a2f2f; font-weight:600; line-height:1.5; }
.kpw-errors p + p{ margin-top:5px; }
.kpw-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
.kpw-field > span{ font-size:.8rem; font-weight:800; color:var(--walnut); }
.kpw-input{ display:flex; align-items:center; gap:10px; border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px; background:#fff; transition:border-color .2s, box-shadow .2s; }
.kpw-input:focus-within{ border-color:var(--powder); box-shadow:0 0 0 3px rgba(183,196,177,.4); }
.kpw-input i{ color:var(--walnut); font-size:1.1rem; }
.kpw-input input{ border:none; background:none; outline:none; width:100%; font-family:inherit; font-size:.92rem; color:var(--ink); }
.kpw-input input:-webkit-autofill{ -webkit-box-shadow:0 0 0 1000px #fff inset; transition:background-color 9999s; }
.kpw-btn{ width:100%; display:inline-flex; align-items:center; justify-content:center; gap:7px; border:none; border-radius:999px; padding:13px 24px; background:var(--walnut); color:var(--vanilla); font-family:inherit; font-weight:700; font-size:.92rem; cursor:pointer; margin-top:6px; transition:background .2s, transform .2s; }
.kpw-btn:hover{ background:var(--walnut-dark); transform:translateY(-2px); }
.kpw-back{ display:flex; align-items:center; justify-content:center; gap:6px; margin-top:20px; font-size:.87rem; font-weight:700; color:var(--walnut); text-decoration:none; }
.kpw-back:hover{ text-decoration:underline; }

/* ===== Mobile fixes ===== */
@media (max-width:480px){
    .kpw{ align-items:flex-start; padding:28px 14px 100px; }
    .kpw-card{ padding:26px 20px; border-radius:22px; }
    .kpw h1{ font-size:1.4rem; }
    .kpw-input input{ font-size:16px; } /* stops iOS zooming in on focus */
}
</style>
@endsection