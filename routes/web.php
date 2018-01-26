<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * Includes a set of security-focused middeware
 *
 * You can see more info on each of them in the Http/Middleware folder
 *
 * Feel free to add/edit/remove Middlewares
 */
$router->group(
    [
        'prefix' => '',
        'middleware' => ['nocache', 'hideserver', 'security', 'csp', 'cors']
    ],
    function () use ($router) {

    /**
     * Routes that do not require a JWT
     *
     * Different routes have different combinations based on use case.
     */

        /**
         *  Ensures that retrieving config is allowed with the correct app id
         *
         *  Ensure APP_ID in your .env
         *  Request with `App: your-key-here`
         */
        $router->group(['middleware' => ['throttle:10,1', 'appid']], function () use ($router) {
            $router->get('/config/app', 'ConfigController@getAppConfig');
        });

        /**
         * 10 Login and Logouts per minute
         */
        $router->group(['middleware' => 'throttle:10,1'], function () use ($router) {
            $router->post('/login', 'AuthController@postLogin');
            $router->post('/logout', 'AuthController@logout');
        });

        /**
         * What you set this throttle to depends on your use case.
         * JWT refresh
         */
        $router->group(['middleware' => ['jwt.refresh', 'throttle:10,1']], function () use ($router) {
            $router->post('/refresh', 'AuthController@refresh');
        });

        /**
         * Authenticated Routes
         */
        $router->group(['prefix' => 'api', 'middleware' => ['auth:api', 'throttle']], function () use ($router) {
            $router->get('/', function () use ($router) {
                return $router->app->version();
            });

            $router->post('/docs/upload', 'DocumentController@saveDoc');

            $router->get('/docs/retrieve/{docId}', 'DocumentController@retrieveDoc');

            $router->post('/docs/list/{userId}', 'DocumentController@listDocs');
        });
    }
);
