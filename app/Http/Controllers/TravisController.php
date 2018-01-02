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

        $this->middleware('set_repo_config');
    }

    public function createNewKey(Request $request)
    {
        $config = config('repository-config');
        $ip = $request->getClientIp();

        $key = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $config['key-params']
        );

        $searchParams = ['acl' => ['search']] + $config['key-params'];
        $searchKey = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $searchParams
        );

        $mcmKey = $this->generateKey(
            $config['mcm']['app-id'],
            $config['mcm']['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return response([
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'api-search-key' => $searchKey,
            'mcm' => [
                'app-id' => $config['mcm']['app-id'],
                'api-key' => $mcmKey,
            ],
            'comment' => 'The keys will expire after '. $config['key-params']['validity'] / 60 .' minutes.',
        ], 201);
    }

    public function deleteKey($key, Request $request)
    {
        $config = config('repository-config');

        $algolia = new Client($config['app-id'], $config['super-admin-key']);

        $algolia->deleteApiKey($key);

        return response('', 204);
    }

    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $algolia = new Client($appId, $apiKey);
        $response = $algolia->addApiKey($keyParams);

        $key = Client::generateSecuredApiKey($response['key'], ['restrictSources' => $ip]);

        return $key;
    }
}
