<?php

namespace App\Support\HttpClient;

use Closure;
use Illuminate\Http\Client\Response as baseResponse;

class Response extends baseResponse
{

    const headerCode = 'Api-Code"';
    const headerMsg = 'Api-Message';
    private static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    public function setDataDecoded(array|null $data = null)
    {
        $this->decoded = is_null($data) ? $this->decoded : $data;
        return $this;
    }

    public function code()
    {
        $code = $this->header(self::headerCode);
        $code = empty($code) ? $this->status() : $code;
        return $code;
    }

    public function message()
    {
        $default = 'Um código http desconhecido foi retornado para a aplicação: (' . $this->code() . ').';
        $message = $this->header(self::headerMsg);
        $message = empty($message) ? $this->toPsrResponse()->getReasonPhrase() : $message;
        $message = empty($message) ? (self::$statusTexts[$this->code()] ?? $default) : $message;
        return $message;
    }

    public function messages()
    {
        if (!$this->ok()) {
            return $this->json();
        }
        return [];
    }

    public function data()
    {
        return $this->json();
    }

    public function error(Closure $error): Response
    {
        if (!$this->ok()) {
            if ($error instanceof Closure) {
                $return = $error($this) ?: $this;
                return (($return instanceof Response) ? $return : $this);
            }
        }
        return $this;
    }

    public function success(Closure $success): Response
    {
        if ($this->ok()) {
            if ($success instanceof Closure) {
                $return = $success($this) ?: $this;
                return (($return instanceof Response) ? $return : $this);
            }
        }
        return $this;
    }

    public function after(Closure $after): Response
    {
        if ($after instanceof Closure) {
            $return = $after($this) ?: $this;
            return (($return instanceof Response) ? $return : $this);
        }
        return $this;
    }
}
