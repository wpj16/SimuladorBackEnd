<?php

namespace App\Exceptions\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\Facades\HttpResponse;
use Illuminate\Database\QueryException as BaseException;

class QueryException extends BaseException
{
    const QUERY_MESSAGES = [
        'error' => 'Falha ao executar operação no banco de dados, entre em contato com o setor de TI!'
    ];

    public static function handler(BaseException $exception, Request $request)
    {  //faz rollback
        DB::rollBack();
        $responsejson = $request->wantsJson();
        $responsejson = $responsejson ?: $request->ajax();
        $message = self::QUERY_MESSAGES['error'];
        if ($responsejson) {
            $data = [
                'falha' => $exception->getMessage(),
                'arquivo' => $exception->getFile(),
                'linha' => $exception->getLine()
            ];
            return HttpResponse::json($data)
                ->code(404)
                ->message([$message, $exception->getMessage()])
                ->httpMessage($message)
                ->send();
        }
        return HttpResponse::return()
            ->code(404)
            ->message([$message, $exception->getMessage()], true)
            ->httpMessage($message, true)
            ->send();
    }
}
