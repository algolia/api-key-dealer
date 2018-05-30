<?php

namespace App\Http\Middleware;

use Closure;

class SetRequestId
{
    /**
     * The request ID is displayed by the client on travis, this allows you
     * to find the log entry in slack when debugging.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config([
            'request_id' => sha1($request->getClientIp().time().mt_rand(99, 9999)),
        ]);

        return $next($request);
    }
}
