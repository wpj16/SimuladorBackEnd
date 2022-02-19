<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class TimeJogadoresTable extends Migration
{
    public function up()
    {
        parent::schema('time')
            ->create('jogadores', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do jogador.');
                $table->bigInteger('id_pessoa')
                    ->comment('ID da tabela pessoa.pessoas, para saber de qual pessoa é esse jogador!.');
                $table->foreign('id_pessoa')->references('id')->on('pessoa.pessoas');
                $table->string('nome')->length(150)
                    ->comment('Nome do jogador.');
                $table->string('numero')->length(3)
                    ->comment('Numero do jogador.');
                $table->timestamps(0, false);
            });

        parent::schema('log')
            ->create('log_time_jogadores', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do jogador.');
                $table->bigInteger('id_pessoa')
                    ->comment('ID da tabela pessoa.pessoas, para saber de qual pessoa é esse jogador!.');
                $table->string('nome')->length(150)
                    ->comment('Nome do jogador.');
                $table->string('numero')->length(3)
                    ->comment('Numero do jogador.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_time_jogadores',
            'time.jogadores'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_time_jogadores',
            'time.jogadores'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_time_jogadores');

        //drop tabela principla
        parent::schema('time')
            ->dropIfExists('jogadores');
    }
}
