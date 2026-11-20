<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * The language switcher stores the choice in the session; without this every page
     * but the landing page rendered in the app default locale.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', 'de');

        if (in_array($locale, site_locales(), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
