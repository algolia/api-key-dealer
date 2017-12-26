<?php

namespace App\Http\Middleware;

use App\TravisAPI;
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
        if (env('APP_DEBUG') && $request->has('repository-name')) {
            config([
                'repository-name' => $request->get('repository-name')
            ]);
        }

        config([
            'repository-config' => $this->getConfig(config('repository-name'))
        ]);

        return $next($request);
    }

    private function getConfig($repoName)
    {
        // We use array_dot to easily deep merge configuration
        $repoConfig = array_dot((array) config('repositories.'.$repoName));
        $defaultConfig = array_dot(config('repositories.default'));

        $repoConfig += $defaultConfig;

        if (env('APP_DEBUG')) {
            $repoConfig['key-params.validity'] = 180;
        }

        $config = [];
        // Then we reverse the array_dot
        foreach ($repoConfig as $key => $value) {
            array_set($config, $key, $value);
        }

        return $config;
    }
}
