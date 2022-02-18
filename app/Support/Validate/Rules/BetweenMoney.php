<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class BetweenMoney extends BaseRules
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        try {
            if (is_array($value) || (strlen($value) <= 0)) {
                $validator->setCustomMessages([$attribute . '.money_between' => "O campo :attribute não contém um valor monetário válido!"]);
                return false;
            }
            $moneyMin = (string)array_shift($parameters);
            $moneyMax = (string)array_shift($parameters);
            $valueFloat = parent::toFloat($value);
            $moneyMinFloat = parent::toFloat($moneyMin);
            $moneyMaxFloat = parent::toFloat($moneyMax);
            if ((strlen($valueFloat) > 0) && (strlen($moneyMinFloat) > 0) && (strlen($moneyMaxFloat) > 0)) {
                $min = 'R$ ' . parent::toMoneyBrasil($moneyMinFloat);
                $max = 'R$ ' .  parent::toMoneyBrasil($moneyMaxFloat);
                $validator->setCustomMessages([$attribute . '.money_between' => "O campo :attribute deve conter um valor entre $min e $max!"]);
                return (($valueFloat >= $moneyMinFloat) && ($valueFloat <= $moneyMaxFloat));
            }
            if ((strlen($valueFloat) > 0) && (strlen($moneyMinFloat) > 0)) {
                $min = 'R$ ' . parent::toMoneyBrasil($moneyMinFloat);
                $validator->setCustomMessages([$attribute . '.money_between' => "O campo :attribute deve conter um valor maior ou igual a $min!"]);
                return ($valueFloat >= $moneyMinFloat);
            }
            if ((strlen($valueFloat) > 0) &&  (strlen($moneyMaxFloat) > 0)) {
                $max = 'R$ ' .  parent::toMoneyBrasil($moneyMaxFloat);
                $validator->setCustomMessages([$attribute . '.money_between' => "O campo :attribute deve conter um valor menor ou igual a $max!"]);
                return ($valueFloat <= $moneyMaxFloat);
            }
            $validator->setCustomMessages([$attribute . '.money_between' => "O campo :attribute não contém uma regra de validação miníma e máxima valida!"]);
            return false;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.money_between' => 'Falha ao validar o campo :attribute!']);
            return false;
        }
    }
}
