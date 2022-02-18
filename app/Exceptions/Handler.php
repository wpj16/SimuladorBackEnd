<?php

namespace App\Exceptions;

use Throwable;
use App\Support\Facades\HttpResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    const PATH_EXCEPTIONS = "App\\Exceptions\\Exceptions\\";

    protected $dontReport = [
        \App\Support\Facades\HttpResponse::class,
        \App\Support\HttpResponse\HttpResponse::class,
        \League\OAuth2\Server\Exception\OAuthServerException::class
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->renderable(function (Throwable $exception, $request) {
            $class = (self::PATH_EXCEPTIONS . pathinfo(get_class($exception), PATHINFO_FILENAME));
            if (class_exists($class)) {
                return $class::handler($exception, $request);
            }
            $responsejson = $request->wantsJson();
            $responsejson = $responsejson ?: $request->ajax();
            $message = 'Falha ao executar operação, entre em contato com o setor de TI!';
            $messageException = $exception->getMessage();
            if ($responsejson) {
                $data = [
                    'falha' => $exception->getMessage(),
                    'arquivo' => $exception->getFile(),
                    'linha' => $exception->getLine()
                ];
                return HttpResponse::json($data)
                    ->code(404)
                    ->message([$message, $messageException])
                    ->httpMessage($message)
                    ->send();
            }
            dd('erro', $exception);
            return HttpResponse::return()
                ->code(303)
                ->message([$message, $messageException], true)
                ->httpMessage($message, true)
                ->send();
        });
    }
}
