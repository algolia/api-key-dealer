<?php

namespace App\ExternalApis;

use AlgoliaSearch\Client;

class AlgoliaClient extends Client
{
    /**
     * This method will add a new key in your app
     * but will wait until it's active on each server
     * of the cluster before returning
     *
     * @param array $keyParams
     * @return array The newly generated key (original response from API)
     * @throws \AlgoliaSearch\AlgoliaException
     */
    public function newApiKey(array $keyParams): array
    {
        $keyResponse = $this->addApiKey($keyParams);
        $res = null;

        try {
            $res = $this->getApiKey($keyResponse['key']);
        } catch (\Exception $e) {
            usleep(100);
        }

        return $keyResponse;
    }
}
