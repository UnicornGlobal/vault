<?php

namespace App\Http\Controllers;

use App\User;
use App\Entity;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Function for registering entities.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function registerEntity(Request $request)
    {
        $this->validate($request, [
            'key' => 'required',
            'secret' => 'required',
        ]);

        $fields = $request->only('key', 'secret');

        if (Entity::where('key', $fields['key'])->exists()) {
            throw new \Exception('Entity with that key already exists.');
        }

        $user = User::where('app_id', $request->header('Client'))->first();

        try {
            $entity = new Entity();
            $entity->_id = Uuid::generate(4)->string;
            $entity->key = $fields['key'];
            $entity->secret = Hash::make($fields['secret']);
            $entity->access_key = Uuid::generate(4)->string;
            $entity->encode_key = Uuid::generate(4)->string;
            $entity->decode_key = Uuid::generate(4)->string;
            $entity->user_id = $user->id;
            $entity->save();
            return response()->json($entity, '200');
        } catch (\Exception $e) {
            throw new \Exception(sprintf('There was a problem registering the entity. %s.', $e->getMessage()));
        }
    }
}
