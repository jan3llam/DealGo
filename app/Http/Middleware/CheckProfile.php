<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckProfile
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
        $user = User::find(auth('api')->user()->id);

        if ($user->email == null || $user->gsm == null || $user->dob == null || $user->gender == null) {
            return response()->error('profileNotCompleted');
        }

        if ($user->verified == null) {
            return response()->error('accountNotVerified');
        }

        return $next($request);
    }
}
