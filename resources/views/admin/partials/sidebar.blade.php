{{-- Admin sidebar, shared by every admin page.
     Add or rename a menu item here once and every page picks it up. --}}

@php
    $me = auth()->user();

    $initials = collect(explode(' ', $me->name))
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');

    // Anchor links (Appointments, Symptom Reports) point into the dashboard,
    // so only real pages get an "active" state.
    $menu = [
        ['href' => route('admin.dashboard'),                   'icon' => 'bx-grid-alt',      'label' => 'Dashboard',        'active' => request()->routeIs('admin.dashboard')],
        ['href' => route('admin.dashboard') . '#appointments', 'icon' => 'bx-calendar',      'label' => 'Appointments',     'active' => false],
        ['href' => route('admin.walkin'),                      'icon' => 'bx-walk',          'label' => 'Walk-in / Phone',  'active' => request()->routeIs('admin.walkin*')],
        ['href' => route('admin.dashboard') . '#symptoms',     'icon' => 'bx-message-dots',  'label' => 'Symptom Reports',  'active' => false],
        ['href' => route('admin.patients'),                    'icon' => 'bx-user',          'label' => 'Patients',         'active' => request()->routeIs('admin.patients')],
        ['href' => route('admin.settings'),                    'icon' => 'bx-cog',           'label' => 'Settings',         'active' => request()->routeIs('admin.settings*')],
    ];
@endphp

<aside class="kad-side" id="kadSide">
    <a href="{{ route('home') }}" class="kad-logo">
        <span class="kad-logo-dots"><i></i><i></i></span>
        <span>KPZ Admin</span>
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