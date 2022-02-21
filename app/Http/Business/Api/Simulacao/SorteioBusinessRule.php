<?php

namespace App\Http\Business\Api\Simulacao;

use  App\Http\Business\{
    MainBusinessRule,
    ResponseBusinessRule
};

use App\Models\Simulacao\Sorteio;
use App\Models\Campeonato\{
    Campeonato,
    CampeonatoTime
};

class SorteioBusinessRule extends MainBusinessRule
{

    const JOGO_FINAIS = 1;
    const JOGO_SEMI_FINAIS = 2;
    const JOGO_QUARTAS_DE_FINAIS = 3;

    private $modelSorteio;
    private $modelCampeonato;

    public function __construct()
    {
        $this->modelSorteio = new Sorteio();
        $this->modelCampeonato = new Campeonato();
        $this->modelCampeonatoTime = new CampeonatoTime();
    }


    public function simular(int $idCampeonato, int $etapa)
    {
        $jogos = $this->listarJogosCadastrados($idCampeonato, $etapa);
        $jogos->error(function () use ($idCampeonato, $etapa) {
            if ($etapa <> self::JOGO_QUARTAS_DE_FINAIS) {
                return parent::response()
                    ->setError(true)
                    ->setMessageError('Nenhum jogo encontroado para esta etapa!');
            }
            $campeonato = $this->modelCampeonato
                ->with('times')
                ->where('id', $idCampeonato)
                ->first()
                ->toArray();
            return $this->sorteio($campeonato['times'])
                ->success(function ($response) use ($campeonato, $etapa) {
                    $etapa = $etapa--;
                    $sorteio = $response->getData();
                    $sorteiosIds = [];
                    foreach ($sorteio as $jogo) {
                        $this->resultado($jogo)
                            ->success(function ($response) use ($campeonato, $etapa, &$sorteiosIds) {
                                $jogo = $response->getData();
                                $sorteiosIds[] = $this->modelSorteio->insertGetId([
                                    'id_campeonato' => $campeonato['id'],
                                    'id_time_a' =>  $jogo[0]['id'],
                                    'id_time_b' =>  $jogo[1]['id'],
                                    'gols_time_a' =>  $jogo[0]['gols'],
                                    'gols_time_b' =>  $jogo[1]['gols'],
                                    'etapa' => $etapa
                                ]);
                            });
                    }
                    return $this->simular($campeonato['id'], $etapa);
                });
        })->success(function ($response) use ($idCampeonato, $etapa) {

            $jogos = $response->getData();
            if ($etapa <= self::JOGO_FINAIS) {
                return parent::response()
                    ->setData($jogos)
                    ->setMessageSuccess('Partidas simuladas com sucesso!')
                    ->setMessageError('Nenhuma partida encontrada para esta etapa!');
            }

            $campeonato = $this->modelCampeonato
                ->with(['times' => function ($query) use ($jogos) {
                    $ganhadores = array_column($jogos, 'time_ganhador');
                    return $query->whereIn("times.id", $ganhadores);
                }])
                ->where('id', $idCampeonato)
                ->first()
                ->toArray();

            return $this->sorteio($campeonato['times'])
                ->success(function ($response) use ($campeonato, $etapa) {
                    $etapa = $etapa - 1;
                    $sorteio = $response->getData();
                    $sorteiosIds = [];
                    foreach ($sorteio as $jogo) {
                        $this->resultado($jogo)
                            ->success(function ($response) use ($campeonato, $etapa, &$sorteiosIds) {
                                $jogo = $response->getData();
                                $sorteiosIds[] = $this->modelSorteio->insertGetId([
                                    'id_campeonato' => $campeonato['id'],
                                    'id_time_a' =>  $jogo[0]['id'],
                                    'id_time_b' =>  $jogo[1]['id'],
                                    'gols_time_a' =>  $jogo[0]['gols'],
                                    'gols_time_b' =>  $jogo[1]['gols'],
                                    'etapa' => $etapa
                                ]);
                            });
                    }
                    return $this->simular($campeonato['id'], $etapa);
                });
        });
    }


    public function listarJogosCadastrados(int $campeonato, int|null $etapa = null): ResponseBusinessRule
    {

        $tabelaSorteio = $this->modelSorteio->getTable();
        $tabelaCampeonatoTime = $this->modelCampeonatoTime->getTable();
        $dados = $this->modelSorteio
            ->select('*')
            ->addSelect(['pontos_time_a' => function ($query) use ($tabelaSorteio) {
                return $query
                    ->selectRaw('(sum(gols_time_a) * c.pontos_por_gol)')
                    ->from("$tabelaSorteio as ss")
                    ->whereRaw("ss.id_time_a = $tabelaSorteio.id_time_a")
                    ->whereRaw("ss.id_campeonato = $tabelaSorteio.id_campeonato");
            }])
            ->addSelect(['pontos_time_b' => function ($query) use ($tabelaSorteio) {
                return $query
                    ->selectRaw('(sum(gols_time_b) * c.pontos_por_gol)')
                    ->from("$tabelaSorteio as ss")
                    ->whereRaw("ss.id_time_b = $tabelaSorteio.id_time_b")
                    ->whereRaw("ss.id_campeonato = $tabelaSorteio.id_campeonato");
            }])
            ->addSelect(['id_time_desempate_data' => function ($query) use ($tabelaCampeonatoTime, $tabelaSorteio) {
                return $query
                    ->select('id_time')
                    ->from("$tabelaCampeonatoTime as cc")
                    ->whereRaw("cc.id_time in($tabelaSorteio.id_time_a, $tabelaSorteio.id_time_b)")
                    ->orderBy('data_criacao', 'asc')
                    ->limit(1);
            }])
            ->join('campeonato.campeonatos as c', 'c.id', '=', "$tabelaSorteio.id_campeonato")
            ->where('id_campeonato', $campeonato)
            ->whereRaw("$tabelaSorteio.etapa = coalesce( ? , $tabelaSorteio.etapa)", [$etapa])
            ->get()->toArray();

        array_walk($dados, function (&$value) {
            switch (true) {
                case ($value['gols_time_a'] > $value['gols_time_b']):
                    $value['time_ganhador'] = $value['id_time_a'];
                    $value['time_perdedor'] = $value['id_time_b'];
                    break;
                case ($value['gols_time_a'] < $value['gols_time_b']):
                    $value['time_ganhador'] = $value['id_time_b'];
                    $value['time_perdedor'] = $value['id_time_a'];
                    break;
                case ($value['pontos_time_a'] > $value['pontos_time_b']):
                    $value['time_ganhador'] = $value['id_time_a'];
                    $value['time_perdedor'] = $value['id_time_b'];

                    break;
                case ($value['pontos_time_a'] < $value['pontos_time_b']):
                    $value['time_ganhador'] = $value['id_time_b'];
                    $value['time_perdedor'] = $value['id_time_a'];
                    break;
                case ($value['gols_time_a'] == $value['gols_time_b']) && ($value['pontos_time_a'] == $value['pontos_time_b']):
                    $value['time_ganhador'] = $value['id_time_desempate_data'];
                    $value['time_perdedor'] = ($value['id_time_desempate_data'] == $value['id_time_a']) ? $value['id_time_b'] : $value['id_time_a'];
                    break;
            }
        });
        return parent::response()
            ->setData($dados)
            ->setMessageSuccess('Partida(s) listada(s) com sucesso!')
            ->setMessageError('Nenhuma partida encontrada!');
    }


    public function sorteio(array $times = []): ResponseBusinessRule
    {
        if ((count($times) % 2) > 0) {
            return parent::response()
                ->setError(true)
                ->setMessageError('Não é possivel chavear uma quantidade impar de times!');
        }
        $partidas = [];
        while (count($times) > 0) {
            if (count($times) == 2) {
                $partidas[] = [array_shift($times), array_shift($times)];
                return parent::response()
                    ->setData($partidas)
                    ->setMessageSuccess('Partidas simuladas com sucesso!')
                    ->setMessageError('Falha ao simular partidas!');
            }
            $max = max(array_keys($times));
            $key = rand(0, $max);
            $keyB = rand(0, $max);
            if (isset($times[$key]) && isset($times[$keyB]) && ($key <> $keyB)) {
                $partidas[] = [$times[$key], $times[$keyB]];
                unset($times[$key]);
                unset($times[$keyB]);
            }
        }
        return parent::response()
            ->setData($partidas)
            ->setMessageSuccess('Partidas simuladas com sucesso!')
            ->setMessageError('Falha ao simular partidas!');
    }

    public function resultado(array $partidas = []): ResponseBusinessRule
    {

        $partidas[0]['gols'] = rand(1, 9);
        $partidas[1]['gols'] = rand(1, 9);

        return parent::response()
            ->setData($partidas)
            ->setMessageSuccess('Resultado da partida simulado com sucesso!')
            ->setMessageError('Falha ao simular resultado partida!');
    }
}