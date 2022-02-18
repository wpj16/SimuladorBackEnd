<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Support\HttpResponse\HttpResponse;

class HttpResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        App::bind('HttpResponse', function () {
            return new HttpResponse();
        });
    }
}
