<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Http\Middleware\IsTravisRunning;

class TravisController extends AlgoliaController
{
    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->middleware(IsTravisRunning::class);

        parent::__construct($algoliaKeys);
    }
}
