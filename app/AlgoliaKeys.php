<?php

namespace App;

use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Http\Request;

class AlgoliaKeys
{
    public function __construct()
    {
        //
    }

    public function forge(Config $config, $withCtsCredentials = false)
    {
        $response = [
            'app-id' => $config->getAppId(),
        ];

        $response = $this->addDedicatedKeys($response, $config);

        if ($withCtsCredentials) {
            $response = $this->addCTSKeys($response, $config);
        }

        return $response;
    }


    private function addDedicatedKeys(array $response, Config $config)
    {
        $key = Key::findOrCreate($config->getAppId());

        $writeKey = $this->generateKey($key->write, $config->getKeyParams());

        $searchKey = $this->generateSearchKey($key->search, $config->getKeyParams());

        return array_merge($response, [
            'api-key' => $writeKey,
            'api-search-key' => $searchKey,
        ]);
    }

    private function addCTSKeys(array $response, Config $config): array
    {
        $key = Key::findOrCreate($config->getCtsAppId(1));

        $writeKey1 = $this->generateKey(
            $key->write,
            $config->getKeyParams()
        );

        $searchKey1 = $this->generateSearchKey(
            $key->write,
            $config->getKeyParams()
        );

        $key2 = Key::findOrCreate($config->getCtsAppId(2));

        $writeKey2 = $this->generateKey(
            $key2->write,
            $config->getKeyParams()
        );

        return array_merge($response, [
            'ALGOLIA_APPLICATION_ID_1' => $config->getCtsAppId(1),
            'ALGOLIA_ADMIN_KEY_1' => $writeKey1,
            'ALGOLIA_SEARCH_KEY_1' => $searchKey1,
            'ALGOLIA_APPLICATION_ID_2' => $config->getCtsAppId(2),
            'ALGOLIA_ADMIN_KEY_2' => $writeKey2,
        ]);
    }

    private function generateSearchKey($parentApiKey, $keyParams)
    {
        $keyParams['acl'] = ['search'];

        return $this->generateKey($parentApiKey, $keyParams);
    }

    private function generateKey($parentApiKey, $keyParams)
    {
        $ip = app(Request::class)->getClientIp();

        return SearchClient::generateSecuredApiKey($parentApiKey, [
            'validUntil' => time() + $keyParams['validity'],
            'restrictIndices' => implode(',', $keyParams['indexes']),
            'restrictSources' => $ip,
        ]);
    }
}
