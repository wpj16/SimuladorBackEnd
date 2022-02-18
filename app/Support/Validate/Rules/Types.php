<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Types extends BaseRules
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        try {
            $return = false;
            switch (true) {
                case in_array('string_or_null', $parameters):
                    $validator->setCustomMessages([$attribute . '.types' => 'O campo ( :attribute ) deve conter um valor do tipo string ou nulo!']);
                    $return = is_string($value) || is_null($value);
                    break;
                case in_array('numeric_or_null', $parameters):
                    $validator->setCustomMessages([$attribute . '.types' => 'O campo ( :attribute ) deve conter um valor do tipo numérico ou nulo!']);
                    $return = is_numeric($value) || is_null($value);
                    break;
                case in_array('array_or_null', $parameters):
                    $validator->setCustomMessages([$attribute . '.types' => 'O campo ( :attribute ) deve conter um valor do tipo lista ou nulo!']);
                    $return = is_array($value) || is_null($value);
                    break;
                default:
                    $validator->setCustomMessages([$attribute . '.types' => 'O campo ( :attribute ) não pode ser validado!']);
            }
            return $return;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.default' => 'Falha ao atribuir um valor default ao campo :attribute!']);
            return false;
        }
    }
}
