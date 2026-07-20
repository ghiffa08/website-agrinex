<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if (!$user) {
                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'username' => strtolower(str_replace(' ', '_', $googleUser->getName())) . rand(100, 999),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'viewer', // Default role for new OAuth users
                    'is_active' => true,
                ]);
            } else {
                // Update google ID and avatar if it was a standard email user
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            }

            Auth::login($user);
            
            try {
                $user->updateLastLogin();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to update last login: ' . $e->getMessage());
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Error: ' . $e->getMessage());
            
            return redirect('/login')->withErrors(['error' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }
}
