<?php

return array(
    'dsn' => env('SENTRY_DNS_URL'),
    // capture release as git sha
    'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),
);
