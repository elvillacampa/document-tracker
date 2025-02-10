<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure to include the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Process the login.
     */
    public function login(Request $request)
    {
        // Validate the login credentials.
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Look up the user by email.
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        // If the user exists and is not approved, block login.
        if ($user && !$user->approved) {
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for an administrator to activate your account.'
            ])->withInput();
        }

        // Attempt to authenticate the user.
        if (Auth::attempt($credentials)) {
            // Regenerate the session to prevent session fixation.
            $user->last_login_at = Carbon::now('Asia/Manila')->format('Y-m-d H:i');
            $user->ip_address = $request->ip();
            $user->save();
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // If authentication fails, return with a generic error message.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }


    /**
     * Log out the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Process the registration.
     */
    public function register(Request $request)
    {
        // Validate the registration form data.
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed', // requires a password_confirmation field
        ]);

        // Create the user.
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Log the user in immediately after registration.
        // Auth::login($user);

        // Redirect to the intended page or default to home.
        return redirect()->intended('/login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'current_password' => ['required', 'current_password'], // checks that the current_password matches the authenticated user's password
            'password' => ['required', 'min:8', 'confirmed'], // new password, must be confirmed
        ]);

        // Update the user's password
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Your password has been changed successfully!');
    }
}
