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

The job ID can be found in the env variable `TRAVIS_JOB_ID` (it's set automatically).
