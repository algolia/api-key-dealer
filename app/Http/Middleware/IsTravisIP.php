<?php

namespace App\Http\Middleware;

use Closure;

class IsTravisIP
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
        error_log("IP: ".$request->getClientIp()."\n");

        if (! $this->isFromTravis($request->getClientIp())) {
            return abort(400, "Sorry the IP ".$request->getClientIp()." isn't in the allowed range" );
        }
        
        return $next($request);
    }

    private function isFromTravis($ip)
    {
        $travisIps = [
            // Sudo-enabled Linux
            '8.34.208.0/20', '8.35.192.0/21', '8.35.200.0/23',
            '23.236.48.0/20', '23.251.128.0/19', '35.184.0.0/14',
            '35.188.0.0/15', '35.190.0.0/17', '35.190.128.0/18',
            '35.190.192.0/19', '35.190.224.0/20', '35.192.0.0/14',
            '35.196.0.0/15', '35.198.0.0/16', '35.199.0.0/17',
            '35.199.128.0/18', '35.200.0.0/15', '35.202.0.0/16',
            '35.203.0.0/17', '35.203.128.0/18', '35.203.240.0/20',
            '35.204.0.0/15', '35.206.64.0/18', '35.224.0.0/14',
            '35.228.0.0/16', '104.154.0.0/15', '104.196.0.0/14',
            '107.167.160.0/19', '107.178.192.0/18', '108.59.80.0/20',
            '108.170.192.0/20', '108.170.208.0/21', '108.170.216.0/22',
            '108.170.220.0/23', '108.170.222.0/24', '130.211.4.0/22',
            '130.211.8.0/21', '130.211.16.0/20', '130.211.32.0/19',
            '130.211.64.0/18', '130.211.128.0/17', '146.148.2.0/23',
            '146.148.4.0/22', '146.148.8.0/21', '146.148.16.0/20',
            '146.148.32.0/19', '146.148.64.0/18', '162.216.148.0/22',
            '162.222.176.0/21', '173.255.112.0/20', '192.158.28.0/22',
            '199.192.112.0/22', '199.223.232.0/22', '199.223.236.0/23',
            '208.68.108.0/23'
        ];

        foreach ($travisIps as $range) {
            if ($this->inRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    private function inRange($ip, $range) {
        if ( strpos( $range, '/' ) == false ) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list( $range, $netmask ) = explode( '/', $range, 2 );
        $range_decimal = ip2long( $range );
        $ip_decimal = ip2long( $ip );
        $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
    }
}
