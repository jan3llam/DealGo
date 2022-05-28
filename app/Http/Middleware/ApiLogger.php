<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        dd($request,$request->ip());
        if ($request->hasHeader('osType') && $request->hasHeader('osVersion') && $request->hasHeader('deviceModel') && $request->hasHeader('deviceManufacturer') && $request->hasHeader('Accept-Language')) {

            if (empty($request->header('osType')) || empty($request->header('Accept-Language')) || empty($request->header('osVersion')) || empty($request->header('deviceModel')) || empty($request->header('deviceManufacturer'))) {
                return response()->httpError('badRequest');
            }
            $language = $request->header('Accept-Language');
            $osType = $request->header('osType');
            $osVersion = $request->header('osVersion');
            $deviceModel = $request->header('deviceModel');
            $deviceManufacturer = $request->header('deviceManufacturer');
            $ip = $request->ip();
            $params = $request->all();
            $method = $request->method();
            $url = $request->url();
            $user = auth('api')->check() ? auth('api')->user()->userable : null;

            $log = new Log;

            $log->language = $language;
            $log->method = $method;
            $log->url = $url;
            $log->params = json_encode($params);
            $log->ip = $ip;
            $log->osType = $osType;
            $log->osVersion = $osVersion;
            $log->deviceModel = $deviceModel;
            $log->deviceManufacturer = $deviceManufacturer;
            $log->user_id = $user ? $user->id : null;

            $log->save();

            if (in_array($language, config('app.available_locales')))
                App::setLocale($language);
            else
                App::setLocale(config('app.fallback_locale'));

            return $next($request);
        }
        return response()->httpError('badRequest');
    }
}
