<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class TimeTimesJogadoresTable extends Migration
{
    public function up()
    {
        parent::schema('time')
            ->create('times_jogadores', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do relacionamento do time X jogador.');
                $table->bigInteger('id_time')
                    ->comment('ID do time.');
                $table->foreign('id_time')->references('id')->on('time.times');
                $table->bigInteger('id_jogador')
                    ->comment('ID do jogador do time.');
                $table->foreign('id_jogador')->references('id')->on('time.jogadores');
                $table->timestamps(0, false);
            });


        parent::schema('log')
            ->create('log_time_times_jogadores', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do relacionamento do time X jogador.');
                $table->bigInteger('id_time')
                    ->comment('ID do time.');
                $table->bigInteger('id_jogador')
                    ->comment('ID do jogador do time.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_time_times_jogadores',
            'time.times_jogadores'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_time_times_jogadores',
            'time.times_jogadores'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_time_times_jogadores');

        //drop tabela principla
        parent::schema('time')
            ->dropIfExists('times_jogadores');
    }
}
