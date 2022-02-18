<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Brasil extends BaseRules
{

    public function validate($attribute, $value, $parameters, Validator $validator)
    {
        try {
            $chek = false;
            $type = trim(strtolower(array_shift($parameters)));
            if ($type == 'cpf') {
                $chek = parent::validateCpf($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um CPF válido!']);
            }
            if ($type == 'rg') {
                $chek = parent::validateRg($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um RG válido!']);
            }
            if ($type == 'cnpj') {
                $chek = parent::validateCnpj($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um CNPJ válido!']);
            }
            if ($type == 'cep') {
                $chek = parent::validateCep($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um CEP válido!']);
            }
            if (in_array($type, ['real', 'r$', 'money'])) {
                $chek = parent::validateReal($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um valor monetário do tipo ( REAL R$ ) válido!']);
            }
            if (in_array($type, ['cpf_cnpj', 'cnpj_cpf'])) {
                $chek = parent::validateCpfCnpj($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um valor ( CPF/CNPJ ) válido!']);
            }
            if (in_array($type, ['celular', 'cel'])) {
                $chek = parent::validateCelular($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Celular ) válido!']);
            }
            if (in_array($type, ['telefone', 'fone'])) {
                $chek = parent::validateTelefone($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Telefone ) válido!']);
            }
            if (in_array($type, ['fone_cel', 'cel_fone', 'telefone_celular', 'celular_telefone'])) {
                $chek = parent::validateTelefoneCelular($value);
                $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Telefone/Celular ) válido!']);
            }
            return $chek;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute  => 'Falha ao validar o campo :attribute para o padrão Brasil!']);
            return false;
        }
    }
}
