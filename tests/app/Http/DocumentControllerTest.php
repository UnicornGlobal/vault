<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class DocumentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testUploadDocument()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key'    => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App'                     => env('APP_ID'),
            'Client'                  => $user->app_id,
            'Authorization'           => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        // TODO see question branch about empty test file uploads
    }
}
