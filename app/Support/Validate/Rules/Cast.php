<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Cast extends BaseRules
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        try {
            $newvalue = null;
            $data = $validator->getData();
            $cast = trim(strtolower(array_shift($parameters)));
            if (in_array($cast, ['regex-numeric', 'regex-numerico'])) {
                $newvalue = parent::onlyNumbers($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em numério, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['numeric', 'numerico'])) {
                $newvalue = parent::toNumeric($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em numério, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['int', 'integer', 'inteiro'])) {
                $newvalue = parent::toNumericInt($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em inteiro, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['float', 'flutuante'])) {
                $newvalue = parent::toFloat($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em flutuante, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['float-string', 'string-float'])) {
                $newvalue = parent::toFloatString($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em flutuante, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['money', 'moeda'])) {
                $newvalue = parent::toMoneyBrasil($value, 2);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em moeda, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['array', '[]'])) {
                $newvalue = parent::toArray($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em lista, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            if (in_array($cast, ['bool', 'boolean', 'booleano'])) {
                $newvalue = $this->toBoolean($value);
                $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em valor booleano, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!']);
            }
            $newvalue = (is_null($newvalue) ? $value : $newvalue);
            $data = parent::setValueIndex($attribute, $data, $newvalue);
            $validator->setData($data);
            return !is_null($newvalue);
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.cast' => 'O campo ( :attribute )  é inválido, falha ao converter o valor do campo, verifique o formato enviado e tente novamente!']);
            return false;
        }
    }
}
