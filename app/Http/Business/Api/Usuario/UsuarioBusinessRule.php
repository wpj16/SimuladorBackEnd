<?php

namespace App\Http\Business\Api\Usuario;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Usuario\Usuario;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Response as Psr7Response;
use League\OAuth2\Server\AuthorizationServer;

class UsuarioBusinessRule extends MainBusinessRule
{

    private $modelUsuario;

    public function __construct()
    {
        $this->modelUsuario = new Usuario();
    }

    public function login(array $data, AuthorizationServer $server): ResponseBusinessRule
    {
        $validate = (new ServerRequest('post', '/login', [], json_encode($data)))->withParsedBody($data);
        $psrResponse = $server->respondToAccessTokenRequest($validate, new Psr7Response);
        $response = json_decode((string)$psrResponse->getBody(), true);
        return parent::response()
            ->setData($response)
            ->setMessageSuccess('Login efetuado com sucesso!')
            ->setMessageError('Falha ao efetuar login!');
    }
}
