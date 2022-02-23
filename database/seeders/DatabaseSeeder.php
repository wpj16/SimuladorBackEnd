<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;
use Exception;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {

            try {
                $this->call([
                    \Database\Seeders\Default\PessoaPessoasSeeder::class,
                    \Database\Seeders\Default\UsuarioUsuariosSeeder::class,
                    \Database\Seeders\Default\CampeonatoCampeonatoSeeder::class,
                ]);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
        });
    }
}
