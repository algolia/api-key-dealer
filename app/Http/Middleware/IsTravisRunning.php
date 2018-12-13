<?php

namespace App\Http\Middleware;

use App\ExternalApis\TravisAPI;

class IsTravisRunning
{
    public function handle($request, \Closure $next)
    {
        $source = config('source');

        if ('travis' === $source) {
            $authorized = $this->handleTravis($request);
            if (true !== $authorized) {
                return $authorized;
            }
        }

        return $next($request);
    }

    private function handleTravis($request)
    {
        $jobId = $request->get('TRAVIS_JOB_ID');

        if (! $jobId) {
            return abort(400, 'TRAVIS_JOB_ID is missing');
        }

        if (! $this->isTravisJobLegit($jobId, $request->get('REPO_SLUG'))) {
            return abort(400, "The TRAVIS_JOB_ID $jobId isn't currently running or repo_slug didn't match");
        }

        return true;
    }

    private function isTravisJobLegit($jobId, $requestRepoSlug)
    {
        $travis = new TravisAPI(env('TRAVIS_API_TOKEN'));
        $job = $travis->getJob($jobId);

        if ($requestRepoSlug !== $job['job']['repository_slug']) {
            return false;
        }

        $statusOk = 'started' === $job['job']['state'];

        $orga = explode('/', $job['job']['repository_slug'])[0];
        $repoOk = \in_array($orga, ['algolia', 'julienbourdeau']);

        return $statusOk && $repoOk;
    }
}
