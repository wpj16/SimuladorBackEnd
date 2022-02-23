Projetos:

1° Clonar o projetos SimuladorBackEnd

2° Clonar o projetos SimuladorFrontEnd

3° Instalar o composer na maquina ou container que for rodar os projetos

4° Entrar na raiz de cada projeto e executar ( composer install )

6° Criar o banco  de dados Postgre

CREATE DATABASE TradeTechnology;

7°  Criar o Usuário do Banco

CREATE USER 'tradetechnology' WITH PASSWORD 'TradeTechnology';

8° Atribuir Permissões ao Usuário para Super usuario

ALTER USER tradetechnology WITH SUPERUSER;

9° no arquivo .env do projeto SimuladorBackEnd, adicionar as informações de conexão

10° Na raiz do projeto SimuladorBackEnd, executar os comandos

        php artisan schema:create

        php artisan migrate

        php artisan passport:client --password

        ##### após o comando ele irá pedir um nome para o client, pode ser qualquer um ou ( SimuladorFrontEnd )

        #### após a primeira pergunta, ele irá pedir um usuário, não precisa por nenhum, somente enter


11° Após o passo 10, ele gerará um ID e uma chame, copie e cole no aquivo .env do projeto ( SimuladorFrontEnd )

            API_WEBSERVICE_CLIENT_ID = "1"
            API_WEBSERVICE_CLIENT_SECRET = "C6UxbZ7VWmdtH7ptudN4wjGGXip8l9RQL7lCngcy"


12° No arquivo .env do projeto ( SimuladorFrontEnd ), adicionar a variavel, API_WEBSERVICE_URL com o endereço apontando para o endpoint do porjeto ( SimuladorFrontEnd )

            API_WEBSERVICE_URL = "https://api.simulador-de-jogos.com.br/api"

13° Acessar o projeto ( SimuladorFrontEnd ) via navegador e logar

       ## O Login e Senha ja vai estar fixo no html


14° O arquivo de Collection está na raiz do projeto ( SimuladorBackEnd )
