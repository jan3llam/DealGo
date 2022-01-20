<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AdminTranslate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasAny('lang')) {
            $language = $request->get('lang');
            if (in_array($language, config('app.available_locales')))
                App::setLocale($language);
            else
                App::setLocale(config('app.fallback_locale'));

            return $next($request);
        }
        return $next($request);
    }
}
