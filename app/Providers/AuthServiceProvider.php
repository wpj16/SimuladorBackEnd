<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Auth\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function register()
    {
        Passport::ignoreMigrations();
        $this->registerPolicies();
    }

    public function boot()
    {
        $scopes = [
            'admin' => 'Administrativo do sistema',
        ];
        // rota de autenticação default do site
        Passport::routes(
            function ($routes) {
                $routes->setMiddlewareRoutesAll(['passport']);
                $routes->forAuthorization();
                $routes->forAccessTokens();
                $routes->forTransientTokens();
                $routes->forClients();
                $routes->forPersonalAccessTokens();
            },
            [
                'prefix' => '/oauth/',
            ]
        );
        //
        Passport::tokensCan(array_merge($scopes));
    }
}
