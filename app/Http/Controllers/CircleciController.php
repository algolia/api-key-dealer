<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Http\Middleware\IsCircleciRunning;

class CircleciController extends AlgoliaController
{
    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->middleware(IsCircleciRunning::class);

        parent::__construct($algoliaKeys);
    }
}
