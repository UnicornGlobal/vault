<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testUploadDocument()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        // TODO see question branch about empty test file uploads
    }

    public function testRegisterExistingEntity()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $this->assertEquals('200', $this->response->status());

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $result = json_decode($this->response->getContent());

        $this->assertEquals('500', $this->response->status());
        $this->assertEquals('Entity with that key already exists.', $result->error);
    }

    public function testRegisterMissingKey()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $result = json_decode($this->response->getContent());

        $this->assertEquals('500', $this->response->status());
        $this->assertEquals('The given data was invalid.', $result->error);
    }

    public function testRegisterEmptyKey()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => '',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $result = json_decode($this->response->getContent());

        $this->assertEquals('500', $this->response->status());
        $this->assertEquals('The given data was invalid.', $result->error);
    }

    public function testRegisterMissingSecret()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'dssdf',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $result = json_decode($this->response->getContent());

        $this->assertEquals('500', $this->response->status());
        $this->assertEquals('The given data was invalid.', $result->error);
    }

    public function testRegisterEmptySecret()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'dfgsdfgs',
            'secret' => '',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $result = json_decode($this->response->getContent());

        $this->assertEquals('500', $this->response->status());
        $this->assertEquals('The given data was invalid.', $result->error);
    }

    public function testRegisterWithoutAppKey()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);

        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }

    public function testRegisterWithoutRegisterKey()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => $user->app_key,
        ]);
        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }

    public function testRegisterWithoutClientKey()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);
        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }

    public function testRegisterWithoutClientSecret()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);
        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }

    public function testRegisterWithBadClientKey()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => 'dfsdsfdfsf',
            'Authorization' => $user->app_key,
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);
        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }

    public function testRegisterWithBadClientSecret()
    {
        $user = factory('App\User')->make();

        $this->post('/api/register', [
            'key' => 'test-key',
            'secret' => 'supersecret',
        ],
        [
            'App' => env('APP_ID'),
            'Client' => $user->app_id,
            'Authorization' => 'adfssdfasfd',
            'Registration-Access-Key' => env('REGISTRATION_ACCESS_KEY'),
        ]);
        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }
}
