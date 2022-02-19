<?php

use App\Database\Migration;
use App\Database\Blueprint;

class DatabaseSession extends Migration
{
    public function up()
    {
        parent::schema('log')
            ->create('log_sessao', function (Blueprint $table) {
                $table->id('log');
                $table->bigInteger('log_usuario')->nullable()->comment('ID da tabela usuario.usuarios, identifica o usuário logado na sessão da conexão.');
                $table->timestamp('log_data')->useCurrent()->comment('Data de abertura da sessão da conexão.');
                $table->ipAddress('log_ip')->nullable()->comment('IP da do usuário logado, podendo ser o IP rela ou do provedor de rede do usuário logado.');
                $table->text('log_token', 1000)->nullable()->comment('Token usado para a comunicação com a API, que foi gerado pelo oauth2.');
                $table->json('log_localizacao')->nullable()->comment('Json contendo as possiveis localizações de ultimo acesso do usuário.');
            });

        parent::triggerSession(
            'trigger_log_session',
            'log.log_sessao',
            'AFTER',
            'INSERT OR UPDATE'
        );
    }

    public function down()
    {

        //drop trigger de log da sessao
        parent::triggerDrop(
            'trigger_log_session',
            'log.log_sessao'
        );

        //drop tabela log da sessao
        parent::schema('log')
            ->dropIfExists('log_sessao');
    }
}
