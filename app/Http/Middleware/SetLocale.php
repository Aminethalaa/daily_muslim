<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve the active locale from the authenticated user, the session, or
     * the app default — then apply it for the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported'));
        $default = config('locales.default', 'ar');

        $locale = $request->user()?->locale
            ?? $request->session()->get('locale')
            ?? $default;

        if (! in_array($locale, $supported, true)) {
            $locale = $default;
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
