@extends('layouts.app')
@section('title', 'Create Account')

@section('content')
<div class="auth-card">
    <div class="text-center mb-4">
        <div class="auth-logo mb-2"><i class="bi bi-journal-bookmark-fill" style="color:#4f46e5"></i> <span>Note</span>Hub</div>
        <h5 class="fw-600 mb-1">Create your account</h5>
        <p class="text-muted" style="font-size:.85rem">Join NoteHub to manage your meeting notes</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2" style="font-size:.85rem">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-500" style="font-size:.85rem">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                    placeholder="Juan dela Cruz" required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-500" style="font-size:.85rem">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                    placeholder="you@example.com" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-500" style="font-size:.85rem">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="password"
                    class="form-control border-start-0 ps-0 border-end-0 @error('password') is-invalid @enderror"
                    placeholder="At least 8 characters" required>
                <span class="input-group-text bg-light" style="cursor:pointer" onclick="togglePw('password','eyeIcon1')">
                    <i class="bi bi-eye" id="eyeIcon1"></i>
                </span>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-500" style="font-size:.85rem">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                <input type="password" name="password_confirmation"
                    class="form-control border-start-0 ps-0"
                    placeholder="Repeat password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-600">
            <i class="bi bi-person-plus me-1"></i> Create Account
        </button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.85rem">
        Already have an account? <a href="{{ route('login') }}" class="text-primary fw-500">Sign in</a>
    </p>
</div>

@push('scripts')
<script>
function togglePw(id, iconId) {
    const input = document.getElementById(id);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') { input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
    else                           { input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
}
</script>
@endpush
@endsection