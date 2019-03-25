<?php

namespace App;

use Illuminate\Support\Arr;

class Config
{
    private $default;
    private $original;
    private $config;
    private $cts;

    public function __construct(array $config)
    {
        $this->default = [
            'app-id' => 'test', // Yes, it's a valid app ID
            'super-admin-key' => env('DEFAULT_ADMIN_KEY'),
            'places' => false,
            'key-params' => [
                'acl' => [
                    'search', 'browse',
                    'addObject', 'deleteObject',
                    'listIndexes', 'deleteIndex',
                    'settings', 'editSettings',
                    'analytics',
                    'logs',
                ],
                'validity' => 3600,
                'maxQueriesPerIPPerHour' => 2500,
                'maxHitsPerQuery' => 501,
                'indexes' => ['TRAVIS_*'],
            ],
        ];

        // Introduced for the Common Test Suite
        $this->cts = [[
            'app-id' => 'NOCTT5TZUU',
            'super-admin-key' => env('CTS_1_ADMIN_KEY'),
        ], [
            'app-id' => 'UCX3XB3SH4',
            'super-admin-key' => env('CTS_2_ADMIN_KEY'),
        ]];

        $this->original = $config;

        $this->config =  $this->mergeConfig($config, $this->default);
    }

    public function getAppId()
    {
        return $this->config['app-id'];
    }

    public function getSuperAdminKey()
    {
        return $this->config['super-admin-key'];
    }

    public function getKeyParams()
    {
        return $this->config['key-params'];
    }

    public function getCtsAppId($appIndex)
    {
        return $this->cts[$appIndex - 1]['app-id'];
    }

    public function getCtsSuperAdminKey($appIndex)
    {
        return $this->cts[$appIndex - 1]['super-admin-key'];
    }

    public function getExtra()
    {
        return $this->config['extra'] ?? [];
    }

    private function mergeConfig($original, $default)
    {

        $mergedConfig = Arr::dot($original) + Arr::dot($default);

        if (env('APP_DEBUG')) {
            $mergedConfig['key-params.validity'] = 180;
        }

        $config = [];
        // Then we reverse the array_dot
        foreach ($mergedConfig as $key => $value) {
            Arr::set($config, $key, $value);
        }

        // Corner case if the config value is an array
        foreach (['key-params.acl', 'key-params.indexes'] as $dotIndex) {
            if (null !== ($value = Arr::get($original, $dotIndex))) {
                Arr::set($config, $dotIndex, $value);
            }
        }

        return $config;
    }
}
