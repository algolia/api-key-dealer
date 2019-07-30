<?php

namespace App\Http\Controllers;

use App\AlgoliaKeys;
use App\Http\Middleware\IsCircleciRunning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CircleciController extends Controller
{
    public function __construct(AlgoliaKeys $algoliaKeys)
    {
        $this->middleware(IsCircleciRunning::class);

        parent::__construct($algoliaKeys);
    }
}
