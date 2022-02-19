<?php

namespace App\Models\Time;

use App\Models\Model;
use App\Models\Pessoa\Pessoa;
use App\Models\Time\Time;

class Jogador extends Model
{
    protected $table = 'time.jogadores';

    protected $fillable = [
        'id_pessoa',
        'nome',
        'numero',
        'situacao'
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id');
    }

    public function times()
    {
        return $this->belongsToMany(Time::class, 'times_jogadores');
    }
}
