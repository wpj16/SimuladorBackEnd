<?php

namespace App\Http\Middleware;

use Closure;
use Throwable;
use App\Models\Log\Sessao;
use Illuminate\Support\Facades\DB;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\Support\Facades\HttpResponse;
use Illuminate\Support\Facades\Validator;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;


class ApiAuthPassportCheckCredentials extends CheckClientCredentials
{
    //Parametros de entrada obrigatorios no header da sessão
    //Parametros usados para validar ambiente administrativo
    //
    const InputHeaderClientIp = 'Client-Ip';
    const InputHeaderClientUser = 'Client-User';
    const InputHeaderClientLocalization = 'Client-Localization';
    //
    //redireciona para esta rota caso o usuário não esteja logado
    const REDIRECT_LOGOUT = '/';
    const MESSAGE_UNAUTHORIZED = 'Usuário não autenticado, ou sem acesso ao recurso solicitado!';

    public function handle($request, Closure $next, ...$scopes)
    {
        $response = null;
        $responsejson = $request->wantsJson();
        $responsejson = $responsejson ?: $request->ajax();
        $token = $request->bearerToken();
        //Valida psr factory
        try {
            $psr = (new PsrHttpFactory(
                new Psr17Factory,
                new Psr17Factory,
                new Psr17Factory,
                new Psr17Factory
            ))->createRequest($request);
            $psr = $this->server->validateAuthenticatedRequest($psr);
            $this->validate($psr, $scopes);
        } catch (Throwable $e) {
            if ($responsejson) {
                return HttpResponse::json()
                    ->code(401)
                    ->message([self::MESSAGE_UNAUTHORIZED, $e->getMessage()])
                    ->httpMessage(self::MESSAGE_UNAUTHORIZED)
                    ->send();
            }
            return HttpResponse::redirect(self::REDIRECT_LOGOUT)
                ->code(401)
                ->message([self::MESSAGE_UNAUTHORIZED, $e->getMessage()], true)
                ->httpMessage(self::MESSAGE_UNAUTHORIZED)
                ->send();
        }
        //valida variaveis obrigatórias do cabeçalho da requisição
        $header = getallheaders() ?: [];
        $header[self::InputHeaderClientUser] = $request?->user()?->id;
        $validation = [
            self::InputHeaderClientIp => 'required|ip',
            self::InputHeaderClientUser => 'required|numeric',
            self::InputHeaderClientLocalization => 'required|json|cast:array',
            self::InputHeaderClientLocalization . '.latitude' => 'required|regex:/[0-9\,\.]/',
            self::InputHeaderClientLocalization . '.longitude' => 'required|regex:/[0-9\,\.]/',
        ];
        $attributes = [
            self::InputHeaderClientIp => '( "Client-Ip" cabeçalho da requisição )',
            self::InputHeaderClientUser => '( "Client-User" cabeçalho da requisição )',
            self::InputHeaderClientLocalization => '( "Client-Localization" cabeçalho da requisição )'
        ];
        $messages = [
            self::InputHeaderClientIp => 'IP, header da requsição, é inválido!',
            self::InputHeaderClientUser => 'Usuário não autenticado!',
            self::InputHeaderClientLocalization . '.latitude.regex' => 'O campo ( latitude ) dentro do json de geolocalização, no cabeçalho da requisição, não contém um valor válido!',
            self::InputHeaderClientLocalization . '.longitude.regex' => 'O campo ( longitude ) dentro do json de geolocalização, no cabeçalho da requisição, não contém um valor válido!',
            self::InputHeaderClientLocalization . '.latitude.required' => 'O campo ( latitude ) cabeçalho da requisição, é obrigatório dentro do json de geolocalição ( ' . self::InputHeaderClientLocalization . ' ).',
            self::InputHeaderClientLocalization . '.longitude.required' => 'O campo ( longitude ) cabeçalho da requisição, é obrigatório dentro do json de geolocalição ( ' . self::InputHeaderClientLocalization . ' ).',
        ];

        Validator::data($header)
            ->rules($validation)
            ->messages($messages)
            ->attributes($attributes)
            ->error(function ($errors) use ($responsejson) {
                if ($responsejson) {
                    return HttpResponse::json($errors)
                        ->code(401)
                        ->message($errors)
                        ->send();
                }
                return HttpResponse::redirect(self::REDIRECT_LOGOUT)
                    ->code(401)
                    ->message($errors, true)
                    ->send();
            })
            ->success(function ($data)  use ($token, $next, $request, &$response) {

                /* valida dados da localização e cria uma sesão ou atualiza a existente no banco */
                $sessao = new Sessao();
                $locy = $data[self::InputHeaderClientLocalization]['latitude'] ?? '';
                $locx = $data[self::InputHeaderClientLocalization]['longitude'] ?? '';
                $keyjson = preg_replace('/[^0-9]/', '', ($locy . $locx));
                $sessiondb = $sessao->select('log_ip')->where(
                    [
                        ['log_ip', '=', trim($data[self::InputHeaderClientIp])],
                        ['log_usuario', '=', trim($data[self::InputHeaderClientUser])],
                        ['log_token', '=', trim($token)]
                    ]
                )->get()->toArray();

                if (count($sessiondb) > 0) {
                    /* atualiza sessão aberta com a localização geografica atual */
                    $sessao->where(
                        [
                            ['log_ip', '=', trim($data[self::InputHeaderClientIp])],
                            ['log_usuario', '=', trim($data[self::InputHeaderClientUser])],
                            ['log_token', '=', trim($token)]
                        ]
                    )->update([
                        'log_localizacao' => DB::raw(
                            'coalesce(log_localizacao,\'{}\')::jsonb||(\'{"' . $keyjson . '":{"longitude":"' . $locx . '","latitude":"' . $locy . '","data":"\'||CURRENT_TIMESTAMP||\'"}}\')::jsonb'
                        ),
                    ]);
                    return  $response = $next($request);
                }
                /* cria uma sessão nova no banco de dados para log das operações com localização atual */
                $sessao->insert([
                    'log_token' => trim($token),
                    'log_ip' => trim($data[self::InputHeaderClientIp]),
                    'log_usuario' => trim($data[self::InputHeaderClientUser]),
                    'log_localizacao' => DB::raw('(\'{"' . $keyjson . '":{"longitude":"' . $locx . '","latitude":"' . $locy . '","data":"\'||CURRENT_TIMESTAMP||\'"}}\')::jsonb')
                ]);
                return  $response = $next($request);
            })
            ->validate();
        return $response;
    }
}
