<?php

use App\Database\Migration;
use App\Database\Blueprint;

class PessoaPessoasTable extends Migration
{
    public function up()
    {
        parent::schema('pessoa')
            ->create('pessoas', function (Blueprint $table) {
                $table->id()
                    ->comment('ID da entidade pessoa.');
                $table->string('nome')->length(150)
                    ->comment('Nome da entidade pessoa.');
                $table->string('email')->length(200)->nullable()
                    ->comment('E-mail da entidade pessoa.');
                $table->string('documento')->unique()->length(20)
                    ->comment('Cpf ou Cnpj da entidade pessoa.');
                $table->string('documento_rg')->unique()->length(20)->nullable()
                    ->comment('RG da entidade ppessoa para pessoas fisícas.');
                $table->column('public.dm_tipo_pessoa', 'tipo')
                    ->comment('0 = Não definido, 1 = Fisica, 2 = Juridica, Tipo da entidade pessoa.');
                $table->column('public.dm_sexo', 'sexo')
                    ->comment('0 = Não definido, 1 = Masculino, 2 = Feminino, Tipo sexo entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->column('public.dm_estado_civil', 'estado_civil')
                    ->comment('0 = Não definido, 1 = Solteiro, 2 = Casado, 3 = Viúvo, 4 = Separado judicialmente, 5 = Divorciado, Tipo estado civil entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->column('public.dm_escolaridade', 'escolaridade')
                    ->comment('0 = Não definido,  1 = Ensino infantil, 2 = Ensino fundamental, 3 = Ensino médio, 4 = Ensino superior, 5 = Pós graduação, 6 = Mestrado, 7 = Doutorado, Escolaridade entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->timestamp('data_nascimento')
                    ->comment('Data de nascimento da entidade pessoa.');
                $table->string('nome_pai')->length(100)->nullable()
                    ->comment('Nome do pai da entidade pessoa em caso de pessoa fisíca.');
                $table->string('nome_mae')->length(100)->nullable()
                    ->comment('Nome da mãe da entidade pessoa em caso de pessoa fisíca.');
                $table->string('naturalidade')->length(50)->nullable()
                    ->comment('Naturalidade da entidade pessoa.');
                $table->string('nacionalidade')->default('brasileiro(a)')->length(20)->nullable()
                    ->comment('Nacionalidade da entidade pessoa.');
                $table->column('public.dm_fone', 'fone_fixo')->nullable()
                    ->comment('Telefone fixo entidade pessoa.');
                $table->column('public.dm_fone', 'fone_movel')->nullable()
                    ->comment('Telefone movel entidade pessoa.');
                $table->timestamps(0, false);
            });

        parent::schema('log')
            ->create('log_pessoa_pessoas', function (Blueprint $table) {
                //campos log
                $table->id('log')
                    ->comment('ID do log da alteração.');
                $table->timestamp('log_data')->useCurrent()
                    ->comment('Data da geração do log.');
                //campos tabela
                $table->bigInteger('id')
                    ->comment('ID da entidade pessoa.');
                $table->string('nome')->length(150)
                    ->comment('Nome da entidade pessoa.');
                $table->string('email')->length(200)->nullable()
                    ->comment('E-mail da entidade pessoa.');
                $table->string('documento')->length(20)
                    ->comment('Cpf ou Cnpj da entidade pessoa.');
                $table->string('documento_rg')->length(20)->nullable()
                    ->comment('RG da entidade pessoa para pessoas fisícas.');
                $table->column('public.dm_tipo_pessoa', 'tipo')
                    ->comment('0 = Não definido, 1 = Fisica, 2 = Juridica, Tipo da entidade pessoa.');
                $table->column('public.dm_sexo', 'sexo')
                    ->comment('0 = Não definido, 1 = Masculino, 2 = Feminino, Tipo sexo entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->column('public.dm_estado_civil', 'estado_civil')
                    ->comment('0 = Não definido, 1 = Solteiro, 2 = Casado, 3 = Viúvo, 4 = Separado judicialmente, 5 = Divorciado, Tipo estado civil entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->column('public.dm_escolaridade', 'escolaridade')
                    ->comment('0 = Não definido,  1 = Ensino infantil, 2 = Ensino fundamental, 3 = Ensino médio, 4 = Ensino superior, 5 = Pós graduação, 6 = Mestrado, 7 = Doutorado, Escolaridade entidade pessoa - ( 0 = Não definido para pessoa tipo jurídica ).');
                $table->timestamp('data_nascimento')->nullable()
                    ->comment('Data de nascimento da entidade pessoa.');
                $table->string('nome_pai')->length(100)->nullable()
                    ->comment('Nome do pai da entidade pessoa em caso de pessoa fisíca.');
                $table->string('nome_mae')->length(100)->nullable()
                    ->comment('Nome da mãe da entidade pessoa em caso de pessoa fisíca.');
                $table->string('naturalidade')->length(50)->nullable()
                    ->comment('Naturalidade da entidade pessoa.');
                $table->string('nacionalidade')->length(20)->nullable()
                    ->comment('Nacionalidade da entidade pessoa.');
                $table->column('public.dm_fone', 'fone_fixo')->nullable()
                    ->comment('Telefone fixo entidade pessoa.');
                $table->column('public.dm_fone', 'fone_movel')->nullable()
                    ->comment('Telefone movel entidade pessoa.');
                $table->timestampsnullable(0, false);
            });
        parent::triggerLog(
            'trigger_log_pessoa_pessoas',
            'pessoa.pessoas'
        );
    }

    public function down()
    {
        //drop trigger de log da tabela
        parent::triggerDrop(
            'trigger_log_pessoa_pessoas',
            'pessoa.pessoas'
        );
        //drop tabela de log da tabela principal
        parent::schema('log')
            ->dropIfExists('log_pessoa_pessoas');

        //drop tabela principla
        parent::schema('pessoa')
            ->dropIfExists('pessoas');
    }
}
