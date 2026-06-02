@extends('layouts.app')
@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people text-primary me-2"></i>All Users</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-1"></i> Add User
        </button>
    </div>

    <!-- Search / Filter -->
    <div class="card-body border-bottom py-2">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-sm-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search name or email…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary">Filter</button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-ghost ms-1">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Notes</th>
                    <th>Joined</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $user->avatar ? asset('storage/avatars/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff&size=64' }}"
                                class="avatar-sm" alt="">
                            <span class="fw-500" style="font-size:.875rem">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary bg-opacity-25 text-secondary' }}">
                            {{ ucfirst($user->role ?? 'member') }}
                        </span>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $user->notes_count }}</span></td>
                    <td class="text-muted" style="font-size:.8rem">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary btn-icon"
                                onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}')"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->id() !== $user->id)
                            <button class="btn btn-sm btn-outline-danger btn-icon"
                                onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- ══════════ ADD USER MODAL ══════════ -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-600"><i class="bi bi-person-plus text-primary me-2"></i>Add New User</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Role</label>
                        <select name="role" class="form-select">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════ EDIT USER MODAL ══════════ -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-600"><i class="bi bi-pencil text-primary me-2"></i>Edit User</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editUserForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Full Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">New Password <span class="text-muted">(leave blank to keep)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="New password (optional)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500" style="font-size:.85rem">Role</label>
                        <select name="role" id="editRole" class="form-select">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteUserForm" method="POST" class="d-none">@csrf @method('DELETE')</form>

@push('styles')
<style>
.btn-icon { width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center; }
</style>
@endpush

@push('scripts')
<script>
function openEditModal(id, name, email, role) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value  = role || 'member';
    document.getElementById('editUserForm').action = '/users/' + id;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function confirmDelete(id, name) {
    if (confirm(`Delete user "${name}"? This cannot be undone.`)) {
        const form = document.getElementById('deleteUserForm');
        form.action = '/users/' + id;
        form.submit();
    }
}
</script>
@endpush
@endsection