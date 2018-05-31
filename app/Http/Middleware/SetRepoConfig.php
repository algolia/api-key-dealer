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
        $source = config('source');

        if (in_array($source, ['local', 'whitelist'])) {
            config([
                'repository-name' => $request->get('repository-name')
            ]);
        } elseif ('travis' == $source) {
            $authorized = $this->handleTravis($request);
            if (true !== $authorized) {
                return $authorized;
            }
        }

        config([
            'repository-config' => $this->getConfig(config('repository-name'))
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

    private function handleTravis($request)
    {
        $jobId = $request->get('TRAVIS_JOB_ID');

        if (! $jobId) {
            return abort(400, 'TRAVIS_JOB_ID is missing');
        }

        if (! $this->isTravisJobLegit($jobId)) {
            return abort(400, "The TRAVIS_JOB_ID $jobId isn't currently running");
        }

        return true;
    }

    private function isTravisJobLegit($jobId)
    {
        $travis = new TravisAPI(env('TRAVIS_API_TOKEN'));
        $job = $travis->getJob($jobId);

        $statusOk = 'started' === $job['job']['state'];

        $orga = explode('/', $job['job']['repository_slug'])[0];
        $repoOk = in_array($orga, ['algolia', 'julienbourdeau']);

        config(['repository-name' => $job['job']['repository_slug']]);

        return $statusOk && $repoOk;
    }
}
