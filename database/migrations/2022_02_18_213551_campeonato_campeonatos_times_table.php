<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class CampeonatoCampeonatosTimesTable extends Migration
{
    public function up()
    {
        parent::schema('campeonato')
            ->create('campeonatos_times', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do relacionamento campeonato X time.');
                $table->bigInteger('id_campeonato')->comment('ID do campeonato');
                $table->foreign('id_campeonato')->references('id')->on('campeonato.campeonatos');
                $table->bigInteger('id_time')->comment('ID do time participante do campeonato');
                $table->foreign('id_time')->references('id')->on('time.times');
                $table->timestamps(0, false);
            });

        parent::schema('log')
            ->create('log_campeonato_campeonatos_times', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do relacionamento campeonato X time.');
                $table->bigInteger('id_campeonato')->comment('ID do campeonato');
                $table->bigInteger('id_time')->comment('ID do time participante do campeonato');
                $table->foreign('id_time')->references('id')->on('time.times');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_campeonato_campeonatos_times',
            'campeonato.campeonatos_times'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_campeonato_campeonatos_times',
            'campeonato.campeonatos_times'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_campeonato_campeonatos_times');

        //drop tabela principla
        parent::schema('campeonato')
            ->dropIfExists('campeonatos_times');
    }
}
