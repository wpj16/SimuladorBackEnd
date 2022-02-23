<?php

namespace App\Http\Business\Api\Time;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Time\Time;

class TimeBusinessRule extends MainBusinessRule
{

    private $modelTime;

    public function __construct()
    {
        $this->modelTime = new Time();
    }

    public function cadastrarTime(string $time): ResponseBusinessRule
    {
        $id = $this->modelTime->insertGetId([
            'nome' => $time
        ]);
        $dados = $this->modelTime->find($id)->toArray();
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Time cadastrado com com sucesso!')
            ->setMessageError('Falha ao cadastrar time!');
    }

    public function listarTimes(): ResponseBusinessRule
    {
        $dados =  $this->modelTime->get()->toArray();
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Times listados com com sucesso!')
            ->setMessageError('Nenhum time encontrado!');
    }
}
