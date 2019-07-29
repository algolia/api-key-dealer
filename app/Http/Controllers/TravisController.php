<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Config;
use App\Http\Middleware\IsTravisRunning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TravisController extends AlgoliaController
{
    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->middleware(IsTravisRunning::class);

        parent::__construct($algoliaKeys);
    }
}
