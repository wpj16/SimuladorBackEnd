<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Time\JogadorBusinessRule;

class JogadorController extends Controller
{
    private $simulacaoBusinessRule;

    public function __construct(JogadorBusinessRule $jogadorBusinessRule)
    {
        $this->simulacaoBusinessRule = $jogadorBusinessRule;
    }
}
