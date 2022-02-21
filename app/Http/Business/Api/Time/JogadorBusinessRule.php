<?php

namespace App\Http\Business\Api\Time;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Time\Jogador;

class JogadorBusinessRule extends MainBusinessRule
{

    private $modelJogador;

    public function __construct()
    {
        $this->modelJogador = new Jogador();
    }
}
