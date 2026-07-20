<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('agrinex.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            // Find user by username or email
            $user = User::where('username', $request->username)
                        ->orWhere('email', $request->username)
                        ->first();

            // Check if user exists and password is correct
            if ($user && Hash::check($request->password, $user->password_hash)) {
                // Check if user is active
                if (!$user->is_active) {
                    return back()->withErrors([
                        'username' => 'Your account has been deactivated. Please contact administrator.',
                    ])->withInput($request->only('username'));
                }

                // Login the user
                Auth::login($user, $request->filled('remember'));

                // Update last login timestamp
                try {
                    $user->updateLastLogin();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to update last login: ' . $e->getMessage());
                }

                // Regenerate session (with error handling for WebView)
                try {
                    $request->session()->regenerate();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Session regenerate failed: ' . $e->getMessage());
                }

                // Redirect based on role
                return $this->redirectBasedOnRole($user);
            }

            // Authentication failed
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('username'));
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login error: ' . $e->getMessage());
            
            return back()->withErrors([
                'username' => 'Login failed. Please try again.',
            ])->withInput($request->only('username'));
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(User $user)
    {
        $intendedUrl = session()->pull('url.intended', route('agrinex.dashboard'));
        
        // Admin and Operator can access everything
        if ($user->isAdmin() || $user->isOperator()) {
            return redirect()->intended($intendedUrl);
        }

        // Viewer can only view, redirect to dashboard
        if ($user->isViewer()) {
            return redirect()->route('agrinex.dashboard');
        }

        // Default redirect
        return redirect()->route('agrinex.dashboard');
    }
}
