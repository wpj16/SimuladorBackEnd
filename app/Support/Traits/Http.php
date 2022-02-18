<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;
use App\Support\Facades\HttpClient;
use App\Support\Facades\HttpResponse;
use Illuminate\Support\Facades\Validator;
use App\Support\Validate\Validate as ValidatorFactory;
use App\Support\HttpClient\HttpClient as HttpClientFactory;
use App\Support\HttpResponse\HttpResponse as HttpResponseFactory;

trait  Http
{
    private $http;

    public function validate(Request|array $request): ValidatorFactory
    {
        return is_array($request) ? Validator::data($request) : Validator::request($request);
    }

    public function http(): HttpClientFactory
    {
        if (empty($this->http))
            return $this->http = HttpClient::instance();
        return $this->http;
    }

    public function return(): HttpResponseFactory
    {
        return HttpResponse::return();
    }

    public function redirect($url): HttpResponseFactory
    {
        return HttpResponse::redirect($url);
    }

    public function view(string $view, array $data = [], array $mergeData = []): HttpResponseFactory
    {
        return HttpResponse::view($view, $data, $mergeData);
    }

    public function responseJson(array $data = []): HttpResponseFactory
    {
        return HttpResponse::json($data);
    }
}
