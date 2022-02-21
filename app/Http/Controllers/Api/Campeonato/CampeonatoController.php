<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Campeonato\CampeonatoBusinessRule;

class SorteioController extends Controller
{
    private $campeonatoBusinessRule;

    public function __construct(CampeonatoBusinessRule $campeonatoBusinessRule)
    {
        $this->campeonatoBusinessRule = $campeonatoBusinessRule;
    }
}
