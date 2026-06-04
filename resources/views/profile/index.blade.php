@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-3">

    {{-- ✅ Fixed: Success flash message --}}
    @if(session('success'))
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show py-2" style="font-size:.85rem">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- ═══════════ LEFT: Avatar + Info Card ═══════════ --}}
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-4">

                {{-- Avatar with camera button --}}
                <div class="position-relative d-inline-block mb-3">
                    <img id="avatarPreview"
                         src="{{ auth()->user()->avatar
                                ? asset('storage/avatars/' . auth()->user()->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=4f46e5&color=fff&size=128' }}"
                         style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #4f46e5"
                         alt="Profile Picture">
                    <label for="avatarInput"
                           style="position:absolute;bottom:0;right:0;background:#4f46e5;color:#fff;
                                  border-radius:50%;width:28px;height:28px;display:flex;
                                  align-items:center;justify-content:center;cursor:pointer;font-size:.8rem"
                           title="Change photo">
                        <i class="bi bi-camera"></i>
                    </label>
                </div>

                <h6 class="fw-bold mb-0">{{ $user->name }}</h6>
                <p class="text-muted mb-2" style="font-size:.8rem">{{ $user->email }}</p>
                <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                    {{ ucfirst($user->role ?? 'member') }}
                </span>

                <hr class="my-3">

                <div class="text-start" style="font-size:.82rem">
                    @if($user->phone)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-phone text-primary"></i>
                        <span>{{ $user->phone }}</span>
                    </div>
                    @endif
                    @if($user->address)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <span>{{ $user->address }}</span>
                    </div>
                    @endif
                    @if($user->gender)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-fill text-primary"></i>
                        <span>{{ ucfirst(str_replace('_', ' ', $user->gender)) }}</span>
                    </div>
                    @endif
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar3 text-primary"></i>
                        <span>Joined {{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                @if($user->bio)
                <hr class="my-3">
                <p class="text-muted mb-0" style="font-size:.82rem;text-align:left">
                    {{ $user->bio }}
                </p>
                @endif
            </div>
        </div>

        {{-- Avatar upload form (hidden input, auto-submits on file select) --}}
        <form method="POST"
              action="{{ route('profile.avatar') }}"
              enctype="multipart/form-data"
              id="avatarForm">
            @csrf
            <input type="file"
                   id="avatarInput"
                   name="avatar"
                   class="d-none"
                   accept="image/*">
        </form>

        {{-- Recent Notes mini list --}}
        <div class="card mt-3">
            <div class="card-header" style="font-size:.85rem">
                <i class="bi bi-clock-history text-primary me-2"></i>My Recent Notes
            </div>
            <ul class="list-group list-group-flush">
                @forelse($notes as $note)
                <li class="list-group-item px-3 py-2">
                    <div style="font-size:.82rem;font-weight:500">
                        {{ Str::limit($note->title, 35) }}
                    </div>
                    <div class="text-muted" style="font-size:.72rem">
                        {{ $note->meeting_date->format('M d, Y') }}
                        &nbsp;·&nbsp;
                        <span style="color:{{ $note->category_color }}">{{ $note->category }}</span>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-3" style="font-size:.82rem">
                    No notes yet
                </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ═══════════ RIGHT: Edit Forms ═══════════ --}}
    <div class="col-lg-8">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" style="font-size:.85rem">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ── Edit Profile Info ── --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square text-primary me-2"></i>Edit Profile Information
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Full Name *</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Email Address *</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Phone Number</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="+63 9XX XXX XXXX">
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Prefer not to say</option>
                                <option value="male"
                                    {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                    Male
                                </option>
                                <option value="female"
                                    {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                    Female
                                </option>
                                <option value="other"
                                    {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>
                                    Other
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Address</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   value="{{ old('address', $user->address) }}"
                                   placeholder="City, Province">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Bio</label>
                            <textarea name="bio"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Tell us a bit about yourself…">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2 me-1"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Change Password ── --}}
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-shield-lock text-primary me-2"></i>Change Password
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name"  value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-500" style="font-size:.85rem">Current Password</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Current password">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-500" style="font-size:.85rem">New Password</label>
                            <input type="password"
                                   name="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   placeholder="Min 8 characters">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-500" style="font-size:.85rem">Confirm New Password</label>
                            <input type="password"
                                   name="new_password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat new password">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-key me-1"></i>Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- /col-lg-8 --}}
</div>{{-- /row --}}
@endsection

@push('scripts')
<script>
// Preview avatar instantly then auto-submit upload form
document.getElementById('avatarInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        document.getElementById('avatarPreview').src = ev.target.result;
        // Short delay so preview renders before page reloads
        setTimeout(() => document.getElementById('avatarForm').submit(), 400);
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
