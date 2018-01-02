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
                'search',
                'addObject',
                'listIndexes',
                'settings',
                'deleteObject',
                'deleteIndex',
                'editSettings'
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
            'validity' => 3600,
            'indexes' => ['TRAVIS_sf_*'],
        ],
    ],
];
