<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class SorteioController extends Controller
{
    private $sorteioBusinessRule;

    public function __construct()
    {
        $this->sorteioBusinessRule = new SorteioBusinessRule();
    }

    public function teste()
    {
        return $this->sorteioBusinessRule->simular(1, 3);
    }
}
