<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('welcome');

// ---- Guest (unauthenticated) -----------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.store');

    // Google OAuth
    Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

// ---- Authenticated ---------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email verification
    Route::get('verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('verify-email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');

    // App
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('quran', [\App\Http\Controllers\QuranController::class, 'index'])->name('quran');
    Route::get('quran/surah/{surah}', [\App\Http\Controllers\QuranController::class, 'show'])->name('quran.show');
    Route::post('quran/log', [\App\Http\Controllers\QuranController::class, 'logReading'])->name('quran.log');
    Route::post('quran/bookmark', [\App\Http\Controllers\QuranController::class, 'bookmark'])->name('quran.bookmark');
    Route::get('azkar', [PageController::class, 'azkar'])->name('azkar');
    Route::get('azkar/{key}', \App\Livewire\AzkarSession::class)->name('azkar.session');
    Route::get('progress', [PageController::class, 'progress'])->name('progress');
    Route::get('account', [PageController::class, 'account'])->name('account');
    Route::post('account/prayer', [PageController::class, 'updatePrayer'])->name('account.prayer');
    Route::post('account/notifications', [PageController::class, 'updateNotifications'])->name('account.notifications');

    // Web push
    Route::post('push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');

    // Tracking actions
    Route::post('track/prayer/{prayer}', [\App\Http\Controllers\TrackController::class, 'prayer'])->name('track.prayer');

    // Sadaka
    Route::get('sadaka', [\App\Http\Controllers\SadakaController::class, 'index'])->name('sadaka');
    Route::post('sadaka', [\App\Http\Controllers\SadakaController::class, 'store'])->name('sadaka.store');
    Route::post('sadaka/goal', [\App\Http\Controllers\SadakaController::class, 'setGoal'])->name('sadaka.goal');
    Route::delete('sadaka/{log}', [\App\Http\Controllers\SadakaController::class, 'destroy'])->name('sadaka.destroy');

    // Locale switch
    Route::post('locale', function (\Illuminate\Http\Request $request) {
        $locale = $request->input('locale');
        if (array_key_exists($locale, config('locales.supported'))) {
            $request->user()->update(['locale' => $locale]);
            $request->session()->put('locale', $locale);
        }

        return back();
    })->name('locale.set');
});
