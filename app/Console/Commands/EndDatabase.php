<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class EndDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop schemas in database';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "\n\n";
        $schemas = config('database.connections')[config('database.default')]['schemas'] ?? [];
        foreach ($schemas as $schema) {
            if (!DB::statement('DROP SCHEMA IF EXISTS ' .  $schema . ' CASCADE;')) {
                echo 'SCHEMA  ' . str_pad($schema, 30, ' ') . " - falha ao excluir!\n";
                return false;
            }
            echo 'SCHEMA   ' . str_pad($schema, 30, ' ') . " - excluido com sucesso!\n";
        }
        echo "\n\n";
        return true;
    }
}
