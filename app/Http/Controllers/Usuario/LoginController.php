<?php

namespace App\Http\Controllers\Usuario;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;

class AdminLoginController extends Controller
{
    use HandlesOAuthErrors;

    public function login(ServerRequestInterface $request, AuthorizationServer $server)
    {
        parent::validate($request->getParsedBody())
            ->rules([
                'username' => 'required|email',
                'password' => 'required',
                'scope' => 'default:null',
                'grant_type' => "required",
                'client_id' => "required|numeric",
                'client_secret' => "required",
            ])
            ->success(function ($x) use ($request, $server) {
                $psrResponse = $server->respondToAccessTokenRequest($request, new Psr7Response);
                $data = json_decode((string)$psrResponse->getBody(), true);
                return parent::responseJson($data)
                    ->code(200)
                    ->message('Usuário autenticado com sucesso!')
                    ->send();
            })
            ->error(function ($errors) {
                return parent::responseJson()
                    ->code(401)
                    ->message($errors)
                    ->send();
            })
            ->validate();
    }
}
