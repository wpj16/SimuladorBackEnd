<?php

namespace App\Http\Controllers\Api\Campeonato;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Campeonato\CampeonatoBusinessRule;
use Illuminate\Http\Request;

class CampeonatoController extends Controller
{
    private $campeonatoBusinessRule;

    public function __construct(CampeonatoBusinessRule $campeonatoBusinessRule)
    {
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
                        return parent::responseJson($response->getData())
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
