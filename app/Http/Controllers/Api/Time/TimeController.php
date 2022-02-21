<?php

namespace App\Http\Controllers\Api\Simulacao;

use App\Http\Controllers\Controller;
use App\Http\Business\Api\Time\TimeBusinessRule;

class TimeController extends Controller
{
    private $simulacaoBusinessRule;

    public function __construct(TimeBusinessRule $timeBusinessRule)
    {
        $this->timeBusinessRule = $timeBusinessRule;
    }
}
