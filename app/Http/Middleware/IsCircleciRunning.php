<?php

namespace App\Http\Middleware;

use App\ExternalApis\CircleciAPI;
use function PHPSTORM_META\type;

class IsCircleciRunning
{
    public function handle($request, \Closure $next)
    {
        $source = config('source');

        if ('circleci' === $source) {
            $authorized = $this->handleCircleCI($request);
            if (true !== $authorized) {
                return $authorized;
            }
        }

        return $next($request);
    }

    private function handleCircleCI($request)
    {
        $jobId = $request->get('CIRCLE_BUILD_NUM');

        if (! $jobId) {
            return abort(400, 'CIRCLE_BUILD_NUM is missing');
        }

        if (! $this->confirmJobLegitimacy($jobId, $request->get('CIRCLE_USERNAME'), $request->get('CIRCLE_REPONAME'))) {
            return abort(400, "The CIRCLE_BUILD_NUM $jobId isn't currently running or CIRCLE_USERNAME didn't match.");
        }

        return true;
    }

    private function confirmJobLegitimacy($jobId, $user, $repo)
    {
        $circleCi = new CircleciAPI(env('CIRCLE_API_TOKEN'), $user, $repo);
        $job = $circleCi->getJob($jobId);

        if ($user !== $job['username']) {
            return false;
        }

        $statusOk = 'running' === $job['status'];

        $orga = $job['username'];
        $repoOk = \in_array($orga, ['algolia', 'bsuravech']);

        return $statusOk && $repoOk;
    }
}
