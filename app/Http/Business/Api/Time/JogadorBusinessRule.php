<?php

namespace App\Http\Business\Api\Time;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Time\{
    Jogador,
    TimeJogador
};
use App\Models\Pessoa\Pessoa;

class JogadorBusinessRule extends MainBusinessRule
{

    private $modelPessoa;
    private $modelJogador;
    private $modelTimeJogador;

    public function __construct()
    {
        $this->modelPessoa = new Pessoa();
        $this->modelJogador = new Jogador();
        $this->modelTimeJogador = new TimeJogador();
    }

    public function cadastrarJogador(array $jogador): ResponseBusinessRule
    {
        $pessoa = $this->modelPessoa
            ->where('documento', $jogador['documento'])
            ->first()?->toArray();
        if (empty($pessoa)) {
            $id = $this->modelPessoa->insertGetId([
                'nome' => $jogador['nome'],
                'email' => $jogador['email'],
                'documento' => $jogador['documento'],
                'tipo' => $jogador['tipo'],
                'data_nascimento' => $jogador['data_nascimento']
            ]);
            $pessoa = $this->modelPessoa->find($id)?->toArray();
        }

        $id = $this->modelJogador->insertGetId([
            'nome' => $jogador['nome'],
            'id_pessoa' => $pessoa['id'],
            'nome' => $jogador['nome'],
            'numero' => $jogador['numero_camisa']
        ]);

        $this->modelTimeJogador->insert([
            'id_time' => $jogador['time'],
            'id_jogador' =>  $id
        ]);

        $dados = $this->modelJogador->with(['pessoa', 'times'])
            ->find($id)?->toArray();

        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Jogador cadastrado com sucesso!')
            ->setMessageError('Falha ao cadastrar jogador!');
    }

    public function listarJogadores()
    {
        $dados = $this->modelJogador->get()->toArray();
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Jogadores listados com sucesso!')
            ->setMessageError('Nenhum jogador encontrado!');
    }
}
