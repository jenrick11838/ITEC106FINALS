@extends('layouts.app')
@section('title', 'Sign In')

@section('content')
<div class="auth-card">
    <div class="text-center mb-4">
        <div class="auth-logo mb-2"><i class="bi bi-journal-bookmark-fill" style="color:#4f46e5"></i> <span>Note</span>Hub</div>
        <h5 class="fw-600 mb-1">Welcome back</h5>
        <p class="text-muted" style="font-size:.85rem">Sign in to your account</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 d-flex align-items-center gap-2" style="font-size:.85rem">
            <i class="bi bi-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif
    @if(session('status'))
        <div class="alert alert-success py-2" style="font-size:.85rem">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-500" style="font-size:.85rem">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                    placeholder="you@example.com" required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-500 mb-0" style="font-size:.85rem">Password</label>
                <a href="#" class="text-primary" style="font-size:.8rem">Forgot password?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="password"
                    class="form-control border-start-0 ps-0 border-end-0 @error('password') is-invalid @enderror"
                    placeholder="Your password" required>
                <span class="input-group-text bg-light" style="cursor:pointer" onclick="togglePw()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>
        <div class="mb-4 form-check" style="font-size:.85rem">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Keep me signed in</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-600">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
        </button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.85rem">
        Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-500">Create one</a>
    </p>
</div>

@push('scripts')
<script>
function togglePw() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') { input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
    else                           { input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
}
</script>
@endpush
@endsection