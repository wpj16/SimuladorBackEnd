<?php

namespace Database\Seeders\Default;

use Illuminate\Database\Seeder;
use \App\Models\Pessoa\Pessoa;

class PessoaPessoasSeeder extends Seeder
{
    public function run()
    {
        $pessoa = new Pessoa();
        $pessoa->insert(
            [
                [
                    'nome' => 'José Gustavo',
                    'email' => 'josegustavo@tradetechnology.com.br',
                    'documento' => rand(00000000000001, 99999999999999),
                    'tipo' => 1,
                    'data_nascimento' => date('d-m-Y')
                ]
            ]
        );
    }
}
