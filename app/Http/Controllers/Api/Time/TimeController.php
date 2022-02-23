<?php

namespace App\Http\Controllers\Api\Time;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Business\Api\Time\TimeBusinessRule;

class TimeController extends Controller
{
    private $timeBusinessRule;

    public function __construct(TimeBusinessRule $timeBusinessRule)
    {
        $this->timeBusinessRule = $timeBusinessRule;
    }

    public function cadastrarTime(Request $request)
    {
        parent::validate($request)
            ->rules([
                'time' => 'required|min:5',
            ])
            ->attributes([
                'time' => 'Time',
            ])
            ->success(function ($data) {
                $this->timeBusinessRule->cadastrarTime($data['time'])
                    ->success(function ($response) {
                        return parent::responseJson($response->getData())
                            ->code(200)
                            ->message($response->getMessage())
                            ->send();
                    })
                    ->error(function ($response) {
                        return parent::responseJson()
                            ->code(204)
                            ->message($response->getMessage())
                            ->send();
                    });
            })
            ->error(function ($errors) {
                return parent::responseJson()
                    ->code(404)
                    ->message($errors)
                    ->send();
            })
            ->validate();
    }

    public function listarTimes()
    {
        $this->timeBusinessRule->listarTimes()
            ->success(function ($response) {
                return parent::responseJson($response->getData())
                    ->code(200)
                    ->message($response->getMessage())
                    ->send();
            })
            ->error(function ($response) {
                return parent::responseJson()
                    ->code(204)
                    ->message($response->getMessage())
                    ->send();
            });
    }
}
