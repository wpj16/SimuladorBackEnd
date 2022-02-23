<?php

namespace App\Http\Controllers\Api\Campeonato;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Campeonato\CampeonatoBusinessRule;
use Illuminate\Http\Request;
use App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class CampeonatoController extends Controller
{
    private $sorteioBusinessRule;
    private $campeonatoBusinessRule;

    public function __construct(CampeonatoBusinessRule $campeonatoBusinessRule, SorteioBusinessRule $sorteioBusinessRule)
    {
        $this->sorteioBusinessRule = $sorteioBusinessRule;
        $this->campeonatoBusinessRule = $campeonatoBusinessRule;
    }

    public function cadastrarCampeonato(Request $request)
    {
        parent::validate($request)
            ->rules([
                'campeonato' => 'required|min:5',
                'times' => 'required|array|min:8|max:8',
                'times.*' => 'int',
            ])
            ->attributes([
                'times' => 'Times',
                'campeonato' => 'Campeonato',
            ])
            ->success(function ($data) {
                $this->campeonatoBusinessRule->cadastrarCampeonato($data['campeonato'], $data['times'])
                    ->success(function ($response) {
                        $novoCampeonato = $response->getData();
                        $this->sorteioBusinessRule->simular($novoCampeonato['id'], 3)
                            ->error(function ($response) {
                                return parent::responseJson()
                                    ->code(404)
                                    ->message($response->getMessage())
                                    ->send();
                            })
                            ->success(function ($response) use ($novoCampeonato) {
                                return parent::responseJson([
                                    'campeonato' => $novoCampeonato,
                                    'simulacao' => $response->getData()
                                ])
                                    ->code(200)
                                    ->message($response->getMessage())
                                    ->send();
                            });
                    })
                    ->error(function ($response) {
                        return parent::responseJson()
                            ->code(404)
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

    public function listarCampeonatos()
    {
        $this->campeonatoBusinessRule->listarCampeonatos()
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
