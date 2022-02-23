<?php

namespace App\Http\Controllers\Api\Time;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Time\JogadorBusinessRule;
use Illuminate\Http\Request;

class JogadorController extends Controller
{
    const PESSOA_FISICA = 1;
    private $jogadorBusinessRule;

    public function __construct(JogadorBusinessRule $jogadorBusinessRule)
    {
        $this->jogadorBusinessRule = $jogadorBusinessRule;
    }

    public function cadastrarJogador(Request $request)
    {
        parent::validate($request)
            ->rules([
                'time' => 'required|numeric',
                'nome' => 'required|min:5',
                'email' => 'required|email',
                'documento' => 'required|brasil:cpf',
                'data_nascimento' => 'required|date_format:d/m/Y',
                'numero_camisa' => 'required|numeric'
            ])
            ->attributes([
                'time' => 'Time',
                'nome' => 'Nome',
                'email' => 'E-mail',
                'documento' => 'Cpf',
                'data_nascimento' => 'Data Nascimentt',
                'numero_camisa' => 'N° Camisa'
            ])
            ->success(function ($data) {
                $data['tipo'] = self::PESSOA_FISICA;
                $this->jogadorBusinessRule->cadastrarJogador($data)
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

    public function listarJogadores()
    {
        $this->jogadorBusinessRule->listarJogadores()
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
    }
}
