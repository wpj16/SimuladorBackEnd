<?php

use App\Database\Migration;
use Illuminate\Support\Facades\DB;


class DatabaseFunctions extends Migration
{
    public function up()
    {
        //FUNCÃO PARA ADD DADOS DE SESSÃO PHP NA SESSÃO DO BANCO
        DB::unprepared("
                    CREATE OR REPLACE FUNCTION SISTEMA.SESSAO(KEY TEXT, VAL TEXT DEFAULT NULL)
                    RETURNS TEXT AS $$
                    BEGIN
                        /*******
                         * SETA E BUSCAR DADOS NA SESSAO
                         * *********/
                        IF ((LENGTH(VAL) > 0) AND (CURRENT_SETTING('SESSION.'||UPPER(KEY), 't') IS NULL)) THEN
                        EXECUTE 'SET SESSION.'||UPPER(KEY)||' = '''||VAL||'''';
                        END IF;
                        RETURN CURRENT_SETTING('SESSION.'||UPPER(KEY), 't')::text;
                    END
                    $$ LANGUAGE plpgsql;");


        //FUNÇÃO PARA SETAR ID DO USUARIO NA SESSAO DO BANCO APÓS REGISTRAR INFROMAÇÕES DE LOG
        DB::unprepared("
                    CREATE OR REPLACE FUNCTION LOG.FUNC_TRIGGER_SESSAO()
                    RETURNS TRIGGER AS $$
                    DECLARE
                    BEGIN
                        /*******
                         * SETA DADOS NA SESSAO DO BANCO AO REGISTRAR UM LOG DE SESSAO
                         * *********/
                        PERFORM (SISTEMA.SESSAO('sessao_id', NEW.log::text));
                        PERFORM (SISTEMA.SESSAO('sessao_usuario', NEW.log_usuario::text));
                        RETURN NEW;
                    END
                    $$ LANGUAGE plpgsql;");

        //FUNÇÃO PARA TRIGGERS DE LOGS DAS TABELAS, VINCULANDO LOG DE INFORMAÇÕES
        DB::unprepared("
                    CREATE OR REPLACE FUNCTION LOG.FUNC_TRIGGER_LOGS()
                    RETURNS trigger AS $$
                    DECLARE
                    TABLELOG   TEXT;
                    COLUMNSKEY TEXT;
                    COLUMNSVAL TEXT;
                    BEGIN
                            /*******
                             * TABELA A SER GRAVADA O LOG
                             * *********/
                            TABLELOG = TG_ARGV[0];

                            /*******
                             *  COLUNAS DA TABELA APLICADA A TRIGGER PARA O LOG
                             * *********/
                            SELECT
                            (STRING_AGG(COLUMN_NAME, ',')) AS COLUMNSKEY,
                            ('$1.'||STRING_AGG(COLUMN_NAME, ', $1.')) AS COLUMNSVAL
                            INTO
                            COLUMNSKEY,
                            COLUMNSVAL
                            FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE
                            UPPER(TRIM(TABLE_SCHEMA)) = UPPER(TRIM(TG_TABLE_SCHEMA))
                            AND
                            UPPER(TRIM(TABLE_NAME)) = UPPER(TRIM(TG_TABLE_NAME));

                            /******
                             *  EXECUTA INSERT DOS VALORES NA TABELA DE LOG
                             *  QUE DEVE CONTER MESMAS COLUNAS QUE A PRINCIPAL
                             * *********/
                            IF TG_OP = 'DELETE' THEN

                                    EXECUTE (
                                        'INSERT INTO '
                                        ||UPPER(TRIM(TABLELOG))||
                                        '('
                                        ||UPPER(TRIM(COLUMNSKEY))||
                                        ')VALUES('
                                        ||UPPER(TRIM(COLUMNSVAL))||
                                        ')'
                                    ) USING OLD;

                                    RETURN OLD;

                            ELSIF TG_OP = 'UPDATE' THEN

                                    EXECUTE (
                                        'INSERT INTO '
                                        ||UPPER(TRIM(TABLELOG))||
                                        '('
                                        ||UPPER(TRIM(COLUMNSKEY))||
                                        ')VALUES('
                                        ||UPPER(TRIM(COLUMNSVAL))||
                                        ')'
                                    ) USING OLD;

                                    RETURN NEW;

                            END IF;

                            /*******
                             * FIM
                             * *********/
                    END
                    $$ LANGUAGE plpgsql;");
    }

    public function down()
    {
        DB::unprepared("DROP FUNCTION IF EXISTS SISTEMA.SESSION CASCADE;");
        DB::unprepared("DROP FUNCTION IF EXISTS SISTEMA.FUNC_TRIGGER_SETAR_SESSAO CASCADE;");
        DB::unprepared("DROP FUNCTION IF EXISTS LOG.FUNC_TRIGGER_LOGS CASCADE;");
    }
}
