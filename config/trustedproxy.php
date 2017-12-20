<?php

return [
    'proxies' => '*',
    'headers' => [
        Illuminate\Http\Request::HEADER_FORWARDED    => null, // not set on AWS or Heroku
        Illuminate\Http\Request::HEADER_CLIENT_IP    => 'X_FORWARDED_FOR',
        Illuminate\Http\Request::HEADER_CLIENT_HOST  => null, // not set on AWS or Heroku
        Illuminate\Http\Request::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO',
        Illuminate\Http\Request::HEADER_CLIENT_PORT  => 'X_FORWARDED_PORT',
    ]
];
