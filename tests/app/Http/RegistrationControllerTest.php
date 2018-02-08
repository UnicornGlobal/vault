<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RegistrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testRegisterEntity()
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

        $result = json_decode($this->response->getContent());

        $this->assertEquals('object', gettype($result));

        $this->assertEquals(5, count((array)$result));

        $this->assertRegExp(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $result->_id
        );

        $this->assertRegExp(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $result->access_key
        );

        $this->assertRegExp(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $result->encode_key
        );

        $this->assertRegExp(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $result->decode_key
        );

        $this->assertEquals('test-key', $result->key);
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
