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

$middleware = 'travis';
if (env('CIRCLE_API_TOKEN')) {
    $middleware = 'circleci';
}

$router->post('/1/algolia/keys/new', [
    'middleware' => $middleware,
    'uses' => 'AlgoliaController@getAlgoliaCredentials',
]);