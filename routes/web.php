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
    Route::get('quran', [PageController::class, 'quran'])->name('quran');
    Route::get('azkar', [PageController::class, 'azkar'])->name('azkar');
    Route::get('progress', [PageController::class, 'progress'])->name('progress');
    Route::get('account', [PageController::class, 'account'])->name('account');
    Route::post('account/prayer', [PageController::class, 'updatePrayer'])->name('account.prayer');

    // Tracking actions
    Route::post('track/prayer/{prayer}', [\App\Http\Controllers\TrackController::class, 'prayer'])->name('track.prayer');
    Route::post('track/sadaka', [\App\Http\Controllers\TrackController::class, 'sadaka'])->name('track.sadaka');

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
