<?php

use App\Database\Migration;
use App\Database\Blueprint;

class SimulacaoSorteiosTable extends Migration
{
    public function up()
    {
        parent::schema('simulacao')
            ->create('sorteios', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do time.');
                $table->bigInteger('id_campeonato');
                $table->foreign('id_campeonato')->references('id')->on('campeonato.campeonatos');
                $table->bigInteger('id_time_a')
                    ->comment('ID do primeiro time do sorteio');
                $table->foreign('id_time_a')->references('id')->on('time.times');
                $table->bigInteger('id_time_b')
                    ->comment('ID do segundo time do sorteio');
                $table->foreign('id_time_b')->references('id')->on('time.times');
                $table->integer('gols_time_a')->default(0)
                    ->comment('Total de gols feito pelo time A.');
                $table->integer('gols_time_b')->default(0)
                    ->comment('Total de gols feito pelo time B.');
                $table->column('public.dm_campeonato_etapas', 'etapa')->nullable()
                    ->comment('0 = Não definido, 1 = Final, 2 = Semifinal, 3 = Quartas de final.');
                $table->timestamps(0, false);
            });


        parent::schema('log')
            ->create('log_simulacao_sorteios', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do time.');
                $table->bigInteger('id_campeonato');
                $table->bigInteger('id_time_a')->nullable()
                    ->comment('ID do primeiro time do sorteio');
                $table->bigInteger('id_time_b')->nullable()
                    ->comment('ID do segundo time do sorteio');
                $table->integer('gols_time_a')->default(0)
                    ->comment('Total de gols feito pelo time A.');
                $table->integer('gols_time_b')->default(0)
                    ->comment('Total de gols feito pelo time B.');
                $table->column('public.dm_campeonato_etapas', 'etapa')->nullable()
                    ->comment('0 = Jogo terceiro lugar, 1 = Final, 2 = Semifinal, 3 = Quartas de final.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_simulacao_sorteios',
            'simulacao.sorteios'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_simulacao_sorteios',
            'simulacao.sorteios'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_simulacao_sorteios');

        //drop tabela principla
        parent::schema('simulacao')
            ->dropIfExists('sorteios');
    }
}
