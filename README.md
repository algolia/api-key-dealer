# Algolia API Key Dealer

Running tests on travis can be a pain if you need credentials that shouldn't 
be publicly shared. This projects aims to solve that.
Read the details of why and how on Algolia's blog: 
[https://blog.algolia.com/travis-encrypted-variables-external-contributions/](https://blog.algolia.com/travis-encrypted-variables-external-contributions/)

**NOTE:** It's not exactly a framework and an app you'll be able to use right away,
but it could be a good start if you want to build something similar.

Feel free to [share your thoughts on this thread of the forum](https://discourse.algolia.com/t/dealing-with-encrypted-environment-variables-in-travis-for-algolia-credentials/5405).


## Client

Inside the client folder, you will find the go script used to call the API from your CI.
Travis (in this case) will download the latest client binary and execute it to get temporary credentials.

Example of `.travis.yml`
```yaml
before_script:
  - wget https://keys.algolia.engineering/client/algolia-keys && chmod +x algolia-keys

script:
  - $(./algolia-keys export) && php vendor/bin/phpunit
```


## Endpoints

Host is in the Github description ☝️

### Get a key

```
POST /1/travis/keys/new
{
    "TRAVIS_JOB_ID": value,
}
```

The job ID can be found in the env variable `TRAVIS_JOB_ID` (it's set automatically).

#### Response

```json
{
    "app-id": "I2UB5B7IZB",
    "api-key": "ee04e3...",
    "api-search-key": "0356747...",
    "places": {
        "app-id": "plSYS0QH6R4R",
        "api-key": "93d082..."
    },
    "comment": "The keys will expire after 3 minutes."
}
```

## Debugging

### Setup the repository locall and run tests

```
git clone https://github.com/algolia/api-key-dealer
cd api-key-dealer
composer install
cp .env.example .env

# edit to `.env` and add
# - test_ADMIN
# - NOCTT5TZUU_ADMIN
# - UCX3XB3SH4_ADMIN
# - CTS_1_ADMIN_KEY
# - CTS_2_ADMIN_KEY
    
# Run tests
composer test
```

### Call local API

If you want to test it locally, set the DEALER_HOST environment variable to the host
of your local server.

Typically, you run the server with:

```
php -S localhost:8080 -t public
```
And test the client with:
```bash
eval $(DEALER_HOST=localhost:8080./public/clients/algolia-keys-mac export)
```

### Force repository name

If you want to test the configuration for a give repository, you can pass a `repository-name`
parameter. It will only be taken into account if your call the API locally
or from Algolia's office. It will bypass the call to Travis API.

```
POST /1/algolia/keys/new
{
    "repository-name": "algolia/algoliasearch-client-php",
}
```
