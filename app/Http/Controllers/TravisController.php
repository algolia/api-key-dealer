<?php

namespace App\Http\Controllers;

use App\Algolia\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TravisController extends Controller
{
    public function createCredentials(Request $request)
    {
        $response = [];
        $config = config('repository-config');
        $ip = $request->getClientIp();

        if (in_array('std', $config['want'])) {
            $response = array_merge($response, $this->getStdResponse($config, $ip));
        }

        if (in_array('places', $config['want'])) {
            $response['places'] = $this->getPlacesResponse($config, $ip);
        }

        $validity = $config['key-params']['validity'] / 60;
        $response['comment'] = "The keys will expire after $validity minutes.";

        $response['request-id'] = config('request_id');

        $this->log($response);

        return response($response, 201);
    }

    private function generateKey($appId, $apiKey, $ip, $keyParams)
    {
        $algolia = new Client($appId, $apiKey);

        $response = $algolia->newApiKey($keyParams);

//        if ('travis' == config('source')) {
//            return Client::generateSecuredApiKey($response['key'], ['restrictSources' => $ip]);
//        } else {
            return $response['key'];
//        }
    }

    private function getStdResponse($config, $ip)
    {
        $key = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $config['key-params']
        );

        $searchParams = ['acl' => ['search']] + $config['key-params'];
        $searchKey = $this->generateKey(
            $config['app-id'],
            $config['super-admin-key'],
            $ip,
            $searchParams
        );

        return [
            'app-id' => $config['app-id'],
            'api-key' => $key,
            'api-search-key' => $searchKey,
        ];
    }

    private function getPlacesResponse($config, $ip)
    {
        $placesKey = $this->generateKey(
            $config['places']['app-id'],
            $config['places']['super-admin-key'],
            $ip,
            $config['key-params']
        );

        return [
            'app-id' => $config['places']['app-id'],
            'api-key' => $placesKey,
        ];
    }

    private function log($context)
    {
        $context['api-key'] = substr($context['api-key'], 0, 8).'...';

        Log::channel('slack')->notice(
            'Generated access for '.config('repository-name'),
            $context
        );
    }
}
