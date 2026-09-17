@extends('layouts.app')

@section('title', 'Settings - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<div class="kset-wrap">
    <a href="{{ route('doctor.dashboard') }}" class="kset-back">
        <i class='bx bx-arrow-back'></i> Back to dashboard
    </a>

    <div class="kset-header">
        <h1>Settings</h1>
        <p>Manage your profile, password, and notification preferences.</p>
    </div>

    @if (session('status'))
        <div class="kset-alert kset-alert-success">
            @switch(session('status'))
                @case('profile-updated') Profile updated successfully. @break
                @case('password-updated') Password changed successfully. @break
                @case('notifications-updated') Notification preferences saved. @break
                @default Saved.
            @endswitch
        </div>
    @endif

    <div class="kset-tabs" role="tablist">
        <button class="kset-tab active" data-tab="profile">Profile</button>
        <button class="kset-tab" data-tab="password">Password</button>
        <button class="kset-tab" data-tab="notifications">Notifications</button>
    </div>

    {{-- Profile tab --}}
    <div class="kset-panel" id="panel-profile">
        <form method="POST" action="{{ route('doctor.settings.profile') }}">
            @csrf
            @method('PATCH')

            <div class="kset-field">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $doctor->name) }}" required>
                @error('name') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <div class="kset-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $doctor->email) }}" required>
                @error('email') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <div class="kset-field">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $doctor->phone) }}">
                @error('phone') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <div class="kset-field">
                <label for="specialisation">Specialisation</label>
                <input type="text" id="specialisation" name="specialisation" value="{{ old('specialisation', $doctor->specialisation) }}">
                @error('specialisation') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="kset-btn kset-btn-primary">Save profile</button>
        </form>
    </div>

    {{-- Password tab --}}
    <div class="kset-panel" id="panel-password" style="display:none;">
        <form method="POST" action="{{ route('doctor.settings.password') }}">
            @csrf
            @method('PUT')

            <div class="kset-field">
                <label for="current_password">Current password</label>
                <input type="password" id="current_password" name="current_password" required>
                @error('current_password') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <div class="kset-field">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" required>
                @error('password') <span class="kset-error">{{ $message }}</span> @enderror
            </div>

            <div class="kset-field">
                <label for="password_confirmation">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="kset-btn kset-btn-primary">Change password</button>
        </form>
    </div>

    {{-- Notifications tab --}}
    <div class="kset-panel" id="panel-notifications" style="display:none;">
        <form method="POST" action="{{ route('doctor.settings.notifications') }}">
            @csrf
            @method('PATCH')

            <div class="kset-field kset-field-checkbox">
                <label>
                    <input type="checkbox" name="notify_new_appointment" value="1" @checked($doctor->notify_new_appointment)>
                    Notify me when a new appointment is booked
                </label>
            </div>

            <div class="kset-field kset-field-checkbox">
                <label>
                    <input type="checkbox" name="notify_cancellation" value="1" @checked($doctor->notify_cancellation)>
                    Notify me when an appointment is cancelled
                </label>
            </div>

            <div class="kset-field kset-field-checkbox">
                <label>
                    <input type="checkbox" name="notify_daily_summary" value="1" @checked($doctor->notify_daily_summary)>
                    Send me a daily summary of tomorrow's appointments
                </label>
            </div>

            <button type="submit" class="kset-btn kset-btn-primary">Save preferences</button>
        </form>
    </div>
</div>

<style>
.kset-wrap{ max-width:640px; margin:40px auto; padding:0 20px; font-family:'Plus Jakarta Sans',sans-serif; }
.kset-back{ display:inline-flex; align-items:center; gap:7px; text-decoration:none; color:var(--walnut,#5E442E); font-weight:700; font-size:.88rem; margin-bottom:18px; }
.kset-back:hover{ text-decoration:underline; }
.kset-back i{ font-size:1.1rem; }
.kset-header h1{ font-family:'Fraunces',serif; color:var(--walnut,#5E442E); margin:0; font-size:1.8rem; }
.kset-header p{ color:var(--muted,#7a6a5a); margin:6px 0 24px; font-size:.92rem; }
.kset-alert{ padding:12px 18px; border-radius:12px; margin-bottom:18px; font-size:.9rem; font-weight:600; }
.kset-alert-success{ background:#dff0d8; color:#3f6b3f; }
.kset-tabs{ display:flex; gap:6px; background:var(--buttercream,#FFEFB9); padding:4px; border-radius:999px; margin-bottom:22px; width:fit-content; }
.kset-tab{ border:none; background:none; padding:9px 18px; border-radius:999px; font-size:.85rem; font-weight:700; color:var(--walnut,#5E442E); cursor:pointer; font-family:inherit; }
.kset-tab.active{ background:var(--walnut,#5E442E); color:var(--vanilla,#FFF8E2); }
.kset-panel{ background:var(--vanilla,#FFF8E2); border:1px solid rgba(94,68,46,.08); border-radius:20px; padding:26px; }
.kset-field{ display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.kset-field label{ font-size:.8rem; font-weight:800; color:var(--walnut,#5E442E); }
.kset-field input[type="text"],
.kset-field input[type="email"],
.kset-field input[type="password"]{
    border:1px solid rgba(94,68,46,.22); border-radius:14px; padding:12px 16px;
    font-family:inherit; font-size:.92rem; color:#3a2c20; background:#fff; outline:none;
}
.kset-field input:focus{ border-color:var(--powder,#BCD1DE); box-shadow:0 0 0 3px rgba(188,209,222,.4); }
.kset-field-checkbox label{ display:flex; align-items:center; gap:10px; font-weight:600; font-size:.9rem; color:#3a2c20; }
.kset-field-checkbox input{ width:18px; height:18px; }
.kset-error{ font-size:.78rem; color:#a13d3d; font-weight:600; }
.kset-btn{ display:inline-flex; align-items:center; justify-content:center; border-radius:999px; font-weight:700; font-size:.9rem; border:none; cursor:pointer; padding:12px 26px; font-family:inherit; }
.kset-btn-primary{ background:var(--walnut,#5E442E); color:var(--vanilla,#FFF8E2); }
.kset-btn-primary:hover{ background:#2f2318; }
</style>

<script>
document.querySelectorAll('.kset-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.kset-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.kset-panel').forEach(p => p.style.display = 'none');
        tab.classList.add('active');
        document.getElementById('panel-' + tab.dataset.tab).style.display = 'block';
    });
});
</script>
@endsection