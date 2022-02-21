<?php

namespace App\Http\Business\Api\Pessoa;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Pessoa\Pessoa;

class PessoaBusinessRule extends MainBusinessRule
{

    private $modelPessoa;

    public function __construct()
    {
        $this->modelPessoa = new Pessoa();
    }
}
