<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Exception $e) {
                Log::info('Socialite stateful OAuth driver failed, falling back to stateless: ' . $e->getMessage());
                $googleUser = Socialite::driver('google')->stateless()->user();
            }

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if (!$user) {
                $rawName = $googleUser->getName() ?? $googleUser->getNickname() ?? 'user';
                $cleanUsername = Str::slug($rawName, '_') . rand(100, 999);

                $user = User::create([
                    'full_name' => $rawName,
                    'username' => $cleanUsername,
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

            Auth::login($user, true);
            
            try {
                $user->updateLastLogin();
            } catch (\Exception $e) {
                Log::warning('Failed to update last login: ' . $e->getMessage());
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return redirect('/login')->withErrors(['error' => 'Gagal login dengan Google (' . $e->getMessage() . '). Silakan coba lagi.']);
        }
    }
}
