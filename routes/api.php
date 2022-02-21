<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Usuario\UsuarioController;
use App\Http\Controllers\Api\Simulacao\SorteioController;

### Rotas não autenticadas
//
Route::post('/login/', [LoginController::class, 'login'])->name('tradetechnology.login');
//
### Rotas autenticadas
//
Route::middleware(['api_client'])->group(function () {
    //
    Route::post('/simular/campeonato/', [SorteioController::class, 'simularCampeonato'])->name('tradetechnology.simular.campeonato');
    //
    Route::post('/teste/', [SorteioController::class, 'teste'])->name('tradetechnology.teste');
    //
});
