<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\MeetingNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user           = Auth::user();
        $totalUsers     = User::count();
        $totalNotes     = MeetingNote::count();
        $myNotes        = MeetingNote::where('user_id', $user->id)->count();
        $notesThisMonth = MeetingNote::whereMonth('meeting_date', now()->month)
                            ->whereYear('meeting_date', now()->year)->count();

        $monthlyNotesLabels = [];
        $monthlyNotesData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyNotesLabels[] = $month->format('M Y');
            $monthlyNotesData[]   = MeetingNote::whereYear('meeting_date', $month->year)
                                    ->whereMonth('meeting_date', $month->month)->count();
        }

        $monthlyUsersLabels = [];
        $monthlyUsersData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyUsersLabels[] = $month->format('M Y');
            $monthlyUsersData[]   = User::whereYear('created_at', $month->year)
                                    ->whereMonth('created_at', $month->month)->count();
        }

        $categoryGroups = MeetingNote::selectRaw('category, COUNT(*) as total')
                            ->groupBy('category')->pluck('total', 'category');
        $categoryLabels = $categoryGroups->keys()->toArray();
        $categoryData   = $categoryGroups->values()->toArray();
        $recentNotes    = MeetingNote::with('user')->latest()->take(5)->get();

        return view('dashboard.index')
            ->with('totalUsers',         $totalUsers)
            ->with('totalNotes',         $totalNotes)
            ->with('myNotes',            $myNotes)
            ->with('notesThisMonth',     $notesThisMonth)
            ->with('monthlyNotesLabels', $monthlyNotesLabels)
            ->with('monthlyNotesData',   $monthlyNotesData)
            ->with('monthlyUsersLabels', $monthlyUsersLabels)
            ->with('monthlyUsersData',   $monthlyUsersData)
            ->with('categoryLabels',     $categoryLabels)
            ->with('categoryData',       $categoryData)
            ->with('recentNotes',        $recentNotes);
    }
}