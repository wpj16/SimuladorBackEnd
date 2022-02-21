<?php

namespace App\Http\Business\Api\Time;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Time\Time;

class TimeBusinessRule extends MainBusinessRule
{

    private $modelTime;

    public function __construct()
    {
        $this->modelTime = new Time();
    }
}
