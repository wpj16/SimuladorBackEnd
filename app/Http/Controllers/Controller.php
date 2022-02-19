<?php

namespace App\Http\Controllers;

use App\Support\Traits\Http;
use App\Support\Traits\Cast;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, Http, Cast;
}
