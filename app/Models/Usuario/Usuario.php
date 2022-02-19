<?php

namespace App\Models\Usuario;

use App\Models\Pessoa\Pessoa;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Auth\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuario.usuarios';

    public function findForPassport($username)
    {
        return $this
            ->where('login', $username)
            ->first();
    }

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id_pessoa', 'id');
    }
}
