<?php

namespace App\Models\Time;

use App\Models\Model;
use App\Models\Time\{
    Time,
    Jogador
};


class TimeJogador extends Model
{
    protected $table = 'time.times_jogadores';

    protected $fillable = [
        'id_time',
        'id_jogador',
        'situacao'
    ];

    public function times()
    {
        return $this->belongsTo(Time::class, 'id', 'id_time');
    }

    public function jogadores()
    {
        return $this->belongsTo(Time::class, 'id', 'id_jogador');
    }
}
