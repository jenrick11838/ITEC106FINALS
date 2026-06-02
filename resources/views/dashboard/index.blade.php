@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0f172a;line-height:1">{{ $totalUsers }}</div>
                    <div class="text-muted" style="font-size:.75rem">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-journal-text"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0f172a;line-height:1">{{ $totalNotes }}</div>
                    <div class="text-muted" style="font-size:.75rem">Meeting Notes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0f172a;line-height:1">{{ $myNotes }}</div>
                    <div class="text-muted" style="font-size:.75rem">My Notes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-calendar3"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0f172a;line-height:1">{{ $notesThisMonth }}</div>
                    <div class="text-muted" style="font-size:.75rem">This Month</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Notes per month line chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up text-primary me-2"></i>Meeting Notes — Last 6 Months</span>
            </div>
            <div class="card-body">
                <canvas id="notesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <!-- Notes by category doughnut -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <span><i class="bi bi-pie-chart text-primary me-2"></i>Notes by Category</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Second row charts -->
<div class="row g-3 mb-4">
    <!-- Users registered per month bar -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-bar-chart text-primary me-2"></i>User Registrations — Last 6 Months</span>
            </div>
            <div class="card-body">
                <canvas id="usersChart" height="130"></canvas>
            </div>
        </div>
    </div>
    <!-- Recent Notes -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history text-primary me-2"></i>Recent Notes</span>
                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentNotes as $note)
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:.85rem;font-weight:500">{{ Str::limit($note->title, 40) }}</div>
                                <div class="text-muted" style="font-size:.72rem">
                                    <i class="bi bi-person me-1"></i>{{ $note->user->name }}
                                    &nbsp;·&nbsp;{{ $note->meeting_date->format('M d, Y') }}
                                </div>
                            </div>
                            <span class="badge rounded-pill"
                                style="background:{{ $note->category_color }};font-size:.65rem">
                                {{ $note->category }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3" style="font-size:.85rem">No notes yet</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const palette = {
    primary: '#4f46e5', success: '#22c55e', warning: '#f59e0b',
    info: '#3b82f6', danger: '#ef4444', purple: '#a855f7'
};

// ── Notes per Month (Line) ──────────────────────────────────
new Chart(document.getElementById('notesChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyNotesLabels) !!},
        datasets: [{
            label: 'Meeting Notes',
            data: {!! json_encode($monthlyNotesData) !!},
            borderColor: palette.primary,
            backgroundColor: 'rgba(79,70,229,.1)',
            fill: true,
            tension: .4,
            pointBackgroundColor: palette.primary,
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});

// ── Category Doughnut ───────────────────────────────────────
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryLabels) !!},
        datasets: [{
            data: {!! json_encode($categoryData) !!},
            backgroundColor: [palette.primary, palette.success, palette.warning, palette.info, palette.purple, palette.danger],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
        }
    }
});

// ── Users per Month (Bar) ───────────────────────────────────
new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyUsersLabels) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($monthlyUsersData) !!},
            backgroundColor: 'rgba(34,197,94,.8)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection