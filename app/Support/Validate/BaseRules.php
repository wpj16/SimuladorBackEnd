<?php

namespace App\Support\Validate;

class BaseRules
{
    protected function setValueIndex(string|int $index, array $data, mixed $newValue = null): array
    {
        $index = explode('.', $index);
        $first = array_shift($index);
        if (isset($data[$first]) && is_array($data[$first]) && !empty($index)) {
            $index = implode('.', $index);
            $data[$first] = $this->setValueIndex($index, $data[$first], $newValue);
            return $data;
        }
        $data[$first] = $newValue;
        return $data;
    }

    protected function onlyNumbers(string|null $value = null): string|null
    {
        $value = preg_replace("/([^0-9])/", '', (string)$value);
        return (strlen($value) > 0) ? ((string)$value) : null;
    }

    protected function toNumericInt(string|null $value = null): int|null
    {
        $value = preg_replace("/([^(\-?\d+)])/", '', (string)$value);
        return (strlen($value) > 0) ? ((int) $value) : null;
    }

    protected function toNumeric(string|null $value = null): string|null
    {
        $value = preg_replace("/([^(\-?\d+)])/", '', (string)$value);
        return (strlen($value) > 0) ? $value : null;
    }

    protected function toFloat($value = null, $decimal = null): float|null
    {
        $value = $this->toFloatString($value, $decimal);
        return $value ? floatval($value) : $value;
    }

    protected function toBoolean(mixed $value = null): bool
    {
        if (is_array($value) || is_bool($value)) {
            return !empty($value);
        }
        return in_array(strtoupper($value), ['S', '1', 1, true]);
    }

    protected function validateCpfCnpj(string $cpfCnpj): bool
    {
        return ($this->validateCpf($cpfCnpj) || $this->validateCnpj($cpfCnpj));
    }

    protected function validateTelefoneCelular(string $foneCel): bool
    {
        return ($this->validateTelefone($foneCel) || $this->validateCelular($foneCel));
    }

    protected function validateReal(string $real): bool
    {
        return preg_match("/^-?((\d+\.\d{3,3}\,\d{2,2})|\d{1,3}\,\d{2,2})$/", $real);
    }

    protected function validateCep(string $cep): bool
    {
        return preg_match('/^[0-9]{5,5}([-\s]?[0-9]{3,3})?$/', $cep);
    }

    protected function validateTelefone(string $fone): bool
    {
        return preg_match("/^\(?\d{2}\)?\s?\d{4}\-?\d{4}$/", $fone);
    }

    protected function validateCelular(string $celular): bool
    {
        return preg_match("/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/", $celular);
    }

    protected function validateRg(string $rg): bool
    {
        return preg_match('/^(\d{2})\.?(\d{3})\.?(\d{3})\-?(\d{1})$/', $rg);
    }

    protected function toArray(mixed $value = null): array|null
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
            if ((json_last_error() == JSON_ERROR_NONE) && is_array($value)) {
                return  $value;
            }
        } elseif (is_array($value)) {
            return  $value;
        }
        return null;
    }

    protected function toMoneyBrasil(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        $value = preg_replace("/[^0-9\.\,\-]/", '', (string)$value);
        if (strlen($value) > 0) {
            if (preg_match("/^-?((\d+\.\d{3,3}\,\d{1,2})|(\d+\.\d+)|(\d+\,\d+)|(\d+))$/", $value, $matches)) {
                $value = array_shift($matches);
                if (preg_match("/^-?(\d+\.\d{3,3}\,\d{1,2})$/", $value)) {
                    $value = str_replace(',', '.', str_replace('.', '', $value));
                    return number_format($value, $decimal, ",", ".");
                }
                $value = str_replace(',', '.', $value);
                $point = strpos(strrev($value), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return number_format($value, $decimal, ",", ".");
            }
        }
        return null;
    }

    protected function toFloatMoneyBrasil(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        $value = preg_replace("/[^0-9\.\,\-]/", '', (string)$value);
        if (strlen($value) > 0) {
            return $this->toFloat($value, $decimal);
        }
        return null;
    }

    protected function toFloatString(string|float|int|null $value = null, int $decimal = null): string|null
    {
        $value = preg_replace("/[^0-9\.\,\-]/", '', (string)$value);
        if (strlen($value) > 0) {
            if (preg_match("/^-?((\d+\.\d{3,3}\,\d{1,2})|(\d+\.\d+)|(\d+\,\d+)|(\d+))$/", $value, $matches)) {
                $value = array_shift($matches);
                if (preg_match("/^-?(\d+\.\d{3,3}\,\d{1,2})$/", $value)) {
                    $value = str_replace(',', '.', str_replace('.', '', $value));
                    $point = strpos(strrev($value), '.') ?: 0;
                    $decimal = $decimal ?: $point;
                    return number_format($value, $decimal, '.', '');
                }
                $value = str_replace(',', '.', $value);
                $point = strpos(strrev($value), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return number_format($value, $decimal, '.', '');
            }
        }
        return null;
    }


    protected function validateCpf(string $cpf): bool
    {
        try {
            // Extrair somente os números
            $cpf = preg_replace('/[^0-9]/is', '', $cpf);
            // Verifica se foi informado todos os digitos corretamente
            if (strlen($cpf) != 11) {
                return false;
            }
            // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            }
            // Faz o calculo para validar o CPF
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function validateCnpj(string $cnpj): bool
    {
        try {
            // Verifica se um número foi informado
            if (empty($cnpj)) {
                return false;
            }
            // Elimina possivel mascara
            $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
            $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
            // Verifica se o numero de digitos informados é igual a 11
            if (strlen($cnpj) != 14) {
                return false;
            }
            // Verifica se foi informada uma sequência de digitos repetidos. Ex: 00000000000000
            if (preg_match('/(\d)\1{14}/', $cnpj)) {
                return false;
            }
            $j = 5;
            $k = 6;
            $soma1 = "";
            $soma2 = "";
            for ($i = 0; $i < 13; $i++) {
                $j = $j == 1 ? 9 : $j;
                $k = $k == 1 ? 9 : $k;
                $soma2 += ($cnpj[$i] * $k);
                if ($i < 12) {
                    $soma1 += ($cnpj[$i] * $j);
                }
                $k--;
                $j--;
            }
            $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
            $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
            return (($cnpj == $digito1) && ($cnpj == $digito2));
        } catch (\Throwable $e) {
            return false;
        }
    }
}
