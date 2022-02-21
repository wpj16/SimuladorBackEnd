<?php

namespace App\Exceptions\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\Facades\HttpResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException as BaseException;

class MethodNotAllowedHttpException extends BaseException
{
    const ERROR_MESSAGES = [
        'error' => 'Não há um serviço disponivel para rota ou metodo utilizado!'
    ];

    public static function handler(BaseException $exception, Request $request)
    {
        $responsejson = $request->wantsJson();
        $responsejson = $responsejson ?: $request->ajax();
        $message = self::ERROR_MESSAGES['error'];
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
            ->code(303)
            ->message([$message, $exception->getMessage()], true)
            ->httpMessage($message, true)
            ->send();
    }
}
