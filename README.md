# Algolia API Key Dealer

Get an almost-admin API key to run your tests on Travis.


## Endpoints

Host is in the Github description ☝️

### Get a key

```
POST /1/travis/keys/new
{
    "TRAVIS_JOB_ID": value 
}
```

`TRAVIS_JOB_ID` is a env variable automatically set by Travis.

### Delete a key

```
DELETE /1/travis/keys/{key_to_delete}
```
