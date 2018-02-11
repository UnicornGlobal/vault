<?php

namespace App\Http\Middleware;

use App\Entity;
use Closure;

class DecodeAccessMiddleware
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
        $entityAccessKey = $request->header('Entity-Access-Key');
        $encodeAccessKey = $request->input('action');

        $entity = Entity::where('access_key', $entityAccessKey)->first();

        if (empty($entity)) {
            return response('Unauthorized.', 401);
        }

        if ($entity->decode_key !== $encodeAccessKey) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
