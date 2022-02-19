<?php

use App\Database\Migration;
use Illuminate\Support\Facades\DB;

class DatabaseTypes extends Migration
{
    public function up()
    {
        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_situacao AS
                        INTEGER
                        NOT NULL
                        DEFAULT 1;

                        ALTER DOMAIN public.dm_situacao
                            ADD CONSTRAINT dm_situacao_chk
                            CHECK (VALUE = ANY (ARRAY[0, 1, 2]));

                        ALTER DOMAIN public.dm_situacao
                            OWNER TO postgres;

                        COMMENT ON DOMAIN public.dm_situacao
                            IS '
                            0 = Excluido
                            1 = Ativo
                            2 = Inativo';");

        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_tipo_pessoa AS
                        INTEGER
                        NOT NULL
                        DEFAULT 0;

                        ALTER DOMAIN public.dm_tipo_pessoa
                            ADD CONSTRAINT dm_tipo_pessoa_chk
                            CHECK (VALUE = ANY (ARRAY[0, 1, 2]));

                        ALTER DOMAIN public.dm_tipo_pessoa
                            OWNER TO postgres;

                        COMMENT ON DOMAIN public.dm_tipo_pessoa
                            IS
                           '0 = Não definido
                            1 = Física
                            2 = Jurídica';");

        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_sexo AS
                        INTEGER
                        NOT NULL
                        DEFAULT 0;

                        ALTER DOMAIN public.dm_sexo
                            ADD CONSTRAINT dm_sexo
                            CHECK (VALUE = ANY (ARRAY[0, 1, 2]));

                        ALTER DOMAIN public.dm_sexo
                            OWNER TO postgres;

                        COMMENT ON DOMAIN public.dm_sexo
                            IS
                           '0 = Não definido
                            1 = Masculino
                            2 = Feminino';");

        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_estado_civil AS
                INTEGER
                NOT NULL
                DEFAULT 0;

                ALTER DOMAIN public.dm_estado_civil
                    ADD CONSTRAINT dm_estado_civil
                    CHECK (VALUE = ANY (ARRAY[0, 1, 2, 3, 4, 5]));

                ALTER DOMAIN public.dm_estado_civil
                    OWNER TO postgres;

                COMMENT ON DOMAIN public.dm_estado_civil
                    IS
                   '0 = Não definido
                    1 = Solteiro
                    2 = Casado
                    3 = Viúvo
                    4 = Separado judicialmente
                    5 = Divorciado';");

        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_escolaridade AS
                INTEGER
                NOT NULL
                DEFAULT 0;

                ALTER DOMAIN public.dm_escolaridade
                    ADD CONSTRAINT dm_escolaridade
                    CHECK (VALUE = ANY (ARRAY[0, 1, 2, 3, 4, 5, 6, 7]));

                ALTER DOMAIN public.dm_escolaridade
                    OWNER TO postgres;

                COMMENT ON DOMAIN public.dm_escolaridade
                    IS
                   '0 = Não definido
                    1 = Ensino infantil
                    2 = Ensino fundamental
                    3 = Ensino médio
                    4 = Ensino superior
                    5 = Pós graduação
                    6 = Mestrado
                    7 = Doutorado';");


        //----------------------------------------------------------------------------------

        DB::unprepared("CREATE DOMAIN public.dm_campeonato_etapas AS
                INTEGER
                NOT NULL
                DEFAULT 0;

                ALTER DOMAIN public.dm_campeonato_etapas
                    ADD CONSTRAINT dm_campeonato_etapas
                    CHECK (VALUE = ANY (ARRAY[0, 1, 2, 3, 4]));

                ALTER DOMAIN public.dm_campeonato_etapas
                    OWNER TO postgres;

                COMMENT ON DOMAIN public.dm_campeonato_etapas
                    IS
                   '0 = Não definido
                    1 = Final
                    2 = Semifinal
                    3 = Quartas de final';");

        //----------------------------------------------------------------------------------


        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_fone CASCADE;");

        DB::unprepared("CREATE DOMAIN public.dm_fone AS
        VARCHAR(15)
        DEFAULT NULL;

        ALTER DOMAIN public.dm_fone
            ADD CONSTRAINT dm_fone
            CHECK (((VALUE)::text ~ '^[0-9]{10,15}$'::text) OR (VALUE IS NULL));

        ALTER DOMAIN public.dm_fone
            OWNER TO postgres;

        COMMENT ON DOMAIN public.dm_fone
            IS
           'Valida telefone com no mínimo 10 digitos, e no máximo 15 digitos';");

        //----------------------------------------------------------------------------------


    }

    public function down()
    {
        //drop tabela log da sessao
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_fone CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_campeonato_etapas CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_escolaridade CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_estado_civil CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_sexo CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_tipo_pessoa CASCADE;");
        DB::unprepared("DROP DOMAIN IF EXISTS public.dm_situacao CASCADE;");
    }
}
