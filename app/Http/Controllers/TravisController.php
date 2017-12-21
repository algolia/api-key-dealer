<?php

namespace App\Http\Controllers;

use AlgoliaSearch\Client;
use Illuminate\Http\Request;

class TravisController extends Controller
{
    protected $algolia;

    /**
     * Create a new controller instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        if (! env('APP_DEBUG')) {
            $this->middleware('is_travis_ip');
            $this->middleware('is_repo_legit');
        }

        $this->algolia = $client;
    }

    public function createNewKey(Request $request)
    {
        $ip = $request->getClientIp();
        $validity = env('APP_DEBUG') ? 60 : 5400;

        $response = $this->algolia->addApiKey([
            'acl' => [
                'search',
                'addObject',
                'listIndexes',
                'settings',
                'deleteObject',
                'deleteIndex',
                'editSettings'
            ],
            'validity' => $validity,
            'maxQueriesPerIPPerHour' => 1000,
            'maxHitsPerQuery' => 50,
            'indexes' => ['TRAVIS_*'],
        ]);

        $key = Client::generateSecuredApiKey($response['key'], ['restrictSources' => $ip]);

        return response(['admin_key' => $key], 201);
    }

    public function deleteKey($key)
    {
        $this->algolia->deleteApiKey($key);

        return response('', 204);
    }
}
