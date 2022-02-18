<?php

namespace App\Database;

use Illuminate\Database\Migrations\Migration as MainMigration;
use App\Database\{
    Blueprint,
    ColumnType
};
use Illuminate\Support\Facades\DB;

class Migration extends MainMigration
{
    protected $schemaName;

    public function __construct()
    {
        DB::connection()->setSchemaGrammar(new ColumnType());
    }

    public function schema($schemaName)
    {
        $this->schemaName = trim($schemaName);
        $this->{$this->schemaName} = DB::connection()->getSchemaBuilder();
        $this->{$this->schemaName}->blueprintResolver(function ($table, $callback) {
            return new Blueprint(($this->schemaName . '.' . $table), $callback);
        });
        return $this->{$this->schemaName};
    }

    public function triggerLog($trigger, $table, $event = 'BEFORE', $action = 'UPDATE OR DELETE')
    {
        $tablelog = strtoupper('log.log_' . trim(str_replace('.', '_', $table)));
        DB::unprepared("
            CREATE TRIGGER $trigger
            $event $action ON $table
            FOR EACH ROW
            EXECUTE PROCEDURE LOG.FUNC_TRIGGER_LOGS('$tablelog');");
    }

    public function triggerSession($trigger, $table, $event = 'BEFORE', $action = 'INSERT OR UPDATE OR DELETE')
    {
        DB::unprepared("
            CREATE TRIGGER $trigger
            $event $action ON $table
            FOR EACH ROW
            EXECUTE PROCEDURE LOG.FUNC_TRIGGER_SESSAO();");
    }

    public function triggerDrop($trigger, $table)
    {
        DB::unprepared("DROP TRIGGER IF EXISTS $trigger ON $table CASCADE;");
    }
}
