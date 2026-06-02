<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MeetingNote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ─────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@notehub.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'bio'      => 'System administrator for NoteHub.',
        ]);

        // ── Sample member ──────────────────────────────────────────
        $member = User::create([
            'name'     => 'Jane Dela Cruz',
            'email'    => 'jane@notehub.com',
            'password' => Hash::make('password'),
            'role'     => 'member',
        ]);

        // ── Sample meeting notes ───────────────────────────────────
        MeetingNote::create([
            'user_id'         => $admin->id,
            'title'           => 'Q1 Project Kickoff',
            'category'        => 'Project',
            'meeting_date'    => now()->subDays(30),
            'location'        => 'Conference Room A',
            'content'         => "Discussed Q1 project goals and milestones.\n- Set up project timeline\n- Assigned team roles\n- Identified key deliverables",
            'action_items'    => "1. Prepare project charter\n2. Schedule weekly sync\n3. Set up project board",
            'attendees_count' => 8,
        ]);

        MeetingNote::create([
            'user_id'         => $admin->id,
            'title'           => 'Client Onboarding — Acme Corp',
            'category'        => 'Client',
            'meeting_date'    => now()->subDays(20),
            'location'        => 'Zoom',
            'content'         => "Onboarding meeting with Acme Corp.\n- Reviewed contract scope\n- Introduced project team\n- Discussed communication plan",
            'action_items'    => "1. Send contract summary\n2. Create Slack channel",
            'attendees_count' => 5,
        ]);

        MeetingNote::create([
            'user_id'         => $member->id,
            'title'           => 'Weekly Team Standup',
            'category'        => 'Team',
            'meeting_date'    => now()->subDays(7),
            'location'        => 'Google Meet',
            'content'         => "Weekly standup updates:\n- Dev: API endpoints 80% complete\n- Design: Mockups approved\n- QA: Test plan ready",
            'action_items'    => "1. Fix login bug\n2. Export final mockups",
            'attendees_count' => 6,
        ]);

        MeetingNote::create([
            'user_id'         => $member->id,
            'title'           => 'Product Roadmap Review',
            'category'        => 'Strategy',
            'meeting_date'    => now()->subDays(3),
            'location'        => 'Boardroom',
            'content'         => "Reviewed product roadmap for next quarter.\n- Feature prioritization\n- Budget approval\n- Timeline adjustments",
            'action_items'    => "1. Update roadmap document\n2. Communicate changes to team",
            'attendees_count' => 4,
        ]);

        MeetingNote::create([
            'user_id'         => $admin->id,
            'title'           => 'Sprint Retrospective',
            'category'        => 'Review',
            'meeting_date'    => now()->subDays(1),
            'location'        => 'Online',
            'content'         => "Sprint 3 retrospective.\nWhat went well:\n- Good collaboration\n- All stories delivered\nImprovement:\n- Code review turnaround",
            'action_items'    => "1. Add code review SLA to team norms",
            'attendees_count' => 7,
        ]);
    }
}
