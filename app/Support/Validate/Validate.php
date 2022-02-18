<?php

namespace App\Support\Validate;

use Closure;
use Illuminate\Http\{
    Request
};
use \Illuminate\Validation\{
    Factory,
    ValidationException
};
use Illuminate\Contracts\{
    Container\Container,
    Translation\Translator
};

class Validate extends Factory
{
    private $error;
    private $success;
    private $data = [];
    private $rules = [];
    private $attributes = [];
    private $messages = [];
    private $default = [];

    public function __construct(Translator $translator, Container $container = null)
    {
        parent::__construct($translator, $container);
    }

    public function request(Request $request): Validate
    {
        $this->data = $request->all();
        return $this;
    }

    public function data(array $data = []): Validate
    {
        $this->data = $data;
        return $this;
    }

    public function rules(array $rules = []): Validate
    {
        $this->rules = $rules;
        return $this;
    }

    public function attributes(array $attributes = []): Validate
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function messages(array $messages = []): Validate
    {
        $this->messages = $messages;
        return $this;
    }

    public function defualts(array $defualts = []): Validate
    {
        $this->default = $defualts;
        return $this;
    }

    public function error(Closure $error): Validate
    {
        $this->error = $error;
        return $this;
    }

    public function success(Closure $success): Validate
    {
        $this->success = $success;
        return $this;
    }

    public function validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $data = array_merge($this->default, $this->data, $data);
        $rules = array_merge($this->rules, $rules);
        $messages = array_merge($this->messages, $messages);
        $customAttributes = array_merge($this->attributes, $customAttributes);
        $error = $this->error;
        $success = $this->success;
        try {
            $validator = parent::make($data, $rules, $messages, $customAttributes);
            $isError = $validator->fails();
            if (!empty($isError)) {
                $errorsList = $validator->errors()->toArray();
                if ($error instanceof Closure) {
                    $error($errorsList, $this, $validator);
                }
                return $validator;
            }

            $data = $validator->validated();
            if (empty($isError)) {
                if ($success instanceof Closure) {
                    $success($data, $this, $validator);
                }
                return $validator;
            }
            return $validator;
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            if ($error instanceof Closure) {
                $error($errors, $this, $validator);
            }
            return $e->validator;
        }
        return null;
    }


    private function current(array $data = [])
    {
        $first = current($data);
        if (is_array($first)) {
            return $this->current($first);
        }
        return $first;
    }
}
