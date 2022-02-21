<?php

namespace App\Models\Campeonato;

use App\Models\Model;
use App\Models\Time\Time;

class Campeonato extends Model
{
    protected $table = 'campeonato.campeonatos';
    protected $fillable = [
        'nome',
        'situacao'
    ];

    public function times()
    {
        return $this->belongsToMany(Time::class, 'campeonato.campeonatos_times', 'id_campeonato', 'id_time')
            ->as('relacionamento');
    }
}
