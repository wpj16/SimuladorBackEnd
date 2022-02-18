<?php

namespace App\Auth\Passport;

use Laravel\Passport\RouteRegistrar as MainRouteRegistrar;
use Illuminate\Contracts\Routing\Registrar as Router;
use PhpParser\Node\Stmt\Foreach_;

class RouteRegistrar extends MainRouteRegistrar
{

    private $middlewaresRoutes = ['web', 'auth'];
    private $middlewaresRoutesAll = [];
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */


    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function setMiddlewareRoutes(array $middlewaresRoutes)
    {
        $this->middlewaresRoutes = $middlewaresRoutes;
    }

    public function setMiddlewareRoutesAll(array $middlewaresRoutes)
    {
        $this->middlewaresRoutesAll = $middlewaresRoutes;
    }
    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forAuthorization();
        $this->forAccessTokens();
        $this->forTransientTokens();
        $this->forClients();
        $this->forPersonalAccessTokens();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forAuthorization(array $middleares = [])
    {

        $middleares = $this->priorityArray(
            'web',
            array_merge(array_merge($middleares, $this->middlewaresRoutes), $this->middlewaresRoutesAll)
        );
        $this->router->group(['middleware' => $middleares], function ($router) {
            $router->get('/authorize', [
                'uses' => 'AuthorizationController@authorize',
                'as' => 'passport.authorizations.authorize',
            ]);

            $router->post('/authorize', [
                'uses' => 'ApproveAuthorizationController@approve',
                'as' => 'passport.authorizations.approve',
            ]);

            $router->delete('/authorize', [
                'uses' => 'DenyAuthorizationController@deny',
                'as' => 'passport.authorizations.deny',
            ]);
        });
    }

    /**
     * Register the routes for retrieving and issuing access tokens.
     *
     * @return void
     */


    public function forAccessTokens(array $middleares = [])
    {

        $this->router->post('/token', [
            'uses' => 'AccessTokenController@issueToken',
            'as' => 'passport.token',
        ])->middleware(array_merge(['throttle'], array_merge($middleares, $this->middlewaresRoutesAll)));


        $middleares = array_merge($middleares, array_merge($this->middlewaresRoutes, $this->middlewaresRoutesAll));

        $this->router->group(['middleware' => $middleares], function ($router) {
            $router->get('/tokens', [
                'uses' => 'AuthorizedAccessTokenController@forUser',
                'as' => 'passport.tokens.index',
            ]);

            $router->delete('/tokens/{token_id}', [
                'uses' => 'AuthorizedAccessTokenController@destroy',
                'as' => 'passport.tokens.destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for refreshing transient tokens.
     *
     * @return void
     */
    public function forTransientTokens(array $middleares = [])
    {
        $middleares = array_merge($middleares, array_merge($this->middlewaresRoutes, $this->middlewaresRoutesAll));

        $this->router->post('/token/refresh', [
            'middleware' =>  $middleares,
            'uses' => 'TransientTokenController@refresh',
            'as' => 'passport.token.refresh',
        ]);
    }

    /**
     * Register the routes needed for managing clients.
     *
     * @return void
     */
    public function forClients(array $middleares = [])
    {
        $middleares = array_merge($middleares, array_merge($this->middlewaresRoutes, $this->middlewaresRoutesAll));

        $this->router->group(['middleware' => $middleares], function ($router) {
            $router->get('/clients', [
                'uses' => 'ClientController@forUser',
                'as' => 'passport.clients.index',
            ]);

            $router->post('/clients', [
                'uses' => 'ClientController@store',
                'as' => 'passport.clients.store',
            ]);

            $router->put('/clients/{client_id}', [
                'uses' => 'ClientController@update',
                'as' => 'passport.clients.update',
            ]);

            $router->delete('/clients/{client_id}', [
                'uses' => 'ClientController@destroy',
                'as' => 'passport.clients.destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens(array $middleares = [])
    {
        $middleares = array_merge($middleares, array_merge($this->middlewaresRoutes, $this->middlewaresRoutesAll));

        $this->router->group(['middleware' => $middleares], function ($router) {
            $router->get('/scopes', [
                'uses' => 'ScopeController@all',
                'as' => 'passport.scopes.index',
            ]);

            $router->get('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@forUser',
                'as' => 'passport.personal.tokens.index',
            ]);

            $router->post('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@store',
                'as' => 'passport.personal.tokens.store',
            ]);

            $router->delete('/personal-access-tokens/{token_id}', [
                'uses' => 'PersonalAccessTokenController@destroy',
                'as' => 'passport.personal.tokens.destroy',
            ]);
        });
    }

    private function priorityArray(string $value, array $data)
    {
        if (in_array($value, $data)) {
            $data = array_diff($data, [$value]);
            array_unshift($data, $value);
        }
        return $data;
    }
}
