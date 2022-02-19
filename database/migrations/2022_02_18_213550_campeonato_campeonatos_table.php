<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class CampeonatoCampeonatosTable extends Migration
{
    public function up()
    {
        parent::schema('campeonato')
            ->create('campeonatos', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do Campeonato.');
                $table->string('nome')->length(150)
                    ->comment('Nome do Campeonato.');
                $table->timestamps(0, false);
                $table->integer('pontos_por_gol')
                    ->length(1)->default(1)
                    ->comment('Pontos por gol feito.');
            });


        parent::schema('log')
            ->create('log_campeonato_campeonatos', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do Campeonato.');
                $table->string('nome')->length(150)
                    ->comment('Nome do Campeonato.');
                $table->integer('pontos_por_gol')
                    ->length(1)->default(1)
                    ->comment('Pontos por gol feito.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_campeonato_campeonatos',
            'campeonato.campeonatos'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_campeonato_campeonatos',
            'campeonato.campeonatos'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_campeonato_campeonatos');

        //drop tabela principla
        parent::schema('campeonato')
            ->dropIfExists('campeonatos');
    }
}
