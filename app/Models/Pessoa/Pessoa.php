<?php

namespace App\Models\Pessoa;

use App\Models\Model;

use App\Models\Usuario\Usuario;
use App\Models\Time\Jogador;

class Pessoa extends Model
{
    protected $table = 'pessoa.pessoas';
    protected $fillable = [
        'nome',
        'documento',
        'tipo',
        'data_nascimento',
        'nacionalidade',
        'email',
        'fone_fixo',
        'fone_movel',
        'situacao'
    ];

    public function usuario()
    {
        return $this->hasMany(Usuario::class, 'id_pessoa', 'id');
    }

    public function jogador()
    {
        return $this->hasOne(Jogador::class, 'id_pessoa', 'id');
    }
}
