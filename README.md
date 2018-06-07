# Algolia API Key Dealer

Get an almost-admin API key to run your tests on Travis.


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
    "mcm": {
        "app-id": "5QZOBPRNH0",
        "api-key": "c6a7f4..."
    },
    "places": {
        "app-id": "plSYS0QH6R4R",
        "api-key": "93d082..."
    },
    "comment": "The keys will expire after 3 minutes."
}
```

## Debugging

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

### Pass repository name

If you want to test the configuration for a give repository, you can pass a `repository-name`
parameter. It will only be taken into account if your call the API locally
or from Algolia's office. It will bypass the call to Travis API.

```
POST /1/travis/keys/new
{
    "repository-name": "algolia/algoliasearch-client-php",
}
```
