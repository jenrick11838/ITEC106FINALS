<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MeetingNotes') — NoteHub</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-w: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #4f46e5;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; min-height: 100vh; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform .3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }
        .sidebar-brand span { color: var(--primary); }
        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .nav-section-label {
            font-size: .65rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #475569;
            padding: .5rem 1.5rem;
            margin-top: .5rem;
        }
        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: .6rem 1.5rem;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
            position: relative;
        }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.05);
        }
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(79,70,229,.2);
        }
        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--primary);
            border-radius: 0 2px 2px 0;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.25rem; text-align: center; }
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-user { display: flex; align-items: center; gap: .75rem; }
        .sidebar-user img {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
        }
        .sidebar-user .name { color: #fff; font-size: .8rem; font-weight: 600; line-height: 1.2; }
        .sidebar-user .role { color: var(--sidebar-text); font-size: .7rem; }

        /* ── Main Content ── */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin .3s ease;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 99;
        }
        .topbar .page-title { font-weight: 600; font-size: 1rem; color: #0f172a; margin: 0; }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }

        .page-content { padding: 1.5rem; flex: 1; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: .9rem;
        }
        .stat-card {
            border-radius: 12px;
            border: none;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        .stat-card .icon-box {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        /* ── Buttons ── */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* ── Toast Container ── */
        #toastContainer {
            position: fixed;
            top: 1.25rem; right: 1.25rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        /* ── Auth pages ── */
        .auth-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px rgba(0,0,0,.3);
        }
        .auth-logo { font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .auth-logo span { color: var(--primary); }

        /* ── Table ── */
        .table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; background: #f8fafc; font-weight: 600; }
        .table td { font-size: .875rem; vertical-align: middle; }
        .avatar-sm { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }

        /* ── Badge ── */
        .badge { font-weight: 500; font-size: .7rem; }

        /* Mobile */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@auth
<!-- ═══════════════════════════ SIDEBAR ═══════════════════════════ -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-journal-bookmark-fill" style="color:var(--primary)"></i> <span>Note</span>Hub</h4>
        <small style="color:#475569;font-size:.7rem;">Meeting Management System</small>
    </div>

    <div class="sidebar-nav">
        <p class="nav-section-label">Main</p>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <p class="nav-section-label">Management</p>
        <a href="{{ route('notes.index') }}" class="nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Meeting Notes
        </a>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>

        <p class="nav-section-label">Account</p>
        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            @php
                $avatar = auth()->user()->avatar
                    ? asset('storage/avatars/' . auth()->user()->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=4f46e5&color=fff&size=80';
            @endphp
            <img src="{{ $avatar }}" alt="avatar">
            <div>
                <div class="name">{{ Str::limit(auth()->user()->name, 18) }}</div>
                <div class="role">{{ auth()->user()->role ?? 'Member' }}</div>
            </div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
               class="ms-auto text-danger" title="Logout" style="font-size:1rem">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</nav>

<!-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ -->
<div id="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h6 class="page-title">@yield('page-title', 'Dashboard')</h6>
        </div>
        <div class="topbar-right">
            <span class="text-muted d-none d-sm-block" style="font-size:.8rem">
                <i class="bi bi-calendar3"></i> {{ now()->format('M d, Y') }}
            </span>
            <a href="{{ route('profile.index') }}" class="text-decoration-none">
                <img src="{{ $avatar }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--primary)" alt="">
            </a>
        </div>
    </div>

    <div class="page-content">
@endauth

@guest
<div class="auth-wrapper">
@endguest

<!-- ═══════════════════════════ TOAST CONTAINER ═══════════════════════════ -->
<div id="toastContainer"></div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('{{ session('success') }}', 'success');
    });
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('{{ session('error') }}', 'danger');
    });
</script>
@endif
@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('{{ session('info') }}', 'info');
    });
</script>
@endif

@yield('content')

@auth
    </div><!-- /page-content -->
</div><!-- /main-content -->
@endauth

@guest
</div><!-- /auth-wrapper -->
@endguest

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Toast Helper ──────────────────────────────────────────────
function showToast(message, type = 'success', duration = 4000) {
    const icons = {
        success: 'bi-check-circle-fill',
        danger:  'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill'
    };
    const colors = {
        success: '#22c55e', danger: '#ef4444', warning: '#f59e0b', info: '#3b82f6'
    };
    const id = 'toast-' + Date.now();
    const html = `
        <div id="${id}" style="
            background:#fff;
            border-radius:10px;
            box-shadow:0 4px 20px rgba(0,0,0,.12);
            padding:.85rem 1rem;
            display:flex;
            align-items:center;
            gap:.75rem;
            min-width:280px;
            max-width:380px;
            border-left:4px solid ${colors[type]};
            animation: slideIn .3s ease;
        ">
            <i class="bi ${icons[type]}" style="color:${colors[type]};font-size:1.1rem;flex-shrink:0"></i>
            <span style="font-size:.875rem;color:#0f172a;flex:1">${message}</span>
            <button onclick="removeToast('${id}')" style="background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:1rem">
                <i class="bi bi-x"></i>
            </button>
        </div>`;
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);
    setTimeout(() => removeToast(id), duration);
}
function removeToast(id) {
    const el = document.getElementById(id);
    if (el) { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; setTimeout(() => el.remove(), 300); }
}

// ── Sidebar Toggle (mobile) ───────────────────────────────────
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// ── Auto-dismiss alerts ───────────────────────────────────────
document.querySelectorAll('.alert').forEach(el => setTimeout(() => el.remove(), 5000));
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);   opacity: 1; }
}
</style>

@stack('scripts')
</body>
</html>