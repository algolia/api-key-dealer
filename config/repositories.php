<?php

return [
    'default' =>[
        'app-id' => 'test', // Yes it's a valid app ID
        'super-admin-key' => env('DEFAULT_ADMIN_KEY'),
        'mcm' => [
            'app-id' => '5QZOBPRNH0',
            'super-admin-key' => env('MCM_ADMIN_KEY'),
        ],
        'key-params' => [
            'acl' => [
                'search', 'browse',
                'addObject', 'deleteObject',
                'listIndexes', 'deleteIndex',
                'settings', 'editSettings',
                'analytics',
                'logs',
            ],
            'validity' => 5400,
            'maxQueriesPerIPPerHour' => 1000,
            'maxHitsPerQuery' => 50,
            'indexes' => ['TRAVIS_*'],
        ],
    ],
    'algolia/search-bundle' => [
        'app-id' => 'I2UB5B7IZB',
        'super-admin-key' => env('PHP_ADMIN_KEY'),
        'key-params' => [
            'acl' => [
                'search',
                'addObject', 'deleteObject',
                'listIndexes', 'deleteIndex',
                'settings', 'editSettings',
            ],
            'validity' => 3600,
            'indexes' => ['TRAVIS_sf_*'],
        ],
    ],
    'algolia/algoliasearch-client-php' => [
        'app-id' => 'I2UB5B7IZB',
        'super-admin-key' => env('PHP_ADMIN_KEY'),
        'key-params' => [
            'validity' => 3600,
            'indexes' => ['TRAVIS_php_*'],
        ],
    ],
    'algolia/algoliasearch-rails' => [
        'app-id' => 'KTG3Y5H8FB',
        'super-admin-key' => env('RAILS_ADMIN_KEY'),
        'key-params' => [
            'validity' => 3600,
            'indexes' => ['TRAVIS_RAILS_*'],
            'maxQueriesPerIPPerHour' => 10000,
            'maxHitsPerQuery' => 300,
        ],
    ],
];
