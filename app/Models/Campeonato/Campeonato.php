<?php

namespace App\Models\Campeonato;

use App\Models\Model;

class Campeonato extends Model
{
    protected $table = 'campeonato.campeonato';
    protected $fillable = [
        'nome',
        'situacao'
    ];
}
