<?php

namespace App\Http\Controllers;

use AlgoliaSearch\Client;
use Illuminate\Http\Request;

class TravisController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        if (! env('APP_DEBUG')) {
            $this->middleware('is_travis_ip');
            $this->middleware('is_repo_legit');
        }
    }

    public function createNewKey(Request $request)
    {
        $ip = $request->getClientIp();

        $config = $this->getConfig($request->get('repo-slug'));

        $key = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return response([
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'comment' => 'This key will expire after '. $config['key-params']['validity'] / 60 .' minutes.',
        ], 201);
    }

    public function deleteKey($key, Request $request)
    {
        $config = $this->getConfig($request->get('repo-slug'));

        $algolia = new Client($config['app-id'], $config['super-admin-key']);

        $algolia->deleteApiKey($key);

        return response('', 204);
    }

    private function getConfig($repo)
    {
        // We use array_dot to easily deep merge configuration
        $repoConfig = array_dot((array) config('repositories.'.$repo));
        $defaultConfig = array_dot(config('repositories.default'));

        $repoConfig += $defaultConfig;

        if (env('APP_DEBUG')) {
            $repoConfig['key-params.validity'] = 180;
        }

        $config = [];
        // Then we reverse the array_dot
        foreach ($repoConfig as $key => $value) {
            array_set($config, $key, $value);
        }

        return $config;
    }
    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $algolia = new Client($appId, $apiKey);
        $response = $algolia->addApiKey($keyParams);

        $key = Client::generateSecuredApiKey($response['key'], ['restrictSources' => $ip]);

        return $key;
    }
}
