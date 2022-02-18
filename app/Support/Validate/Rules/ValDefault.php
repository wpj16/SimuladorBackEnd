<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class ValDefault extends BaseRules
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        try {
            if (is_array($value)) {
                if (count($value) > 0) return true;
            }
            if (!is_array($value)) {
                if (strlen($value) > 0) return true;
            }
            //prepara valor default
            $data = $validator->getData();
            $default = current($parameters);
            if (count($parameters) === 1) {
                $compare = trim(strtolower($default));
                if (is_numeric($compare) || is_float($compare) || in_array($compare, ['null', '[]', 'array'])) {
                    $default = eval("return " . $default . ";");
                    $data = $this->setValueIndex($attribute, $data, $default);
                    $validator->setData($data);
                    return true;
                }
                $data[$attribute] = $default;
                $validator->setData($data);
                return true;
            }
            $default = $parameters;
            $data = parent::setValueIndex($attribute, $data, $default);
            $validator->setData($data);
            return true;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.default' => 'Falha ao atribuir um valor default ao campo :attribute!']);
            return false;
        }
    }
}
