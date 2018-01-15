<?php

namespace App\Algolia;

use AlgoliaSearch\Client as AlgoliaClient;

class Client extends AlgoliaClient
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

        do {
            try {
                $res = $this->getApiKey($keyResponse['key']);
            } catch (\Exception $e) {
                // Not ready yet
            }
        } while (! is_array($res));


        return $keyResponse;
    }
}
