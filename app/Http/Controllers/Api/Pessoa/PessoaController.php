<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Pessoa\PessoaBusinessRule;

class PessoaController extends Controller
{
    private $pessoaBusinessRule;

    public function __construct(PessoaBusinessRule $pessoaBusinessRule)
    {
        $this->pessoaBusinessRule = $pessoaBusinessRule;
    }
}
