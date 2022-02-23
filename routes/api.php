<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Usuario\UsuarioController;
use App\Http\Controllers\Api\Simulacao\SorteioController;
use App\Http\Controllers\Api\Time\{
    TimeController,
    JogadorController
};
use App\Http\Controllers\Api\Campeonato\CampeonatoController;

Route::middleware(['api'])->prefix('/')->group(function () {
    //
    ### Rotas não autenticadas
    Route::post('/login/', [UsuarioController::class, 'login'])->name('login');
    //
    //
    ### Rotas autenticadas
    Route::middleware(['api_client'])->prefix('/minha-conta/')->group(function () {
        //
        Route::get('/meus-dados/', [UsuarioController::class, 'listarMeusDados'])->name('minha.conta.meus.dados');
        //
        Route::get('/meus-campeonatos/', [SorteioController::class, 'listarSimulacoesCampeonatos'])->name('minha.conta.meus.campeonatos');
        //
        Route::post('/simular/campeonato/', [SorteioController::class, 'simularCampeonato'])->name('minha.conta.simular.campeonato');
        //
        //
        Route::prefix('/cadastrar/')->group(function () {
            //
            Route::post('/time/', [TimeController::class, 'cadastrarTime'])->name('minha.conta.cadastrar.time.post');
            //
            Route::post('/campeonato/', [CampeonatoController::class, 'cadastrarCampeonato'])->name('minha.conta.cadastrar.campeonato.post');
            //
            Route::post('/jogador/', [JogadorController::class, 'cadastrarJogador'])->name('minha.conta.cadastrar.jogadores.post');
        });
        //
        //
        Route::prefix('/listar/')->group(function () {
            //
            Route::get('/times/', [TimeController::class, 'listarTimes'])->name('minha.conta.cadastrar.time.get');
            //
            Route::get('/campeonatos/', [CampeonatoController::class, 'listarCampeonatos'])->name('minha.conta.cadastrar.campeonato.get');
            //
            Route::get('/jogadores/', [JogadorController::class, 'listarJogadores'])->name('minha.conta.cadastrar.jogadores.get');
        });
        //
        //
    });
    //
    //
});
