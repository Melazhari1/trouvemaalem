<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the locale from the first segment of the URL (e.g. /en/about)
        $locale = $request->segment(1);

        if (in_array($locale, ['en', 'fr', 'ar'])) {
            App::setLocale($locale);
            
            // This is critical: it automatically passes the 'locale' parameter
            // to all route() helper calls, so you don't have to manually specify it!
            URL::defaults(['locale' => $locale]);

            // Remove the locale parameter from the route so it's not passed to controllers
            if ($request->route()) {
                $request->route()->forgetParameter('locale');
            }
        } else {
            // Fallback for non-localized routes like /sitemap.xml
            App::setLocale(config('app.locale', 'en'));
        }

        return $next($request);
    }
}
