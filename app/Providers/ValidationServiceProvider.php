<?php

namespace App\Providers;

use App\Support\Validate\Rules\{
    Cast,
    Types,
    Brasil,
    ValDefault,
    BetweenMoney
};
use App\Support\Validate\Validate;
use Illuminate\Validation\ValidationServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    protected function registerValidationFactory()
    {
        $this->app->singleton('validator', function ($app) {
            $validator = new Validate($app['translator'], $app);
            if (isset($app['db']) && isset($app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }
            return $validator;
        });
    }

    public function boot()
    {
        $this->app['validator']->extend('cast', Cast::class);
        $this->app['validator']->extend('types', Types::class);
        $this->app['validator']->extend('brasil', Brasil::class);
        $this->app['validator']->extend('money_between', BetweenMoney::class);
        $this->app['validator']->extendImplicit('default', ValDefault::class);
    }
}
