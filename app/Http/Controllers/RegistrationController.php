<?php

namespace App\Http\Controllers;

use App\Entity;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'access_key' => 'required',
            'access_secret' => 'required',
        ]);

        $fields = $request->only('access_key', 'access_secret');

        try {
            $entity = new Entity();
            $entity->_id = Uuid::generate(4)->string;
            $entity->access_key = $fields['access_key'];
            $entity->access_secret = $fields['access_secret'];
            $entity->encoding_key = Uuid::generate(4)->string;
            $entity->decoding_key = Uuid::generate(4)->string;
            $entity->save();
            return response()->json($entity, '200');
        } catch (\Exception $e) {
            throw new \Exception(sprintf('There was a problem registering the entity. %s.', $e->getMessage()));
        }
    }
}
