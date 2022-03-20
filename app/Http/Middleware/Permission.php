<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Permission
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $authGuard = app('auth')->guard('admins');
        $authAPIGuard = app('auth')->guard('api_admins');

        if ($authGuard->guest() && $authAPIGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);
        foreach ($permissions as $permission) {
            if (!$authGuard->guest() && $authGuard->user()->hasPermission($permission)) {
                return $next($request);
            } elseif (!$authAPIGuard->guest() && $authAPIGuard->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
