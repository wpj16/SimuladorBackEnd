<?php

namespace App\Http\Business;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Business\ResponseBusinessRule;

class MainBusinessRule
{
    /**
     * Retorna um formato de instancia de responsta para metodos das classes de negocio
     *
     * @return ResponseBusinessRule instancia de resposta para metodos de regra de negocio
     */
    protected function response(): ResponseBusinessRule
    {
        return (new ResponseBusinessRule($this));
    }
}
