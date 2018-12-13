<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Http\Middleware\IsTravisRunning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TravisController extends Controller
{
    /**
     * @var \App\AlgoliaKeys
     */
    private $algoliaKeys;

    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->middleware(IsTravisRunning::class);

        $this->algoliaKeys = $algoliaKeys;
    }

    public function getAlgoliaCredentials(Request $request)
    {
        $response = [];
        $config = config('repository-config');
        $ip = $request->getClientIp();

        $response = $this->algoliaKeys->forge($config, $ip);

        $this->log(array_merge($response, ['IP Address' => $ip]));

        return response($response, 201);
    }

    private function log($context)
    {
        $context['api-key'] = substr($context['api-key'], 0, 8).'...';

        Log::channel('slack')->notice(
            'Generated access for '.config('repository-name'),
            $context
        );
    }
}
