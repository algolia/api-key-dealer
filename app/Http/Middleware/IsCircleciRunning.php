<?php

namespace App\Http\Middleware;

use App\ExternalApis\CircleciAPI;

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

        if (! $this->confirmJobLegitimacy($jobId, $request->get('CIRCLE_WORKFLOW_ID'), $request->get('CIRCLE_USERNAME'))) {
            return abort(400, "The CIRCLE_BUILD_NUM $jobId isn't currently running 
                or CIRCLE_WORKFLOW_ID and/or CIRCLE_USERNAME didn't match.");
        }

        return true;
    }

    private function confirmJobLegitimacy($jobId, $workflowId, $projectUserName)
    {
        $circleCi = new CircleciAPI(env('CIRCLE_API_TOKEN'));
        $job = $circleCi->getJob($jobId);

        if ($workflowId !== $job['workflows']['workflow_id']) {
            return false;
        }

        if ($projectUserName !== $job['username']) {
            return false;
        }

        $statusOk = 'running' === $job['status'];

        $orga = $job['username'];
        $repoOk = \in_array($orga, ['algolia', 'bsuravech']);

        return $statusOk && $repoOk;
    }
}
