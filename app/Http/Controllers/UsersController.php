<?php

namespace App\Http\Controllers;
date_default_timezone_set('Asia/Manila');

use App\Models\Roles;
use App\Models\User;
use App\Models\locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request) {
        // Count users
        $userCount = User::count();
    
        // Start building the query
        $query = User::with('roles');
    
        // Check for search input
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
    
            $query->where(function ($q) use ($searchTerm) {
                $q->where('users.id', 'LIKE', "%$searchTerm%")
                ->orWhere('users.fName', 'LIKE', "%$searchTerm%")
                ->orWhere('users.lName', 'LIKE', "%$searchTerm%")
                ->orWhere('users.email', 'LIKE', "%$searchTerm%")
                ->orWhere('users.location', 'LIKE', "%$searchTerm%")
                ->orWhereHas('roles', function ($q) use ($searchTerm) {
                    $q->where('roles.roles', 'LIKE', "%$searchTerm%");
                }); 
            });
        }
    
        // Paginate results (10 per page)
        $users = $query->paginate(10)->appends(['search' => $request->search]);

        // Check if no results were found
        if ($users->isEmpty()) {
            // Get all locations from the database
            $locations = locations::all();
            
            return view('users.index', [
                'users' => $users,
                'lastLogin' => [],
                'userCount' => $userCount,
                'searchMessage' => 'The search term "' . $request->search . '" did not match any records.',
                'locations' => $locations
            ]);
        }
    
        // Fetch last login time
        $lastLogin = [];
        foreach ($users as $user) {
            $session = DB::table('sessions')->where('user_id', $user->id)->first();
            $lastLogin[] = [
                'id' => $user->id,
                'last' => $session ? date('Y-m-d H:i', $session->last_activity) : null,
            ];
        }
    
        // Pass the authenticated user to the view
        $authenticatedUser = auth()->user();
        
        // Get all locations from the database
        $locations = locations::all();

        return view('users.index', compact('users', 'lastLogin', 'userCount', 'authenticatedUser', 'locations'));
    }
    

    public function store(Request $request) {
        $request->validate([
            'fName' => ['required', 'string', 'max:255'],
            'lName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string'],
            'location' => ['required', 'string'],
        ]);

        $user = User::create([
            'fName' => $request->fName,
            'lName' => $request->lName,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make("Pass1234"),
            'location' => $request->location,
        ]);

        Roles::create([
            'user_id' => $user->id,
            'roles' => $request->role,
        ]);

        $currentPage = $request->input('page', 1); // Get the current page number

        return redirect()->route('users', ['page' => $currentPage]);
    }

    public function destroy($id) {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the item
        $user->delete();

        $currentPage = request('page', 1); // Get the current page number from the request

        return redirect()->route('users', ['page' => $currentPage])->with('success', 'User deleted successfully.');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'fName' => ['required', 'string', 'max:255'],
            'lName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'string'],
            'location' => ['required', 'string'], // Removed 'in:Manila,Batanes'
        ]);

        $user = User::findOrFail($id);

        // Update user details
        $user->update([
            'fName' => $request->fName,
            'lName' => $request->lName,
            'phone' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
        ]);

        $user->refresh(); // Refresh the user object to ensure it reflects the latest data from the database

        // Update user role
        $role = Roles::where('user_id', $user->id)->first();
        if ($role) {
            $role->update(['roles' => $request->role]);
        } else {
            Roles::create([
                'user_id' => $user->id,
                'roles' => $request->role,
            ]);
        }

        $page = $request->input('page', 1);
        return redirect()->route('users', ['page' => $page])->with('success', 'User updated successfully!');
    }

    public function add(Request $request) {
        return view('auth.register');
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

}
