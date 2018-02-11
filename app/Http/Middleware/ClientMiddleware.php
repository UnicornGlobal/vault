<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class ClientMiddleware
{
    /**
     * Ensures that the client/user has the correct ID and KEY.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::where('app_id', $request->header('Client'))
            ->where('app_key', $request->header('Authorization'))
            ->where('ip_address', $request->ip())
            ->first();
        // Ensure that the requesting app is legit
        if (!is_null($user)) {
            return $next($request);
        }

        return response('Unauthorized.', 401);
    }
}
