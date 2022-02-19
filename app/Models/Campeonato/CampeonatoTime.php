<?php

namespace App\Models\Campeonato;

use App\Models\Model;
use App\Models\Pessoa\Pessoa;

class CampeonatoTime extends Model
{
    protected $table = 'campeonato.campeonatos_times';
    protected $fillable = [
        'id_campeonato',
        'id_time',
        'situacao'
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id');
    }
}
