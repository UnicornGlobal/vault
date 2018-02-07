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
        'middleware' => ['nocache', 'hideserver', 'security', 'csp', 'cors', 'client', 'appid']
    ],
    function () use ($router) {
        $router->get('/config/app', 'ConfigController@getAppConfig');

        /**
         * Authenticated Routes
         */
        $router->group(['prefix' => 'api'], function () use ($router) {
            $router->get('/', function () use ($router) {
                return $router->app->version();
            });

            $router->group(['middleware' => ['entity', 'encode']], function () use ($router) {
                $router->post('/document/upload', 'DocumentController@saveDocument');
            });

            $router->group(['middleware' => ['entity', 'decode']], function () use ($router) {
                $router->post('/document/retrieve/{documentId}', 'DocumentController@retrieveDocument');
            });

            $router->group(['middleware' => 'register'], function () use ($router) {
                $router->post('/register', 'RegistrationController@registerEntity');
            });
        });
    }
);
