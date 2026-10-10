{{-- Dentist sidebar, shared by every doctor page.
     Add or rename a menu item here once and every page picks it up. --}}

@php
    $me  = auth()->user();
    $tab = request('tab');

    $initials = collect(explode(' ', $me->name))
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');

    $onDashboard = request()->routeIs('doctor.dashboard');

    $menu = [
        ['href' => route('doctor.dashboard'),                     'icon' => 'bx-calendar',     'label' => 'My Schedule',     'active' => $onDashboard && ! $tab],
        ['href' => route('doctor.dashboard', ['tab' => 'notes']),   'icon' => 'bx-user-voice',   'label' => 'Consultations',   'active' => $onDashboard && $tab === 'notes'],
        ['href' => route('doctor.dashboard', ['tab' => 'history']), 'icon' => 'bx-folder-open',  'label' => 'Patient Records', 'active' => $onDashboard && $tab === 'history'],
        ['href' => route('doctor.availability'),                  'icon' => 'bx-time-five',    'label' => 'My Availability', 'active' => request()->routeIs('doctor.availability*')],
        ['href' => route('doctor.settings'),                      'icon' => 'bx-cog',          'label' => 'Settings',        'active' => request()->routeIs('doctor.settings*')],
    ];
@endphp

<aside class="kad-side" id="kadSide">
    <a href="{{ route('home') }}" class="kad-logo">
        <span class="kad-logo-dots"><i></i><i></i></span>
        <span>KPZ Doctor</span>
    </a>

    <nav class="kad-menu">
        @foreach ($menu as $item)
            <a href="{{ $item['href'] }}" class="kad-menu-item {{ $item['active'] ? 'active' : '' }}">
                <i class='bx {{ $item['icon'] }}'></i> {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="kad-side-foot">
        <div class="kad-user">
            <span class="kad-avatar">{{ $initials }}</span>
            <div>
                <strong>{{ $me->name }}</strong>
                <span>{{ $me->specialisation ?: 'Dental Surgeon' }}</span>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="kad-logout"><i class='bx bx-log-out'></i> Log out</button>
        </form>
    </div>
</aside>

<div class="kad-overlay" id="kadOverlay"></div>