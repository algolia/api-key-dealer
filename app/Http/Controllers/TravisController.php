<?php

namespace App\Http\Controllers;

use App\Algolia\Client;
use Illuminate\Http\Request;

class TravisController extends Controller
{
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

    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $algolia = new Client($appId, $apiKey);

        $response = $algolia->newApiKey($keyParams);

        if ('travis' == config('source')) {
            return Client::generateSecuredApiKey($response['key'], ['restrictSources' => $ip]);
        } else {
            return $response['key'];
        }
    }
}
