<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'meeting_date',
        'location',
        'content',
        'action_items',
        'attendees_count',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ────────────────────────────────────────────────
    /**
     * Returns a color hex string based on the category value.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'Project'  => '#4f46e5',
            'Team'     => '#22c55e',
            'Client'   => '#f59e0b',
            'Strategy' => '#3b82f6',
            'Review'   => '#a855f7',
            default    => '#64748b',
        };
    }
}