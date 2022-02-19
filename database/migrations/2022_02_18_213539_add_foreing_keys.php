<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class AddForeingKeys extends Migration
{
    public function up()
    {
        parent::schema('pessoa')
            ->table('pessoas', function (Blueprint $table) {
                $table->foreign('id_usuario_criacao')->references('id')->on('usuario.usuarios');
                $table->foreign('id_usuario_edicao')->references('id')->on('usuario.usuarios');
                $table->foreign('log_sessao')->references('log')->on('log.log_sessao');
            });

        parent::schema('usuario')
            ->table('usuarios', function (Blueprint $table) {
                $table->foreign('id_usuario_criacao')->references('id')->on('usuario.usuarios');
                $table->foreign('id_usuario_edicao')->references('id')->on('usuario.usuarios');
                $table->foreign('log_sessao')->references('log')->on('log.log_sessao');
            });
    }

    public function down()
    {
        parent::schema('pessoa')
            ->table('pessoas', function (Blueprint $table) {
                $table->dropForeign(['id_usuario_criacao']);
                $table->dropForeign(['id_usuario_edicao']);
                $table->dropForeign(['log_sessao']);
            });

        parent::schema('usuario')
            ->table('usuarios', function (Blueprint $table) {
                $table->dropForeign(['id_usuario_criacao']);
                $table->dropForeign(['id_usuario_edicao']);
                $table->dropForeign(['log_sessao']);
            });
    }
}
