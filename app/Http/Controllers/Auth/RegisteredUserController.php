<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        //return view('auth.register');
        return redirect()->route('login');

    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'fName' => ['required', 'string', 'max:255'],
            'lName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'size:11', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            //'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'],
        ]);

        $user = User::create([
            'fName' => $request->fName,
            'lName' => $request->lName,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make("Pass1234"),
        ]);

        $store = $user->id;

        Roles::create([
            'user_id' => $store,
            'roles' => $request->role,

        ]);

        return redirect(route('login', absolute: false));
    }
}
