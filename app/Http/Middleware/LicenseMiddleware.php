<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LicenseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $licenseKey = config('app.license_key');
        $host = $_SERVER['HTTP_HOST'];
        // Remove port if present
        $domain = explode(':', $host)[0];
        $allowedDomains = explode(',', config('app.allowed_domains', ''));

        // Check domain/IP
        if (!in_array($domain, array_map('trim', $allowedDomains))) {
            abort(403, 'Unauthorized Domain');
        }

        // For demo, check if key is set; in production, call your API
        if (!$licenseKey) {
            abort(403, 'License key not configured.');
        }

        // Example API call (replace with your server)
        // $response = Http::post('https://your-license-server.com/validate', [
        //     'key' => $licenseKey,
        //     'domain' => $domain,
        // ]);

        // if (!$response->successful()) {
        //     abort(403, 'Invalid License');
        // }

        return $next($request);
    }
}
