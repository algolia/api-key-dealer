<?php

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

if (env('APP_DEBUG')) {
    $router->get('/', function (\Illuminate\Http\Request $request) {
        return ['message' => 'It works!'];
    });
}

$router->post('/1/travis/keys/new', 'TravisController@createNewKey');
