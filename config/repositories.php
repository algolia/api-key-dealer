<?php

return [
    'default' =>[
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
        'places' => [
            'app-id' => 'plSYS0QH6R4R',
            'super-admin-key' => env('PLACES_ADMIN_KEY'),
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
    'algolia/algoliasearch-client-ruby' => [
        'app-id' => 'N415NQ98FV',
        'super-admin-key' => env('RUBY_ADMIN_KEY'),
        'key-params' => [
            'maxQueriesPerIPPerHour' => 50000,
            'maxHitsPerQuery' => 3000,
            'indexes' => ['TRAVIS_RUBY_*'],
        ],
    ],
    'algolia/algoliasearch-magento' => [
        'app-id' => 'I2UB5B7IZB',
        'super-admin-key' => env('PHP_ADMIN_KEY'),
        'key-params' => [
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_M1_*'],
        ],
    ],
    'algolia/algoliasearch-magento-2' => [
        'app-id' => 'I2UB5B7IZB',
        'super-admin-key' => env('PHP_ADMIN_KEY'),
        'key-params' => [
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_M2_*'],
        ],
    ],
    "algolia/algoliasearch-client-javascript" => [
        'key-params' => [
            'indexes' => ['TRAVIS_JS_*'],
        ],
    ],
    "algolia/algoliasearch-client-python" => [
        'app-id' => 'RDOT4PBY36',
        'super-admin-key' => env('PYTHON_ADMIN_KEY'),
        'key-params' => [
            'indexes' => ['TRAVIS_PYTHON_*'],
            'maxQueriesPerIPPerHour' => 10000,
        ],
    ],
    "algolia/algoliasearch-client-python-async" => [
        'app-id' => 'RDOT4PBY36',
        'super-admin-key' => env('PYTHON_ADMIN_KEY'),
        'key-params' => [
            'indexes' => ['TRAVIS_PYTHONasync_*'],
        ],
    ],
    "algolia/algoliasearch-client-java-2" => [
        'app-id' => 'GLKI3BO0NS',
        'super-admin-key' => env('JAVA2_ADMIN_KEY'),
        'key-params' => [
            'indexes' => [],
            'maxQueriesPerIPPerHour' => 10000,
        ],
    ],
];
