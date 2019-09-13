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
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->getClientIp();

        $ipAuthorized = $this->isLocal($ip)
            || $this->isFromAuthorizedIp($ip)
            || $this->isFromTravis($ip)
            || $this->isFromCircleCi($request);

        if ($ipAuthorized) {
            return $next($request);
        }

        return response("Sorry the IP " . $request->getClientIp() . " isn't in the allowed range", 400);
    }

    private function isLocal($ip)
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            config(['source' => 'local']);

            return true;
        }
        return false;
    }

    /**
     * You can set a list of address IP that will be authorized. This is helpful to
     * call the API from work or from home.
     *
     * In`config/app.php` add an entry to `under authorized_ip_addresses`.
     */
    private function isFromAuthorizedIp($ip)
    {
        $authorizedIps = config('custom.whitelist');

        $isAuthorized = in_array($ip, array_keys($authorizedIps));

        if ($isAuthorized) {
            config(['source' => 'whitelist']);

        }

        return $isAuthorized;
    }

    private function isFromTravis($ip)
    {
        if ($this->isTravisIpAddress($ip)) {
            config(['source' => 'travis']);

            return true;
        }

        return false;
    }

    private function isTravisIpAddress($ip)
    {
        $travisIps = config('travis.addresses');

        return in_array($ip, $travisIps);
    }

    private function isFromCircleCi($request)
    {
        if ($request->has('CIRCLE_BUILD_NUM')) {
            config(['source' => 'circleci']);

            Log::channel('slack')->debug('Incoming request from authorized source', [
                'Request ID' => config('request_id'),
                'From' => 'CIRCLE CI #' . $request->get('CIRCLE_BUILD_NUM') . ': ' . $request->get('CIRCLE_USERNAME') . '/' . $request->get('CIRCLE_REPONAME'),
            ]);

            return true;
        }

        return false;
    }
}
