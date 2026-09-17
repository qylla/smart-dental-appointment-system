<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Dental Appointment System')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (optional, untuk icon cantik) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
    :root {
        --vanilla: #FFF8E2;
        --buttercream: #FFEFB9;
        --powder: #BCD1DE;
        --walnut: #5E442E;
    }

    body {
        background-color: var(--vanilla);
        overflow-x: hidden;
        margin: 0;
    }

    /* Fade in whole page */
    .fade-in {
        animation: fadeIn 0.8s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Slide up form side */
    .slide-up {
        animation: slideUp 0.9s ease-out;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Slide from left for image side */
    .slide-left {
        animation: slideLeft 0.9s ease-out;
    }
    @keyframes slideLeft {
        from { opacity: 0; transform: translateX(-40px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Floating icon */
    .float-icon {
        animation: floatY 3s ease-in-out infinite;
    }
    @keyframes floatY {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    /* Input focus glow */
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(188,209,222,0.5);
        border-color: var(--powder);
        transition: all 0.3s ease;
    }

    /* Button hover pop */
    .btn-primary {
        background-color: var(--walnut);
        border-color: var(--walnut);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        background-color: #4a3624;
        border-color: #4a3624;
        box-shadow: 0 6px 14px rgba(94,68,46,0.35);
    }

    /* Logo pulse on load */
    .logo-pulse {
        animation: pulseIn 0.7s ease-out;
    }
    @keyframes pulseIn {
        0% { opacity: 0; transform: scale(0.7); }
        70% { transform: scale(1.05); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
</head>
<body>

    @yield('content')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.whatsapp-float')
    
</body>
</html>