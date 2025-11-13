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
        // 2. If no session, use the APP_LOCALE from config
        else {
            $appLocale = config('app.locale', 'en');
            App::setLocale($appLocale);
            // Store in session for consistency
            Session::put('locale', $appLocale);
        }

        return $next($request);
    }
}