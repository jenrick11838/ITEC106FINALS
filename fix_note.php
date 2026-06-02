<?php
$content = <<<'EOT'
<?php
namespace App\Http\Controllers;
use App\Models\MeetingNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingNote::with('user')->where('user_id', Auth::id());
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $notes = $query->latest()->paginate(10)->withQueryString();
        $categories = MeetingNote::where('user_id', Auth::id())->distinct()->pluck('category');
        return view('notes.index', compact('notes', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string'],
            'meeting_date'    => ['required', 'date'],
            'content'         => ['required', 'string'],
            'location'        => ['nullable', 'string', 'max:255'],
            'attendees_count' => ['nullable', 'integer', 'min:1'],
            'action_items'    => ['nullable', 'string'],
        ]);
        MeetingNote::create([
            'user_id'         => Auth::id(),
            'title'           => $request->title,
            'category'        => $request->category,
            'meeting_date'    => $request->meeting_date,
            'content'         => $request->content,
            'location'        => $request->location,
            'attendees_count' => $request->attendees_count ?? 1,
            'action_items'    => $request->action_items,
        ]);
        return back()->with('success', "Note '{$request->title}' saved successfully.");
    }

    public function update(Request $request, MeetingNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            return back()->with('error', 'You can only edit your own notes.');
        }
        $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string'],
            'meeting_date'    => ['required', 'date'],
            'content'         => ['required', 'string'],
            'location'        => ['nullable', 'string', 'max:255'],
            'attendees_count' => ['nullable', 'integer', 'min:1'],
            'action_items'    => ['nullable', 'string'],
        ]);
        $note->update($request->only([
            'title', 'category', 'meeting_date', 'content',
            'location', 'attendees_count', 'action_items',
        ]));
        return back()->with('success', "Note '{$note->title}' updated successfully.");
    }

    public function destroy(MeetingNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            return back()->with('error', 'You can only delete your own notes.');
        }
        $title = $note->title;
        $note->delete();
        return back()->with('success', "Note '{$title}' deleted.");
    }

    public function json(MeetingNote $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'title'           => $note->title,
            'category'        => $note->category,
            'meeting_date'    => $note->meeting_date->format('M d, Y'),
            'location'        => $note->location,
            'content'         => $note->content,
            'action_items'    => $note->action_items,
            'attendees_count' => $note->attendees_count,
        ]);
    }
}
EOT;

$result = file_put_contents('app/Http/Controllers/NoteController.php', $content);
echo $result ? "SUCCESS: wrote $result bytes" : "FAILED: could not write file";