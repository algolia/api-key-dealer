<?php

namespace App\Http\Controllers;

use App\Algolia\Client;
use Illuminate\Http\Request;

class TravisController extends Controller
{
    public function createCredentials(Request $request)
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

        $response = [
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'api-search-key' => $searchKey,
        ];

        if (in_array('mcm', $config['want'])) {
            $response['mcm'] = $this->getMcmResponse($config, $ip);
        }

        // Add comment
        $validity = $config['key-params']['validity'] / 60;
        $response['comment'] = "The keys will expire after $validity minutes.";

        return response($response, 201);
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

    private function getMcmResponse($config, $ip)
    {
        $mcmKey = $this->generateKey(
            $config['mcm']['app-id'],
            $config['mcm']['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return [
            'app-id' => $config['mcm']['app-id'],
            'api-key' => $mcmKey,
        ];
    }
}
