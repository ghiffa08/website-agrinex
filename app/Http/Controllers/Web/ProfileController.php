<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display profile page (redirect to dashboard with hash)
     */
    public function index()
    {
        return redirect('/#profile');
    }

    /**
     * Update user profile information
     * PUT /profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        // Return JSON response for AJAX
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Informasi profil berhasil diperbarui',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Informasi profil berhasil diperbarui');
    }

    /**
     * Update user password (only for non-OAuth accounts)
     * POST /profile/password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Check if user has OAuth login (no local password)
        if ($user->google_id && !$user->password_hash) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun OAuth tidak dapat mengubah sandi. Gunakan login Google.',
                    'errors' => ['password' => ['Akun OAuth tidak dapat mengubah sandi.']]
                ], 403);
            }
            return back()->withErrors(['password' => 'Akun OAuth tidak dapat mengubah sandi. Gunakan login Google.']);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password_hash)) {
                    $fail('Sandi saat ini tidak sesuai.');
                }
            }],
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        $user->update([
            'password_hash' => Hash::make($validated['password']),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sandi berhasil diperbarui'
            ]);
        }

        return back()->with('success', 'Sandi berhasil diperbarui');
    }

    /**
     * Link OAuth account to existing local account
     * POST /profile/link-oauth
     */
    public function linkOAuth(Request $request)
    {
        $user = Auth::user();

        // Only allow linking if user doesn't already have OAuth linked
        if ($user->google_id) {
            return back()->withErrors(['oauth' => 'Akun sudah terhubung dengan Google.']);
        }

        // TODO: Implement OAuth linking logic
        // This would redirect to Google OAuth flow and link the ID to current user

        return back()->with('success', 'Akun berhasil terhubung dengan Google');
    }

    /**
     * Unlink OAuth account
     * POST /profile/unlink-oauth
     */
    public function unlinkOAuth(Request $request)
    {
        $user = Auth::user();

        // Only allow unlinking if user has a local password
        if (!$user->password_hash) {
            return back()->withErrors(['oauth' => 'Tidak dapat melepas akun Google tanpa sandi lokal. Atur sandi terlebih dahulu.']);
        }

        if (!$user->google_id) {
            return back()->withErrors(['oauth' => 'Akun tidak terhubung dengan Google.']);
        }

        $user->update([
            'google_id' => null,
        ]);

        return back()->with('success', 'Akun Google berhasil dilepaskan');
    }

    /**
     * Logout user
     * POST /profile/logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil');
    }

    /**
     * Check password strength
     * GET /profile/password-strength
     */
    public function checkPasswordStrength(Request $request)
    {
        $password = $request->get('password', '');

        $strength = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $strength++;
        } else {
            $feedback[] = 'Minimal 8 karakter';
        }

        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Tambahkan huruf besar (A-Z)';
        }

        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Tambahkan huruf kecil (a-z)';
        }

        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Tambahkan angka (0-9)';
        }

        // Special character check
        if (preg_match('/[!@#$%^&*()_\-+=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Tambahkan karakter spesial (!@#$%^&*)';
        }

        $strengthLevel = match ($strength) {
            0, 1 => 'Sangat Lemah',
            2 => 'Lemah',
            3 => 'Sedang',
            4 => 'Kuat',
            5 => 'Sangat Kuat',
        };

        return response()->json([
            'strength' => $strength,
            'level' => $strengthLevel,
            'feedback' => $feedback,
            'percentage' => ($strength / 5) * 100,
        ]);
    }
}
