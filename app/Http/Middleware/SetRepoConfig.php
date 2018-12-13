<?php

namespace App\Http\Middleware;

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
            'repository-config' => $this->getConfig($repoSlug)
        ]);

        return $next($request);
    }

    private function getConfig($repoName)
    {
        // We use array_dot to easily deep merge configuration
        $repoConfig = (array) config('repositories.'.$repoName);
        $defaultConfig = config('repositories.default');

        $mergedConfig = array_dot($repoConfig) + array_dot($defaultConfig);

        if (env('APP_DEBUG')) {
            $mergedConfig['key-params.validity'] = 180;
        }

        $config = [];
        // Then we reverse the array_dot
        foreach ($mergedConfig as $key => $value) {
            array_set($config, $key, $value);
        }

        // Corner case if the config value is an array
        foreach (['want', 'key-params.acl', 'key-params.indexes'] as $dotIndex) {
            if (! is_null($value = array_get($repoConfig, $dotIndex))) {
                array_set($config, $dotIndex, $value);
            }
        }

        return $config;
    }
}
