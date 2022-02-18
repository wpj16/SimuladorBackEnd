<?php

namespace App\Auth\Passport\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthValidateScope
{

    public function handle(Request $request, Closure $next, ...$guards)
    {
        /*
        $guard = $guards[0] ?? null;
        $user = $request->user($guard);
        $scopes = [];
        if ($guard == 'web_client') {
            $scopes = $user->clientScopes()->pluck('scope');
        }
        if ($guard == 'web_admin') {
            $scopes = $user->adminScopes()->pluck('scope');
        }
        $reqScopres = array_filter(explode('', $request->get('scope')));
        foreach ($reqScopres as  $reqScopre) {
            if (!in_array($reqScopre, $scopes)) {
                return 'escopo inválido para o usuário';
            }
        }
        */
        return $next($request);
    }
}
