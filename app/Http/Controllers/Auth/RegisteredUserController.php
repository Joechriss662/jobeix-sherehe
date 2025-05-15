<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'regex:/^[a-zA-Z]+(\.[a-zA-Z]+)+$/', 'unique:users'], // Username validation
            'phone' => ['required', 'string', 'regex:/^0[1-9][0-9]{8}$/', 'unique:users'], // Phone number validation
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'], // Email is optional
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email, // Can be null
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

       // Auth::login($user);

        return redirect('login')->with('success', 'Registration successful! Please log in.');
    }
}
