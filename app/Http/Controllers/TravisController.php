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

        if (in_array('places', $config['want'])) {
            $response['places'] = $this->getPlacesResponse($config, $ip);
        }

        // Add comment
        $validity = $config['key-params']['validity'] / 60;
        $response['comment'] = "The keys will expire after $validity minutes.";

        return response($response, 201);
    }

    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $options = [];
        $hosts = null;

        // This can be removed when PHP client is updated with
        // https://github.com/algolia/algoliasearch-client-php/pull/364
        if ($this->isPlacesApp($appId)) {
            $options[Client::PLACES_ENABLED] = true;
            $hosts = array(
                'places-1.algolianet.com',
                'places-2.algolianet.com',
                'places-3.algolianet.com',
            );
            shuffle($hosts);
            array_unshift($hosts, 'places.algolia.net');
        }

        $algolia = new Client($appId, $apiKey, $hosts, $options);

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

    private function getPlacesResponse($config, $ip)
    {
        $placesKey = $this->generateKey(
            $config['places']['app-id'],
            $config['places']['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return [
            'app-id' => $config['places']['app-id'],
            'api-key' => $placesKey,
        ];
    }

    private function isPlacesApp($appId)
    {
        return 'pl' == strtolower(substr($appId, 0, 2));
    }
}
