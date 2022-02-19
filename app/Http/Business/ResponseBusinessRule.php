<?php

namespace App\Http\Business;

use Closure;

class ResponseBusinessRule
{
    /**
     * Parametros da classe
     */
    private bool   $error           = false;
    private array  $data            = [];
    private mixed  $class           = false;
    private string $message         = 'Ok';
    private string $messageError    = 'Nenhum registo encontrado!';
    private string $messageSuccess  = 'Operação realizada com sucesso!';
    /**
     * seta instancia da classe base de regra de negocio
     *
     * @return ResponseBusinessRule retorna a instância da classe
     */
    public function __construct(mixed $class)
    {
        $this->class = $class;
    }
    /**
     * seta se ouver erro ou não
     *
     * @return ResponseBusinessRule retorna a instância da classe
     */
    public function setError(bool $error = false): ResponseBusinessRule
    {
        $this->error = $error;
        return $this;
    }
    /**
     * Seta dados de resposta
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function setData(array $data = []): ResponseBusinessRule
    {
        $this->data = $data;
        $this->error = empty($this->data);
        return $this;
    }
    /**
     * Retorna dados de responsta
     *
     * @return array dados de resposta
     */
    public function getData(): array
    {
        return  $this->data;
    }
    /**
     * seta mensagem de caso de erro da operação do ultimo metodo chamado da classe de regra de negocio
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function setMessageError(string $message): ResponseBusinessRule
    {
        $this->messageError = $message;
        return $this;
    }
    /**
     * seta mensagem de caso de sucesso da operação do ultimo metodo chamado da classe de regra de negocio
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function setMessageSuccess(string $message): ResponseBusinessRule
    {
        $this->messageSuccess = $message;
        return $this;
    }
    /**
     * Retorna mensagem do tipo de resposta sucesso ou erro
     *
     * @return string mensagem do tipo de resposta sucesso ou erro
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    /**
     * Executa uma função Closure em caso de sucesso na chamada
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function success(Closure $success): ResponseBusinessRule
    {
        if (empty($this->error) && ($success instanceof Closure)) {
            $this->message = $this->messageSuccess;
            $instance = (clone $this);
            $this->data = [];
            $this->error = false;
            $this->message = 'OK';
            $return = $success($instance) ?: $instance;
            return (($return instanceof ResponseBusinessRule) ? $return : $instance);
        }
        return $this;
    }
    /**
     * Executa uma função Closure em caso de erro na chamada
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function error(Closure $error): ResponseBusinessRule
    {
        if ((!empty($this->error)) && ($error instanceof Closure)) {
            $this->message = $this->messageError;
            $instance = (clone $this);
            $this->data = [];
            $this->error = false;
            $this->message = 'OK';
            $return = $error($instance) ?: $instance;
            return (($return instanceof ResponseBusinessRule) ? $return : $instance);
        }
        return $this;
    }
    /**
     * Retorna status de erro true or false
     *
     * @return boolean status de erro true or false
     */
    public function isError(): bool
    {
        return $this->error;
    }
    /**
     * Chama qualquer outro metodo na classe base, caso não exista aqui
     *
     * @return ResponseBusinessRule retorna a instância da classe de regra de negocio chamada
     */
    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->class, $method], $arguments);
    }
}
