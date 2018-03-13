<?php

namespace App\Http\Middleware;

use Closure;

class SetRequestId
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
        config([
            'request_id' => sha1($request->getClientIp().time().mt_rand(99, 9999)),
        ]);

        return $next($request);
    }
}
