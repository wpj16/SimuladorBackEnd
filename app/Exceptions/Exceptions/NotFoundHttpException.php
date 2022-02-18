<?php

namespace App\Exceptions\Exceptions;

use Illuminate\Http\Request;
use App\Support\Facades\HttpResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as BaseException;

class NotFoundHttpException extends BaseException
{
    const NOTFOUND_MESSAGES = [
        'error' => 'Nenhum serviço está disponível para rota solicitada!!'
    ];

    public static function handler(BaseException $exception, Request $request)
    {  //faz rollback
        $responsejson = $request->wantsJson();
        $responsejson = $responsejson ?: $request->ajax();
        $message = self::NOTFOUND_MESSAGES['error'];
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
                ->response();
        }
        return HttpResponse::return()
            ->code(404)
            ->message([$message, $exception->getMessage()], true)
            ->httpMessage($message, true)
            ->send();
    }
}
