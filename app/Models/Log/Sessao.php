<?php

namespace App\Models\Log;

use App\Models\Model;

class Sessao extends Model
{
    const DELETE_AT = null;
    const USER_CREATED_AT = null;
    const USER_UPDATED_AT = null;

    protected $table = 'log.log_sessao';
    protected $primaryKey = 'log_sessao';
    public $timestamps = false;
    protected $casts = [
        'log_localizacao' => 'array'
    ];
    protected $fillable = [
        'log_ip',
        'log_usuario',
        'log_localizacao'
    ];

    public function setLogLocalizacaoAttribute($value)
    {
        $this->attributes['log_localizacao'] = $value;
    }
}
