<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Check for locale in the session (set by the language switcher)
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // 2. If no session, check for browser's preferred language
        else {
            $browserLocale = $request->getPreferredLanguage(['en', 'ar', 'fr', 'ru']);
            App::setLocale($browserLocale);
            // Optionally, store this detected locale in the session for consistency
            Session::put('locale', $browserLocale);
        }

        return $next($request);
    }
}