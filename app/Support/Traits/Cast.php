<?php

namespace App\Support\Traits;

use Illuminate\Support\Carbon;

trait  Cast
{

    public function cast(array $data, array $cast = []): array
    {
        $columns = array_keys($cast);
        array_walk_recursive($data, function (&$value, $key) use ($columns, $cast) {
            if (empty(is_array($value)) && in_array($key, $columns) && !is_null($value)) {
                $type = $cast[$key];
                if (in_array($type, ['regex-numeric', 'regex-numerico'])) {
                    $value = $this->onlyNumbers($value);
                }
                if (in_array($type, ['numeric', 'numerico'])) {
                    $value = $this->toNumeric($value);
                }
                if (in_array($type, ['int', 'integer', 'inteiro'])) {
                    $value = $this->toNumericInt($value);
                }
                if (in_array($type, ['float', 'flutuante'])) {
                    $value = $this->toFloat($value);
                }
                if (in_array($type, ['float-string', 'string-float'])) {
                    $value = $this->toFloatString($value);
                }
                if (in_array($type, ['money', 'moeda', 'real', 'R$'])) {
                    $value = $this->toMoneyBrasil($value);
                }
                if (in_array($type, ['array', '[]'])) {
                    $value = $this->toArray($value);
                }
                if (in_array($type, ['bool', 'boolean', 'booleano'])) {
                    $value = $this->toBoolean($value);
                }
                if (in_array($type, ['date-br', 'data-br'])) {
                    $value = $this->toDate($value, 'd/m/Y');
                }
                if (in_array($type, ['timestamp-br', 'timestamp-br'])) {
                    $value = $this->toDate($value, 'd/m/Y H:m:s');
                }
            }
        });
        return $data;
    }

    public function onlyNumbers(string|null $value = null): string|null
    {
        if ((strlen($value) > 0) && ($newValue = preg_replace("/([^0-9])/", '', $value))) {
            return (string)$newValue;
        }
        return null;
    }

    public function toNumericInt(string|null $value = null): int|null
    {
        if ((strlen($value) > 0) && ($newValue = preg_replace("/([^(\-?\d+)])/", '', $value))) {
            return (int)$newValue;
        }
        return null;
    }

    public function toNumeric(string|null $value = null): string|null
    {
        if ((strlen($value) > 0) && ($newValue = preg_replace("/([^(\-?\d+)])/", '', $value))) {
            return $newValue;
        }
        return null;
    }

    public function toFloat($value = null, $decimal = null): float|null
    {
        $value = $this->toFloatString($value, $decimal);
        return $value ? floatval($value) : $value;
    }

    public function toBoolean(mixed $value = null): bool
    {
        if (is_array($value) || is_bool($value)) {
            return !empty($value);
        }
        return in_array(strtoupper($value), ['S', '1', 1, true]);
    }

    public function toArray(mixed $value = null): array|null
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

    public function toDate(string $data, $format = 'd/m/y'): string
    {
        return (new Carbon($data))->format($format);
    }

    public function toMoneyBrasil(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        $value = (string)$value;
        if ((strlen($value) > 0) && ($value = preg_replace("/[^0-9\.\,\-]/", '', $value))) {
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

    public function toFloatString(string|float|int|null $value = null, int $decimal = null): string|null
    {
        $value = (string)$value;
        if ((strlen($value) > 0) && ($value = preg_replace("/[^0-9\.\,\-]/", '', $value))) {
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
}
