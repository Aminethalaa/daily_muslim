<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.google.client_id')) {
            return redirect()->route('login')->with('error', __('auth.continue_google').' — غير مُفعّل بعد.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(UserProvisioner $provisioner): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', __('auth.failed'));
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'مستخدم',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'locale' => app()->getLocale(),
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId(), 'avatar' => $googleUser->getAvatar()]);
        }

        $provisioner->provision($user);
        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
