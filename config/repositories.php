<?php

return [
    'algolia/search-bundle' => [
        'app-id' => 'I2UB5B7IZB',
        'key-params' => [
            'acl' => [
                'search',
                'addObject', 'deleteObject',
                'listIndexes', 'deleteIndex',
                'settings', 'editSettings',
            ],
            'indexes' => ['TRAVIS_sf_*', 'atomic_temporary_*'],
        ],
    ],
    'algolia/algoliasearch-client-php' => [
        'app-id' => 'I2UB5B7IZB',
        'key-params' => [
            'indexes' => ['TRAVIS_php_*'],
        ],
    ],
    'algolia/algoliasearch-client-php-helper' => [
        'app-id' => 'I2UB5B7IZB',
        'key-params' => [
            'indexes' => ['TRAVIS_php-helper_*'],
        ],
    ],
    'algolia/algoliasearch-rails' => [
        'app-id' => 'KTG3Y5H8FB',
        'key-params' => [
            'maxQueriesPerIPPerHour' => 10000,
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_RAILS_*', 'rails_*'],
        ],
    ],
    'algolia/algoliasearch-client-ruby' => [
        'app-id' => 'N415NQ98FV',
        'key-params' => [
            'maxQueriesPerIPPerHour' => 50000,
            'maxHitsPerQuery' => 3000,
            'indexes' => ['TRAVIS_RUBY_*'],
        ],
    ],
    'algolia/algoliasearch-magento' => [
        'app-id' => 'I2UB5B7IZB',
        'key-params' => [
            'maxHitsPerQuery' => 300,
            'indexes' => ['TRAVIS_M1_*'],
        ],
    ],
    'algolia/algoliasearch-magento-2' => [
        'app-id' => 'I2UB5B7IZB',
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
        'key-params' => [
            'indexes' => ['TRAVIS_PYTHON_*'],
            'maxQueriesPerIPPerHour' => 10000,
        ],
    ],
    "algolia/algoliasearch-client-python-async" => [
        'app-id' => 'RDOT4PBY36',
        'key-params' => [
            'indexes' => ['TRAVIS_PYTHONasync_*'],
        ],
    ],
    "algolia/algoliasearch-client-java-2" => [
        'app-id' => 'GLKI3BO0NS',
        'key-params' => [
            'indexes' => [],
            'maxQueriesPerIPPerHour' => 10000,
        ],
    ],
];
