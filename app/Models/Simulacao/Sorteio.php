<?php

namespace App\Models\Simulacao;

use App\Models\Model;
use App\Models\Time\Time;
use App\Models\Campeonato\Campeonato;

class Sorteio extends Model
{
    protected $table = 'simulacao.sorteios';

    protected $fillable = [
        'id_time_a',
        'id_time_b',
        'gols_time_a',
        'gols_time_b',
        'id_campeonato',
        'etapa',
        'situacao'
    ];

    public function campeonato()
    {
        return $this->hasOne(Campeonato::class, 'id', 'id_campeonato');
    }

    public function timeA()
    {
        return $this->hasOne(Time::class, 'id', 'id_time_a');
    }

    public function timeB()
    {
        return $this->hasOne(Time::class, 'id', 'id_time_b');
    }
}
