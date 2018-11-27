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
        $response = $this->createKeys($config, $ip);

        if ($config['places']) {
            $response = array_merge($response, $this->createPlacesKey($config, $ip));
        }

        $response = array_merge($response, [
            'comment' => $this->getComment($config),
            'request-id' => config('request_id'),
        ]);

        return $response;
    }


    private function createKeys($config, $ip)
    {
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

        return [
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'api-search-key' => $searchKey,
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
