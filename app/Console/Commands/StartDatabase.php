<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class StartDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create schemas in database';


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
            if (!DB::statement('CREATE SCHEMA IF NOT EXISTS ' .  $schema . ';')) {
                echo 'SCHEMA ' . str_pad($schema, 30, ' ') . " - falha ao criar!\n";
                return false;
            }
            echo 'SCHEMA ' . str_pad($schema, 30, ' ') . " - criado com sucesso!\n";
        }
        echo "\n\n";
        return true;
    }
}
