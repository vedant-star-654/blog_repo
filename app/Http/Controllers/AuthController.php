<?php

namespace App\Http\Controllers;

use App\Mail\UserRegisteredMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,owner'
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Debugging: Check if user is created
        if (!$user) {
            return back()->with('error', 'User could not be created.');
        }

        // Debugging: Check if email is available
        if (!$user->email) {
            return back()->with('error', 'User email is missing.');
        }

        // Send Welcome Email
        try {
            Mail::to($user->email)->send(new UserRegisteredMail($user));
        } catch (\Exception $e) {
            return back()->with('error', 'Mail could not be sent: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Registration successful. A confirmation email has been sent to your email address.');
    }


    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'owner') {
                return redirect()->route('owner.dashboard');
            }
        }

        return back()->with('error', 'Invalid email or password.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
