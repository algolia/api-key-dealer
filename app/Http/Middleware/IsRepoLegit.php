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

        error_log("JOB ID: $jobId\n");

        if (! $jobId) {
            return abort(400, 'TRAVIS_JOB_ID is missing');
        }

        if ($this->isTravisJobLegit($jobId)) {
            return $next($request);
        }

        return abort(400, "The TRAVIS_JOB_ID $jobId isn't currently running");
    }

    private function isTravisJobLegit($jobId)
    {
        $travis = new TravisAPI(env('TRAVIS_API_TOKEN'));
        $job = $travis->getJob($jobId);

        error_log("JOB: ".json_encode($job)."\n");
        error_log("repo substr: ".substr($job['job']['repository_slug'], 0, 15));

        $statusOk = 'started' === $job['job']['state'];
        $repoOk = 'algolia/' === substr($job['job']['repository_slug'], 0, 8);

        if (! $repoOk) {
            $repoOk = 'julienbourdeau/' === substr($job['job']['repository_slug'], 0, 15);
        }

        config(['repository-name' => $job['job']['repository_slug']]);

        return $statusOk && $repoOk;
    }
}
