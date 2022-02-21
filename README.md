CREATE DATABASE TradeTechnology;

CREATE USER 'tradetechnology' WITH PASSWORD 'TradeTechnology';

ALTER USER tradetechnology WITH SUPERUSER;

------------------------------------------------------------

php artisan schema:create


php artisan migrate


php artisan passport:client --password

What should we name the password grant client?

simulador-de-jogos.com.br







        /**
         *
         * select
         *  (CASE
         *      WHEN (gols_time_a > gols_time_b) THEN
         *      id_time_a
         *      WHEN (gols_time_a = gols_time_b) and (pontos_time_a > gols_time_b) THEN
         *      id_time_a
         *      WHEN (gols_time_a = gols_time_b) and (pontos_time_a = gols_time_b) THEN
         *      id_time_desempate_data
         *      ELSE
         *      id_time_b
         * END) as ganhador,
         * temp.*
         * from (
         *      select
         *      ((select sum(gols_time_a) from simulacao.sorteios ss where ss.id_time_a = s.id_time_a and ss.id_campeonato = s.id_campeonato ) * c.pontos_por_gol) as pontos_time_a,
         *      ((select sum(gols_time_b) from simulacao.sorteios ss where ss.id_time_b = s.id_time_b and ss.id_campeonato = s.id_campeonato ) * c.pontos_por_gol) as pontos_time_b,
         *      (select id from campeonato.campeonatos_times where id in(id_time_a,id_time_b) order by data_criacao asc limit 1) as id_time_desempate_data,
         *       s.*
         *        from simulacao.sorteios s
         *       inner join campeonato.campeonatos c on c.id = s.id_campeonato
         *   )temp
         */
