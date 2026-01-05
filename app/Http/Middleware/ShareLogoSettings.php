<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareLogoSettings
{
    /**
     * Handle an incoming request and share logo settings with all Inertia pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share('logoSettings', function () {
            return [
                'site_logo' => Setting::where('key', 'site_logo')->value('value'),
                'logo_size' => Setting::where('key', 'logo_size')->value('value') ?? '100',
                'site_title' => Setting::where('key', 'site_title')->value('value') ?? 'CMS Admin',
            ];
        });

        return $next($request);
    }
}
