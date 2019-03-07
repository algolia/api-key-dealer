<?php

namespace App\Http\Middleware;

use App\Config;
use App\ExternalApis\TravisAPI;
use Closure;

class SetRepoConfig
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
        $repoSlug = $request->get('REPO_SLUG');

        config([
            'repository-name' => $repoSlug
        ]);

        config([
            'repository-config' => new Config((array) config('repositories.'.$repoSlug)),
        ]);

        return $next($request);
    }
}
