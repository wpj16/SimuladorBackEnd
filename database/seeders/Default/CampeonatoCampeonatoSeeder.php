<?php

namespace Database\Seeders\Default;

use Illuminate\Database\Seeder;
use \App\Models\Campeonato\{
    Campeonato,
    CampeonatoTime
};
use \App\Models\Time\Time;
use \App\Http\Business\Api\Simulacao\SorteioBusinessRule;

class CampeonatoCampeonatoSeeder extends Seeder
{

    public function run()
    {
        $time = new Time();
        $campeonato = new Campeonato();
        $campeonatoTime = new CampeonatoTime();
        $simulacao =  new SorteioBusinessRule();
        $idCampeonato = $campeonato->insertGetId([
            'nome' => 'Primeiro Campeonato'
        ]);

        $time->insert([
            [
                'nome' => 'Time A',
            ],
            [
                'nome' => 'Time B',
            ],
            [
                'nome' => 'Time C',
            ],
            [
                'nome' => 'Time D',
            ],
            [
                'nome' => 'Time E',
            ],
            [
                'nome' => 'Time F',
            ],
            [
                'nome' => 'Time G',
            ],
            [
                'nome' => 'Time H',
            ]
        ]);

        foreach ($time->get()->toArray() as $time) {
            $campeonatoTime->insert(
                [
                    'id_campeonato' => $idCampeonato,
                    'id_time' => $time['id'],
                ]
            );
        }

        $simulacao->simular($idCampeonato, 3);
    }
}
