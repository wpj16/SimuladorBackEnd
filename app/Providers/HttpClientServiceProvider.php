<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Support\HttpClient\HttpClient;

class HttpClientServiceProvider extends ServiceProvider
{

    public function register()
    {
        App::bind('HttpClient', function () {
            $options = [
                'verify' => getenv('API_WEBSERVICE_SSL')
            ];
            $http = new HttpClient();
            return $http
                ->withOptions($options)
                ->contentType('application/json')
                ->acceptJson()
                ->baseUrl(getenv('API_WEBSERVICE_URL'));
        });
    }
}
