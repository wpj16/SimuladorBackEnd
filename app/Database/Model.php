<?php

namespace App\Database;

use App\Database\QueryBilder;
use App\Scopes\SituationScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as MainModel;

class Model extends MainModel
{
    use HasFactory;

    const DELETE_AT = 'situacao';
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_edicao';
    const USER_CREATED_AT = 'id_usuario_criacao';
    const USER_UPDATED_AT = 'id_usuario_edicao';

    protected $primaryKey = 'id';

    protected $casts = [
        'data_criacao' => 'date:d-m-Y H:m:s',
        'data_edicao' => 'date:d-m-Y H:m:s',
    ];

    protected $hidden = [
        'senha',
        'log_sessao'
    ];

    public $timestamps = true;

    public function getUserCreatedAtColumn()
    {
        return static::USER_CREATED_AT;
    }

    public function getDeleteAtColumn()
    {
        return static::DELETE_AT;
    }

    public function getDeleteAtValue()
    {
        return isset($this->{static::DELETE_AT}) ? $this->{static::DELETE_AT} = 0 : 0;
    }

    public function getUserUpdatedAtColumn()
    {
        return static::USER_UPDATED_AT;
    }

    public function newEloquentBuilder($query)
    {
        return new QueryBilder($query);
    }

    public function fireModelEvent($event, $halt = false)
    {
        return parent::fireModelEvent($event, $halt);
    }

    protected static function booted()
    {
        static::addGlobalScope(new SituationScope);
    }

    public function upload($file): File
    {
        return $this->file()
            ->setFileData($file)
            ->save();
    }

    public function dowload($file): File
    {
        return $this->file()
            ->load($file);
    }

    public function getNextId()
    {
        $table = $this->getTable();
        $column = $this->getKeyName();
        $getNameSerialId = "pg_get_serial_sequence('$table', '$column')";
        $object = $this->selectRaw("(setval($getNameSerialId,(nextval($getNameSerialId)-1))+1) as $column")->first();
        return $object?->$column ?: null;
    }

    public function file(): File
    {
        return (new File());
    }

    public function getUserCreatedAtValue()
    {
        $default = DB::raw("(sistema.sessao('sessao_usuario'::text))::bigint");
        return $this->{static::USER_CREATED_AT} ?? $default;
    }

    public function getUserUpdatedAtValue()
    {
        $default = DB::raw("(sistema.sessao('sessao_usuario'::text))::bigint");
        return $this->{static::USER_UPDATED_AT} ?? $default;
    }

    public function updateTimestamps()
    {
        $time = $this->freshTimestamp();
        $updatedAtColumn = $this->getUpdatedAtColumn();
        if (!empty($this->exists) && !is_null($updatedAtColumn) && !$this->isDirty($updatedAtColumn)) {
            $this->setUpdatedAt($time);
        }
        $createdAtColumn = $this->getCreatedAtColumn();
        if (empty($this->exists) && !is_null($createdAtColumn) && !$this->isDirty($createdAtColumn)) {
            $this->setUpdatedAt(null);
            $this->setCreatedAt($time);
        }
    }
}
