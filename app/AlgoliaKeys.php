<?php

namespace App;

use Algolia\AlgoliaSearch\SearchClient;

class AlgoliaKeys
{
    public function __construct()
    {
        //
    }

    public function forge($config, $ip)
    {
        $response = $this->createLegacyKeys($config, $ip);

        if ($config['places']) {
            $response = array_merge($response, $this->createPlacesKey($config, $ip));
        }

        $response = array_merge($response, $this->createCTSKeys($config, $ip));

        $response = array_merge($response, [
            'comment' => $this->getComment($config),
            'request-id' => config('request_id'),
        ]);

        return $response;
    }


    private function createLegacyKeys($config, $ip)
    {
        $key = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $config['key-params']
        );

        $searchKey = $this->generateSearchKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return [
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'api-search-key' => $searchKey,
        ];
    }

    private function createCTSKeys($config, $ip): array
    {
        $appId1 = config('repositories.cts.app-id-1');
        $appId2 = config('repositories.cts.app-id-2');

        $key1 = $this->generateKey(
            $appId1,
            config('repositories.cts.super-admin-key-1'),
            $ip,
            $config['key-params']
        );

        $searchKey1 = $this->generateSearchKey(
            $appId1,
            config('repositories.cts.super-admin-key-1'),
            $ip,
            $config['key-params']
        );

        $key2 = $this->generateKey(
            $appId2,
            config('repositories.cts.super-admin-key-2'),
            $ip,
            $config['key-params']
        );

        return [
            'ALGOLIA_APPLICATION_ID_1' => $appId1,
            'ALGOLIA_ADMIN_KEY_1' => $key1,
            'ALGOLIA_SEARCH_KEY_1' => $searchKey1,
            'ALGOLIA_APPLICATION_ID_2' => $appId2,
            'ALGOLIA_ADMIN_KEY_2' => $key2,
        ];
    }

    private function createPlacesKey($config, $ip)
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

    private function generateSearchKey($appId, $apiKey, $ip, $keyParams)
    {
        $keyParams['acl'] = ['search'];

        return $this->generateKey($appId, $apiKey, $ip, $keyParams);
    }

    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $algolia = SearchClient::create($appId, $apiKey);

        $acl = $keyParams['acl'];
        unset($keyParams['acl']);

        $key = $algolia->addApiKey($acl, $keyParams)->wait();

        return $key['key'];
    }

    private function getComment($config)
    {
        $validity = $config['key-params']['validity'] / 60;

        return "The keys will expire after $validity minutes.";
    }
}
