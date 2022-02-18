<?php

namespace App\Exceptions\Exceptions;

use Illuminate\Http\Request;
use App\Support\Facades\HttpResponse;
use League\OAuth2\Server\Exception\OAuthServerException as BaseException;

class OAuthServerException extends BaseException
{
    const PASSPORT_MESSAGES = [
        'error' => 'Um erro ocorreu ao tentar se autenticar, tente novamente mais tarde!',
        'success' => 'Ok',
        'invalid_grant' => 'Usuário ou senha inválido(s), tente novamente!',
        'invalid_client' => 'Cliente não autorizado a se comunicar com a API, entre em contato com o Trade Technology!'
    ];

    public static function handler(BaseException $exception, Request $request)
    {
        $responsejson = $request->wantsJson();
        $responsejson = $responsejson ?: $request->ajax();
        $error = $exception->getErrorType();
        $message = self::PASSPORT_MESSAGES[$error] ?? self::PASSPORT_MESSAGES['error'];
        $messageException = empty(self::PASSPORT_MESSAGES[$error]) ? $exception->getMessage() : null;
        if ($responsejson) {
            return HttpResponse::json()
                ->code(404)
                ->message(array_filter([$message, $messageException]))
                ->httpMessage($message)
                ->send();
        }
        return HttpResponse::return()
            ->code(404)
            ->message(array_filter([$message, $messageException]), true)
            ->httpMessage($message, true)
            ->send();
    }
}
