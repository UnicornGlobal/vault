<?php

namespace App\Http\Middleware;

use App\Entity;
use App\User;
use Closure;
use Illuminate\Support\Facades\Hash;

class EntityAccessMiddleware
{
    /**
     * Ensures that the user can access the entity.
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
        $entityId = $request->input('entity_id');
        $entityKey = $request->input('key');
        $entitySecret = $request->input('secret');
        $entityAccessKey = $request->header('Entity-Access-Key');

        $entity = Entity::where('_id', $entityId)->first();

        if (empty($entity)) {
            return response('Unauthorized.', 401);
        }

        if ($entity->access_key !== $entityAccessKey) {
            return response('Unauthorized.', 401);
        }

        if ($entity->key !== $entityKey) {
            return response('Unauthorized.', 401);
        }

        if (!Hash::check($entitySecret, $entity->secret)) {
            return response('Unauthorized.', 401);
        }

        $user = User::where('app_id', $request->header('Client'))->first();

        if ($user->id !== $entity->user_id) {
            // return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
