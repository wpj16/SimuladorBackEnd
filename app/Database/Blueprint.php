<?php

namespace App\Database;

use Illuminate\Database\Schema\Blueprint as MainBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;

class Blueprint extends MainBlueprint
{

    /**
     * The prefix of the table columns.
     *
     * @var string
     */
    protected $prefixColumn;
    protected $prefixColumns;
    protected $ignorePrefixColumns = [];

    public function column($type, $column, $length = null, array $allowed = [])
    {
        $length = $length ?: Builder::$defaultStringLength;
        return $this->addColumn($type, $column, compact('allowed'))->length($length);
    }

    public function foreign($columns, $name = null)
    {
        $columns = $this->addPrefixColumn($columns);
        $command = new ForeignKeyDefinition(
            $this->indexCommand('foreign', $columns, $name)->getAttributes()
        );
        $this->commands[count($this->commands) - 1] = $command;

        return $command;
    }

    public function addColumn($type, $name, array $parameters = [])
    {
        $name = $this->addPrefixColumn($name);
        return $this->addColumnDefinition(new ColumnDefinition(
            array_merge(compact('type', 'name'), $parameters)
        ));
    }

    public function prefixColumnsIgnore($columns = [])
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $this->ignorePrefixColumns = $columns;
        return true;
    }

    public function prefixColumn($prefix)
    {
        $this->prefixColumn = $prefix;
        return $this;
    }

    public function prefixColumns($prefix)
    {
        if (empty($this->prefixColumns)) {
            $this->prefixColumns = $prefix;
        }
        return true;
    }

    protected function createIndexName($type, array $columns)
    {
        $index = strtolower(trim($type) . '_' . trim($this->prefix) . trim($this->table) . '_' . trim(implode('_', $columns)));
        return str_replace(['-', '.'], '_', $index);
    }

    public function index($columns, $name = null, $algorithm = null)
    {
        $teste = $this->indexCommand('index', $columns, $name, $algorithm);
        return $teste;
    }

    protected function indexCommand($type, $columns, $index, $algorithm = null)
    {
        $columns = (array) $columns;
        $index = $index ?: $this->createIndexName($type, $columns);
        return $this->addCommand(
            $type,
            compact('index', 'columns', 'algorithm')
        );
    }

    public function timestamps($precision = 0, $foreign = true)
    {
        $this->column('public.dm_situacao', 'situacao')->default(1)
            ->comment('0 = Excluido, 1 = Ativo, 2 = Inativo, Situação atual do registro ( Linha na tabela ).');
        parent::timestamp('data_criacao', $precision)->useCurrent()
            ->comment('Data de criação do registro ( Linha na tabela ), timestemp em tempo real.');
        parent::timestamp('data_edicao', $precision)->nullable()
            ->comment('Data da "ULTIMA" edição do registro ( Linha na tabela ), timestemp em tempo real.');
        parent::bigInteger('id_usuario_criacao')->nullable()
            ->default(DB::raw("(sistema.sessao('sessao_usuario'::text)::bigint)"))
            ->comment('ID do usuário da tabela usuario.usuarios, responsavel pela criação do registro ( Linha na tabela ), usuário da sessão.');
        parent::bigInteger('id_usuario_edicao')->nullable()
            ->comment('ID do usuário da tabela usuario.usuarios, responsavel pela "ULTIMA" alteração do registro ( Linha na tabela ), usuário da sessão.');
        parent::bigInteger('log_sessao')->nullable()
            ->default(DB::raw("(sistema.sessao('sessao_id'::text)::bigint)"))
            ->comment('ID do log de sessão log.log_sessao, responsavel por identificar a sessão usada para cadastro/alteração do registro.');

        if ($foreign) {
            parent::foreign('id_usuario_criacao')->references('id')->on('usuario.usuarios');
            parent::foreign('id_usuario_edicao')->references('id')->on('usuario.usuarios');
            parent::foreign('log_sessao')->references('log')->on('log.log_sessao');
        }
    }

    public function timestampsnullable($precision = 0)
    {
        $this->column('public.dm_situacao', 'situacao')->default(1)
            ->comment('0 = Excluido, 1 = Ativo, 2 Inativo, Situação atual do registro ( Linha na tabela ).');
        parent::timestamp('data_criacao', $precision)->useCurrent()
            ->comment('Data de criação do registro ( Linha na tabela ), timestemp em tempo real.');
        parent::timestamp('data_edicao', $precision)->nullable()
            ->comment('Data da "ULTIMA" edição do registro ( Linha na tabela ), timestemp em tempo real.');
        parent::bigInteger('id_usuario_criacao')->nullable()
            ->default(DB::raw("(sistema.sessao('sessao_usuario'::text)::bigint)"))
            ->comment('id do usuário da tabela usuario.usuarios, responsavel pela criação do registro ( Linha na tabela ), usuário da sessão.');
        parent::bigInteger('id_usuario_edicao')->nullable()
            ->comment('id do usuário da tabela usuario.usuarios, responsavel pela "ULTIMA" alteração do registro ( Linha na tabela ), usuário da sessão.');
        parent::bigInteger('log_sessao')->nullable()
            ->default(DB::raw("(sistema.sessao('sessao_id'::text)::bigint)"))
            ->comment('ID do log de sessão log.log_sessao, responsavel por identificar a sessão usada para cadastro/alteração do registro.');
    }

    private function addPrefixColumn($column)
    {
        $addPrefix = (!empty($this->prefixColumn));
        if ($addPrefix) {
            if (is_array($column)) {
                foreach ($column as $key => $col) {
                    $noPrefixo = (strpos(trim(strtolower($col)), trim(strtolower($this->prefixColumn))) !== 0);
                    $noIgnore = !in_array($col, $this->ignorePrefixColumns);
                    if ($noPrefixo && $noIgnore) {
                        $column[$key] = trim($this->prefixColumn) . trim($col);
                    }
                }
            } else {
                $noPrefixo = (strpos(trim(strtolower($column)), trim(strtolower($this->prefixColumn))) !== 0);
                $noIgnore = !in_array($column, $this->ignorePrefixColumns);
                if ($noPrefixo && $noIgnore) {
                    $column = trim($this->prefixColumn) . trim($column);
                }
            }
        } else {
            $addPrefix = (!empty($this->prefixColumns));
            if ($addPrefix) {
                if (is_array($column)) {
                    foreach ($column as $key => $col) {
                        $noPrefixo = (strpos(trim(strtolower($col)), trim(strtolower($this->prefixColumns))) !== 0);
                        $noIgnore = !in_array($col, $this->ignorePrefixColumns);
                        if ($noPrefixo && $noIgnore) {
                            $column[$key] = trim($this->prefixColumns) . trim($col);
                        }
                    }
                } else {
                    $noPrefixo = (strpos(trim(strtolower($column)), trim(strtolower($this->prefixColumns))) !== 0);
                    $noIgnore = !in_array($column, $this->ignorePrefixColumns);
                    if ($noPrefixo && $noIgnore) {
                        $column = trim($this->prefixColumns) . trim($column);
                    }
                }
            }
        }
        $this->prefixColumn = null;
        return $column;
    }
}
