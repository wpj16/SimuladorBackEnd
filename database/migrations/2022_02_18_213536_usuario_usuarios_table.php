<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class UsuarioUsuariosTable extends Migration
{
    public function up()
    {
        parent::schema('usuario')
            ->create('usuarios', function (Blueprint $table) {
                $table->id()
                    ->comment('ID usuários de logim no sistema!.');
                $table->bigInteger('id_pessoa')
                    ->comment('ID da tabela pessoa.pessoas, para saber de qual pessoa é esse usuário!.');
                $table->foreign('id_pessoa')->references('id')->on('pessoa.pessoas');
                $table->string('login')->length(50)
                    ->comment('Campo de login usado para o usuário autenticar no sistema.');
                $table->string('senha')->length(255)
                    ->comment('Campo de senha usado para o usuário autenticar no sistema.');
                $table->timestamps(0, false);
            });

        parent::schema('log')
            ->create('log_usuario_usuarios', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID usuários de logim no sistema!.');
                $table->bigInteger('id_pessoa')
                    ->comment('ID da tabela pessoa.pessoas, para saber de qual pessoa é esse usuário!.');
                $table->foreign('id_pessoa')->references('id')->on('pessoa.pessoas');
                $table->string('login')->length(50)
                    ->comment('Campo de login usado para o usuário autenticar no sistema.');
                $table->string('senha')->length(255)
                    ->comment('Campo de senha usado para o usuário autenticar no sistema.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_usuario_usuarios',
            'usuario.usuarios'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_usuario_usuarios',
            'usuario.usuarios'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_usuario_usuarios');

        //drop tabela principla
        parent::schema('usuario')
            ->dropIfExists('usuarios');
    }
}
