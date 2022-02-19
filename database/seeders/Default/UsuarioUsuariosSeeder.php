<?php

namespace Database\Seeders\Default;

use Illuminate\Database\Seeder;
use \App\Models\Usuario\Usuario;
use Illuminate\Support\Facades\Hash;
use \App\Models\Pessoa\Pessoa;

class UsuarioUsuariosSeeder extends Seeder
{

    public function run()
    {
        $usuario = new Usuario();
        $pessoa = new Pessoa();
        //Add  usuário para o sistema
        $usuario->insert([
            'id_pessoa' => 1,
            'login' => 'josegustavo@tradetechnology.com.br',
            'senha' => Hash::make('tradetechnology')
        ]);
        //atribui a primeira pessoa cadastrada ao usuário
        $pessoa->where('id_usuario_criacao', '=', null)
            ->update([
                'id_usuario_criacao' => 1
            ]);
    }
}
