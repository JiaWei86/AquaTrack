<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterResidentRequest;
use App\Models\User;
use App\Services\UserFactoryProducer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'states' => User::malaysianStates(),
        ]);
    }

    public function register(RegisterResidentRequest $request)
    {
        $attributes = $request->only(['name', 'email', 'phone', 'state']);
        $attributes['password'] = Hash::make($request->input('password'));
        $attributes['role'] = 'Resident';

        UserFactoryProducer::factory('Resident')->create($attributes);

        return redirect()->route('login')->with('status', 'Registration successful. Please sign in.');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status === 'Inactive') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Session Management: invalidate the session and regenerate the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}