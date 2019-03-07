<?php

namespace App;

use Algolia\AlgoliaSearch\SearchClient;

class AlgoliaKeys
{
    public function __construct()
    {
        //
    }

    public function forge(Config $config, $ip)
    {
        $response = [
            'app-id' => $config->getAppId(),
        ];

        $response = $this->addDedicatedKeys($response, $config, $ip);

        $response = $this->addCTSKeys($response, $config, $ip);

        $response = array_merge($response, [
            'comment' => $this->getComment($config),
            'request-id' => config('request_id'),
        ]);

        return $response;
    }


    private function addDedicatedKeys(array $response, Config $config, $ip)
    {
        $key = $this->generateKey($config->getAppId(), $config->getSuperAdminKey(), $config->getKeyParams(), $ip);

        $searchKey = $this->generateSearchKey($config->getAppId(), $config->getSuperAdminKey(), $config->getKeyParams(), $ip);

        return array_merge($response, [
            'api-key' => $key,
            'api-search-key' => $searchKey,
        ]);
    }

    private function addCTSKeys(array $response, Config $config, $ip): array
    {
        $key1 = $this->generateKey(
            $config->getCtsAppId(1),
            $config->getCtsSuperAdminKey(1),
            $config->getKeyParams(),
            $ip
        );

        $searchKey1 = $this->generateSearchKey(
            $config->getCtsAppId(1),
            $config->getCtsSuperAdminKey(1),
            $config->getKeyParams(),
            $ip
        );

        $key2 = $this->generateKey(
            $config->getCtsAppId(2),
            $config->getCtsSuperAdminKey(2),
            $config->getKeyParams(),
            $ip
        );

        return array_merge($response, [
            'ALGOLIA_APPLICATION_ID_1' => $config->getCtsAppId(1),
            'ALGOLIA_ADMIN_KEY_1' => $key1,
            'ALGOLIA_SEARCH_KEY_1' => $searchKey1,
            'ALGOLIA_APPLICATION_ID_2' => $config->getCtsAppId(2),
            'ALGOLIA_ADMIN_KEY_2' => $key2,
        ]);
    }

    private function generateSearchKey($appId, $apiKey, $keyParams, $ip = null)
    {
        // TODO: HERE
        $keyParams['acl'] = ['search'];

        return $this->generateKey($appId, $apiKey, $keyParams, $ip);
    }

    private function generateKey($appId, $apiKey, $keyParams, $ip = null)
    {
        // TODO: HERE
        $algolia = SearchClient::create($appId, $apiKey);

        $acl = $keyParams['acl'];
        unset($keyParams['acl']);

        $key = $algolia->addApiKey($acl, $keyParams)->wait();

        return $key['key'];
    }

    private function getComment(Config $config)
    {
        $validity = $config->getKeyParams()['validity'] / 60;

        return "The keys will expire after $validity minutes.";
    }
}
