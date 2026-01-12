<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements.
     */
    public function index()
    {
        // Get all announcements ordered by newest first
        $announcements = Announcement::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Store a new announcement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
        ]);

        $validated['user_id'] = Auth::id();

        Announcement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Announcement posted successfully!',
        ]);
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        // Check if user is the creator or an admin
        if (Auth::id() !== $announcement->user_id && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this announcement.',
            ], 403);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully!',
        ]);
    }

    /**
     * Get announcements as JSON (for AJAX loading).
     */
    public function getAnnouncements()
    {
        $announcements = Announcement::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($announcement) {
                // Get user name - try fName/lName first (custom), then fall back to name field
                $userName = 'Unknown User';
                if ($announcement->user) {
                    if ($announcement->user->fName || $announcement->user->lName) {
                        $userName = trim(($announcement->user->fName ?? '') . ' ' . ($announcement->user->lName ?? ''));
                    } elseif ($announcement->user->name) {
                        $userName = $announcement->user->name;
                    }
                }
                
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'user_name' => $userName,
                    'created_at' => $announcement->created_at->format('M d, Y H:i'),
                    'user_id' => $announcement->user_id,
                ];
            });

        return response()->json($announcements);
    }
}
