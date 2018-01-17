<?php

return [
    'default' =>[
        'want' => ['std'], // 'std', 'mcm', 'places'
        'app-id' => 'test', // Yes it's a valid app ID
        'super-admin-key' => env('DEFAULT_ADMIN_KEY'),
        'mcm' => [
            'app-id' => '5QZOBPRNH0',
            'super-admin-key' => env('MCM_ADMIN_KEY'),
        ],
        'places' => [
            'app-id' => 'plSYS0QH6R4R',
            'super-admin-key' => env('PLACES_ADMIN_KEY'),
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
            'validity' => 3600,
            'maxQueriesPerIPPerHour' => 2500,
            'maxHitsPerQuery' => 101,
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
            'indexes' => ['TRAVIS_sf_*'],
        ],
    ],
    'algolia/algoliasearch-client-php' => [
        'app-id' => 'I2UB5B7IZB',
        'super-admin-key' => env('PHP_ADMIN_KEY'),
        'key-params' => [
            'indexes' => ['TRAVIS_php_*'],
        ],
    ],
    'algolia/algoliasearch-rails' => [
        'app-id' => 'KTG3Y5H8FB',
        'super-admin-key' => env('RAILS_ADMIN_KEY'),
        'key-params' => [
            'maxQueriesPerIPPerHour' => 10000,
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_RAILS_*'],
        ],
    ],
    'algolia/algoliasearch-magento' => [
        'app-id' => 'testingITL38KLXHC',
        'super-admin-key' => env('MAGENTO_ADMIN_KEY'),
        'key-params' => [
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_M1_*'],
        ],
    ],
    'algolia/algoliasearch-magento-2' => [
        'app-id' => 'testingITL38KLXHC',
        'super-admin-key' => env('MAGENTO_ADMIN_KEY'),
        'key-params' => [
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_M2_*'],
        ],
    ],
    "algolia/algoliasearch-client-javascript" => [
        'key-params' => [
            'indexes' => ['TRAVIS_JS_*'],
        ],
    ]

];
