<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class SorteioController extends Controller
{
    private $sorteioBusinessRule;

    public function __construct(SorteioBusinessRule $sorteioBusinessRule)
    {
        $this->sorteioBusinessRule = $sorteioBusinessRule;
    }

    public function simularCampeonato(Request $request)
    {
        parent::validate($request)
            ->rules([
                'campeonato' => 'required|numeric',
            ])
            ->success(function ($data) {
                //valida campeonato
                $this->sorteioBusinessRule
                    ->validarCampeonato($data['campeonato'])
                    ->error(function ($response) {
                        return parent::responseJson()
                            ->code(404)
                            ->message($response->getMessage())
                            ->send();
                    });

                //valida campeonato ja simulado
                $this->sorteioBusinessRule
                    ->validarCampeonatoSimulado($data['campeonato'])
                    ->success(function ($response) use ($data) {
                        //retorna jogos ja simulados
                        return $response
                            ->listarSimulacoesCampeonatos($data['campeonato'])
                            ->success(function ($responseJogos) use ($response) {
                                $data = $responseJogos->getData();
                                return parent::responseJson($data)
                                    ->code(207)
                                    ->message([$response->getMessage()])
                                    ->send();
                            })
                            ->error(function ($response) {
                                return parent::responseJson()
                                    ->code(404)
                                    ->message($response->getMessage())
                                    ->send();
                            });
                    })->error(function () use ($data) {
                        //faz simulacao
                        $simulacao = $this->sorteioBusinessRule->simular($data['campeonato'], 3);
                        $simulacao->success(function ($response) use ($data) {
                            //retorna jogos simulados
                            return $response
                                ->listarSimulacoesCampeonatos($data['campeonato'])
                                ->success(function ($response) {
                                    $data = $response->getData();
                                    return parent::responseJson($data)
                                        ->code(200)
                                        ->message($response->getMessage())
                                        ->send();
                                })
                                ->error(function ($response) {
                                    return parent::responseJson()
                                        ->code(404)
                                        ->message($response->getMessage())
                                        ->send();
                                });
                        });
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

    public function listarSimulacoesCampeonatos()
    {

        $this->sorteioBusinessRule->listarSimulacoesCampeonatos()
            ->success(function ($response) {
                $data = $response->getData();
                return parent::responseJson($data)
                    ->code(200)
                    ->message($response->getMessage())
                    ->send();
            })
            ->error(function ($response) {
                return parent::responseJson()
                    ->code(404)
                    ->message($response->getMessage())
                    ->send();
            });
    }
}
