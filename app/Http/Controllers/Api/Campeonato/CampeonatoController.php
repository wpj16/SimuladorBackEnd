<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class SorteioController extends Controller
{
    private $simulacaoBusinessRule;

    public function __construct()
    {
        $this->simulacaoBusinessRule = new SorteioBusinessRule();
    }

    public function teste()
    {

        $this->simulacaoBusinessRule->teste();
    }
}
