<?php

namespace App\Models\Time;

use App\Models\Model;
use App\Models\Time\Jogador;

class Time extends Model
{
    protected $table = 'time.times';

    protected $fillable = [
        'nome',
        'situacao'
    ];

    public function jogadores()
    {
        return $this->belongsToMany(Jogador::class, 'times_jogadores');
    }
}
