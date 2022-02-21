<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class SorteioController extends Controller
{
    private $simulacaoBusinessRule;

    public function __construct()
    {
        $this->simulacaoBusinessRule = new SorteioBusinessRule();
    }

    public function login(Request $request, AuthorizationServer $server)
    {
        parent::validate($request)
            ->rules([
                'username' => 'required|email',
                'password' => 'required|min:5',
                'scope' => 'default:null',
                'grant_type' => "required",
                'client_id' => "required|numeric",
                'client_secret' => "required",
            ])
            ->success(function ($data) use ($server) {
                $this->usuarioBusinessRule->login($data, $server)
                    ->success(function ($response) {
                        return parent::responseJson($response->getData())
                            ->code(200)
                            ->message($response->getMessage())
                            ->send();
                    })
                    ->error(function ($response) {
                        return parent::responseJson()
                            ->code(204)
                            ->message($response->getMessage())
                            ->send();
                    });
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
