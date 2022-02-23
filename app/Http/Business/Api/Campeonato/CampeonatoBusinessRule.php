<?php

namespace App\Http\Business\Api\Campeonato;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Campeonato\{
    Campeonato,
    CampeonatoTime
};

use App\Models\Time\Time;

class CampeonatoBusinessRule extends MainBusinessRule
{

    private $modelTime;
    private $modelCampeonato;
    private $modelCampeonatoTime;

    public function __construct()
    {
        $this->modelTime = new Time();
        $this->modelCampeonato = new Campeonato();
        $this->modelCampeonatoTime = new CampeonatoTime();
    }

    public  function cadastrarCampeonato(string $campeonato, array $times): ResponseBusinessRule
    {
        $validaTimes = $this->modelTime->whereIn('id', $times)->count();
        if ($validaTimes <> count($times)) {
            return parent::response()
                ->setError(true)
                ->setMessageError('A lista de time(s) contém time(s) inválidos!');
        }

        $id = $this->modelCampeonato->insertGetId([
            'nome' => $campeonato
        ]);

        foreach ($times as $time) {
            $this->modelCampeonatoTime->insert([
                'id_time' => $time,
                'id_campeonato' => $id,
            ]);
        }
        $dados = $this->modelCampeonato->find($id)->toArray();
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Time cadastrado com com sucesso!')
            ->setMessageError('Falha ao cadastrar time!');
    }

    public function listarCampeonatos()
    {
        $dados = $this->modelCampeonato->get()->toArray();
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Campeonatos listados com com sucesso!')
            ->setMessageError('Nenhum campeonato encontrado!');
    }
}
