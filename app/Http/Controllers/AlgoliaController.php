<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Config;
use App\Http\Middleware\IsCircleciRunning;
use App\Http\Middleware\IsTravisRunning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlgoliaController extends Controller
{
    /**
     * @var \App\AlgoliaKeys
     */
    private $algoliaKeys;

    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->algoliaKeys = $algoliaKeys;

        $this->middleware(IsTravisRunning::class);
        $this->middleware(IsCircleciRunning::class);
    }

    public function getAlgoliaCredentials(Request $request)
    {
        $config = config('repository-config');
        $isClient = Str::contains(config('repository-name'), '-client-');

        $keys = $this->algoliaKeys->forge($config, $isClient);

        $response = array_merge($keys, [
            'comment' => $this->getComment($config),
            'request-id' => config('request_id'),
        ]);

        $this->log(array_merge($response, ['IP Address' => $request->getClientIp()]));

        return response($response, 201);
    }

    private function getComment(Config $config)
    {
        $validity = $config->getKeyParams()['validity'] / 60;

        return "The keys will expire after $validity minutes.";
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
