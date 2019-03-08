#!/usr/bin/env bash

php artisan migrate --force
php artisan dealer:update:travis-ip
php artisan dealer:update:parent-keys
