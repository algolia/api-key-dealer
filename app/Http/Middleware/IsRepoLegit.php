<?php

namespace App\Http\Middleware;

use App\TravisAPI;
use Closure;

class IsRepoLegit
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
        $jobId = $request->get('TRAVIS_JOB_ID');

        if (! $jobId) {
            return abort(400, 'TRAVIS_JOB_ID is missing');
        }

        if ($this->isTravisJobLegit($jobId)) {
            return $next($request);
        }

        return abort(400);
    }

    private function isTravisJobLegit($jobId)
    {
        $travis = new TravisAPI(env('TRAVIS_API_TOKEN'));
        $job = $travis->getJob($jobId);

        $statusOk = 'started' === $job['job']['state'];
        $repoOk = 'algolia/' === substr($job['job']['repository_slug'], 0, 8);

        config(['repository-name' => $job['job']['repository_slug']]);

        return $statusOk && $repoOk;
    }
}
