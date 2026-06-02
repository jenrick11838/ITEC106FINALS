<?php
// Create the directory if it doesn't exist
if (!is_dir('resources/views/notes')) {
    mkdir('resources/views/notes', 0755, true);
    echo "Created notes directory\n";
}

$content = <<<'EOT'
@extends('layouts.app')
@section('title','Meeting Notes')
@section('page-title','Meeting Notes')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text text-primary me-2"></i>My Meeting Notes</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
            <i class="bi bi-plus-lg me-1"></i> New Note
        </button>
    </div>
    <div class="card-body border-bottom py-2">
        <form method="GET" action="{{ route('notes.index') }}" class="row g-2 align-items-center">
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search title…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary">Filter</button>
                @if(request()->hasAny(['search','category']))
                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Meeting Date</th>
                    <th>Attendees</th>
                    <th>Created</th>
                    <th width="110">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-size:.875rem;font-weight:500">{{ Str::limit($note->title,45) }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ Str::limit(strip_tags($note->content),60) }}</div>
                    </td>
                    <td>
                        <span class="badge rounded-pill" style="background:{{ $note->category_color }};font-size:.7rem">
                            {{ $note->category }}
                        </span>
                    </td>
                    <td style="font-size:.8rem">{{ $note->meeting_date->format('M d, Y') }}</td>
                    <td style="font-size:.8rem">{{ $note->attendees_count }} people</td>
                    <td class="text-muted" style="font-size:.8rem">{{ $note->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-info btn-icon" onclick="viewNote({{ $note->id }})" title="View"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-primary btn-icon" onclick="editNote({{ $note->id }},{{ json_encode($note) }})" title="Edit"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger btn-icon" onclick="deleteNote({{ $note->id }},'{{ addslashes($note->title) }}')" title="Delete"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-journal-x d-block mb-2" style="font-size:2rem"></i>
                        No meeting notes yet. Click <strong>New Note</strong> to add one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notes->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-end">{{ $notes->links() }}</div>
    @endif
</div>

{{-- ADD NOTE MODAL --}}
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-journal-plus text-primary me-2"></i>New Meeting Note</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('notes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="Meeting title" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                <option value="Project">Project</option>
                                <option value="Team">Team</option>
                                <option value="Client">Client</option>
                                <option value="Strategy">Strategy</option>
                                <option value="Review">Review</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Meeting Date *</label>
                            <input type="date" name="meeting_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Room / Online">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Attendees Count</label>
                            <input type="number" name="attendees_count" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Meeting Notes *</label>
                            <textarea name="content" class="form-control" rows="5" placeholder="Write meeting notes…" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Action Items</label>
                            <textarea name="action_items" class="form-control" rows="2" placeholder="List of action items…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT NOTE MODAL --}}
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-pencil text-primary me-2"></i>Edit Meeting Note</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editNoteForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Title *</label>
                            <input type="text" name="title" id="eTitle" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Category *</label>
                            <select name="category" id="eCategory" class="form-select" required>
                                <option value="Project">Project</option>
                                <option value="Team">Team</option>
                                <option value="Client">Client</option>
                                <option value="Strategy">Strategy</option>
                                <option value="Review">Review</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Meeting Date *</label>
                            <input type="date" name="meeting_date" id="eMeetingDate" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Location</label>
                            <input type="text" name="location" id="eLocation" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-500" style="font-size:.85rem">Attendees Count</label>
                            <input type="number" name="attendees_count" id="eAttendees" class="form-control" min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Meeting Notes *</label>
                            <textarea name="content" id="eContent" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-500" style="font-size:.85rem">Action Items</label>
                            <textarea name="action_items" id="eActionItems" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Update Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VIEW NOTE MODAL --}}
<div class="modal fade" id="viewNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="vTitle"></h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <span class="badge rounded-pill" id="vCategory"></span>
                    <span class="text-muted" style="font-size:.8rem" id="vDate"></s