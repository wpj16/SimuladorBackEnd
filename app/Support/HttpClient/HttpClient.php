<?php

namespace App\Support\HttpClient;

use Closure;
use Throwable;
use App\Support\HttpClient\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;

class HttpClient extends PendingRequest
{
    private $error;
    private $success;
    private $after;
    private $before;

    public function ip(): string
    {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_FORWARDED', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
        return $ipaddress;
    }

    public function instance(): HttpClient
    {
        return $this;
    }

    public function error(Closure $error): HttpClient
    {
        $this->error = $error;
        return $this;
    }

    public function success(Closure $success): HttpClient
    {
        $this->success = $success;
        return $this;
    }

    public function after(Closure $after): HttpClient
    {
        $this->after = $after;
        return $this;
    }

    public function before(Closure $before): HttpClient
    {
        $this->before = $before;
        return $this;
    }

    protected function prepareUrl(string $uri = '/'): string
    {
        $expressao = '/(\/)\1+/';
        $replace = '$1';
        $newurl = preg_replace($expressao, $replace, $uri);
        return str_ireplace(['http:/', 'https:/'], ['http://', 'https://'], $newurl);
    }


    public function send(string $method, string $url, array $options = []): Response
    {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');
        $url = $this->prepareUrl($url);

        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $options[$this->bodyFormat] = $this->pendingBody;
            }

            if (is_array($options[$this->bodyFormat])) {
                $options[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat],
                    $this->pendingFiles
                );
            }
        }

        [$this->pendingBody, $this->pendingFiles] = [null, []];

        return retry($this->tries ?? 1, function () use ($method, $url, $options) {
            try {
                $before = $this->before;
                if ($before instanceof Closure) {
                    $before($this);
                }
                $laravelData = $this->parseRequestData($method, $url, $options);

                return tap(new Response(
                    $this->buildClient()->request(
                        $method,
                        $url,
                        $this->mergeOptions([
                            'laravel_data' => $laravelData,
                            'on_stats' => function ($transferStats) {
                                $this->transferStats = $transferStats;
                            },
                        ], $options)
                    )
                ), function ($response) {

                    $this->error ? $response->error($this->error) : null;
                    $this->success ? $response->success($this->success) : null;
                    $this->after ? $response->after($this->after) : null;

                    $response->cookies = $this->cookies;
                    $response->transferStats = $this->transferStats;

                    if ($this->tries > 1 && !$response->successful()) {
                        $response->throw();
                    }
                });
            } catch (ConnectException $e) {
                throw new ConnectionException($e->getMessage(), 0, null);
            } catch (Throwable $e) {
                throw new ConnectionException($e->getMessage(), 0, $e);
            }
        }, $this->retryDelay ?? 100);
    }
}
