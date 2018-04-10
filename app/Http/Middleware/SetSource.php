<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class SetSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->getClientIp();

        $ipAuthorized = $this->isLocal($ip)
            || $this->isFromAlgolia($ip)
            || $this->isFromTravis($ip);

        if ($ipAuthorized) {
            return $next($request);
        }

        Log::channel('slack')->debug('Incoming request from unauthorized source', [
            'Request ID' => config('request_id'),
            'IP address' => $ip,
            'Is it a crawler?' => "http://$ip/",
        ]);

        return response("Sorry the IP ".$request->getClientIp()." isn't in the allowed range", 400);
    }

    private function isLocal($ip)
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            config(['source' => 'local']);

            Log::channel('slack')->debug('Incoming request from local source', [
                'Request ID' => config('request_id'),
                'From' => $ip
            ]);

            return true;
        }
        return false;
    }

    private function isFromAlgolia($ip)
    {
        $algoliaIps = [
            '84.14.205.82' => 'Paris office',
            '162.217.74.98' => 'SF office',
        ];

        $isAlgolia = in_array($ip, array_keys($algoliaIps));

        if ($isAlgolia) {
            config(['source' => 'algolia']);

            Log::channel('slack')->debug('Incoming request from authorized source', [
                'Request ID' => config('request_id'),
                'From' => $algoliaIps[$ip]
            ]);
        }

        return $isAlgolia;
    }

    private function isFromTravis($ip)
    {
        if ($this->isTravisIpAddress($ip)) {
            config(['source' => 'travis']);

            Log::channel('slack')->debug('Incoming request from authorized source', [
                'Request ID' => config('request_id'),
                'From' => $ip
            ]);

            return true;
        }

        return false;
    }

    private function isTravisIpAddress($ip)
    {
        $travisIps = config('travis.addresses');

        if (in_array($ip, $travisIps)) {
            return true;
        }

        return false;
    }
}
