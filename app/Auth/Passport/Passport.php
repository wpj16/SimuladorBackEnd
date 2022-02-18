<?php

namespace App\Auth\Passport;

use Laravel\Passport\Passport as MainPassport;
use App\Auth\Passport\RouteRegistrar;
use Illuminate\Support\Facades\Route;

class Passport extends MainPassport
{

    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }

    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }
}
