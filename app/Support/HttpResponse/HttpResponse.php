<?php

namespace App\Support\HttpResponse;

use RuntimeException;
use Illuminate\View\{
    View
};
use Illuminate\Http\{
    Request,
    Response
};

class HttpResponse extends RuntimeException
{

    private $show;
    private $view;
    private $json;
    private $return;
    private $redirect;
    private $response;
    private $headers;
    private $request;
    private $clearWithInput;
    private int $responseHttpCode = 200;
    private string $responseHttpMsg = 'Ok';
    private int $responseCode = 200;
    private array $responseMsg = ['Ok'];
    private const OutputHeaderCode = 'Api-Code';
    private const OutputHeaderMessage = 'Api-Message';
    private const OutputHeaderListErrors = 'Api-Messages';
    private const ESCAPE_HTTP = [
        204 => 404
    ];

    private const ENCLIST = [
        'UTF-8', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4',
        'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9',
        'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'ASCII', 'Windows-1251', 'Windows-1252', 'Windows-1254',
    ];

    public function instance(): HttpResponse
    {
        return $this;
    }

    public function code(int $code): HttpResponse
    {
        $this->responseCode = $code;
        if (empty($this->responseHttpCode) || ($this->responseHttpCode == 200)) {
            $this->httpCode(self::ESCAPE_HTTP[$code] ?? $code);
        }
        return $this;
    }

    public function message(string|array $message, $show = false): HttpResponse
    {
        $this->show = $this->show ?: $show;
        $messagesList = is_array($message) ? $message : [$message];
        $messagesList = $this->singleLevelArray($messagesList);
        $this->responseMsg = $messagesList;
        if (empty($this->responseHttpMsg) || (strtolower($this->responseHttpMsg) == 'ok')) {
            $this->httpMessage($this->current($messagesList));
        }
        return $this;
    }

    public function headers(array $headers): HttpResponse
    {
        $this->headers = $headers;
        return $this;
    }

    public function httpCode(int $code): HttpResponse
    {
        $this->responseHttpCode = $code;
        return $this;
    }

    public function httpMessage(string $message, $show = false): HttpResponse
    {
        $this->show = $this->show ?: $show;
        $this->responseHttpMsg = $message;
        return $this;
    }

    public function request(Request $request): HttpResponse
    {
        $this->request = $request;
        return $this;
    }

    public function clear(bool $withInput = false): HttpResponse
    {
        $this->clearWithInput = $withInput;
        return $this;
    }

    public function view(string $view, array $data = [], $mergeData = []): HttpResponse
    {
        $this->response = null;
        $this->view = view($view, $data, $mergeData);
        return $this;
    }

    public function json(array $data = []): HttpResponse
    {
        $this->response = null;
        $this->json = response()->json($data);
        return $this;
    }

    public function return(): HttpResponse
    {
        $this->response = null;
        $this->return = redirect()
            ->back()
            ->setContent(null);
        return $this;
    }

    public function redirect($url): HttpResponse
    {
        $this->response = null;
        $this->redirect = redirect($url)
            ->setContent(null);
        return $this;
    }

    public function getResponse()
    {
        if (empty($this->response)) {
            $this->changeResponse();
        }
        return $this->response;
    }

    public function send(): HttpResponse
    {
        $this->changeResponse();
        return throw $this;
    }

    public function render()
    {
        return $this->response;
    }

    public function response($response = null)
    {
        $this->response = $response ?: $this->response;
        $this->changeResponse();
        return $this->response;
    }

    public function report()
    {
        return false;
    }

    private function changeResponse(): HttpResponse
    {
        $code = $this->responseCode;
        $listMessages = $this->responseMsg;
        $message = $this->current($listMessages);
        $httpCode = $this->responseHttpCode;
        $httpMessage = $this->responseHttpMsg;
        $success = (($httpCode == 200) && ($code == 200));
        $response = $this->response;
        $response = $response ?: $this->view;
        $response = $response ?: $this->json;
        $response = $response ?: $this->return;
        $response = $response ?: $this->redirect;
        $response = $response ?: $this->response;
        $this->response = $response;
        if (empty($this->response)) {
            return $this;
        }
        if (method_exists($this->response, 'withHeaders')) {
            $headers = $this->headers ?: [];
            $headers =  array_merge([
                self::OutputHeaderCode => $code,
                self::OutputHeaderMessage => $message,
                self::OutputHeaderListErrors => $listMessages
            ], $headers);
            $headers = $this->toCharset($headers, 'ISO-8859-1');
            $this->response?->withHeaders($headers);
        }
        if (method_exists($this->response, 'setStatusCode')) {
            $httpMessage = $this->toCharset($httpMessage, 'ISO-8859-1');
            $this->response?->setStatusCode($httpCode, $httpMessage);
        }
        if (method_exists($this->response, 'withInput')) {
            $this->request ? $this->response?->withInput($this->request?->all()) : null;
            $this->clearWithInput ? $this->response?->withInput([]) : null;
        }
        if ($success && $this->show) {
            $listMessages = $this->toCharset($listMessages, 'UTF-8');
            method_exists($this->response, 'with') ?
                $this->response?->with('success', $listMessages) : session()->flash('success', $listMessages);
        }
        if (empty($success) && $this->show) {
            $listMessages = $this->toCharset($listMessages, 'UTF-8');
            method_exists($this->response, 'withErrors') ?
                $this->response?->withErrors($listMessages) : session()->flash('error', $listMessages);
        }
        if ($this->response instanceof View) {
            $this->response = new Response($this->response);
            $this->changeResponse();
        }
        return $this;
    }

    private function singleLevelArray(array $data = [])
    {
        $singleLevelArray = [];
        array_walk_recursive($data, function ($value) use (&$singleLevelArray) {
            if (!empty($value) && !is_array($value)) {
                $singleLevelArray[] = $value;
            }
        });
        return array_filter($singleLevelArray);
    }

    private function current(array $data = [])
    {
        $first = current($data);
        if (is_array($first)) {
            return $this->current($first);
        }
        return $first;
    }

    private function toCharset(array|string $data, string $enc = 'UTF-8'): array|string
    {
        $isArr = is_array($data);
        $dataInput = $isArr ? $data : [$data];
        array_walk_recursive($dataInput, function (&$value) use ($enc) {
            $charset = md5($value);
            foreach (self::ENCLIST as $item) {
                $newCharsetA = iconv($item, (trim($item) . '//IGNORE'), $value);
                $newCharsetB = iconv(mb_detect_encoding($value, self::ENCLIST), (trim($item) . '//IGNORE'), $value);
                $newCharsetA = md5($newCharsetA);
                $newCharsetB = md5($newCharsetB);
                if (($newCharsetA === $charset) && ($newCharsetB ===  $charset)) {
                    return $value = iconv($item, $enc, $value);
                }
            }
            return $value;
        });
        return $isArr ? $dataInput : array_shift($dataInput);
    }
}
