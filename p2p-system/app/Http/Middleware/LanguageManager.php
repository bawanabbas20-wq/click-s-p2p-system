<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LanguageManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority: Session locale > Site default locale > Config default
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            // Get default locale from site settings
            try {
                $locale = \App\Models\Setting::get('default_locale', config('app.locale'));
            } catch (\Exception $e) {
                $locale = config('app.locale', 'en');
            }
        }
        
        // Validate and set locale
        if (in_array($locale, ['en', 'ar', 'ku'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
