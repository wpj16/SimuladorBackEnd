<?php

use App\Database\Migration;
use App\Database\Blueprint;
use Illuminate\Support\Facades\DB;

class TimeTimesTable extends Migration
{
    public function up()
    {
        parent::schema('time')
            ->create('times', function (Blueprint $table) {
                $table->id()
                    ->comment('ID do time.');
                $table->string('nome')->length(150)
                    ->comment('Nome do time.');
                $table->timestamps(0, false);
            });


        parent::schema('log')
            ->create('log_time_times', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID do time.');
                $table->string('nome')->length(150)
                    ->comment('Nome do time.');
                $table->timestampsnullable(0, false);
            });

        parent::triggerLog(
            'trigger_log_time_times',
            'time.times'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_time_times',
            'time.times'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_time_times');

        //drop tabela principla
        parent::schema('time')
            ->dropIfExists('times');
    }
}
