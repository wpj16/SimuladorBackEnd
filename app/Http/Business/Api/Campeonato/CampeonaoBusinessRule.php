<?php

namespace App\Http\Business\Api\Campeonato;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Campeonato\Campeonato;

class CampeonatoBusinessRule extends MainBusinessRule
{

    private $modelCampeonato;

    public function __construct()
    {
        $this->modelCampeonato = new Campeonato();
    }
}
