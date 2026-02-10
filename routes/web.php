<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\RelatorioController;

// raiz
Route::get('/', fn () => redirect()->route('welcome'));

// welcome
Route::view('/welcome', 'welcome')->name('welcome');

// perfil
Route::get('/perfil', [SurveyController::class, 'perfil'])
    ->name('perfil');

Route::post('/perfil', [SurveyController::class, 'salvarPerfil'])
    ->name('perfil.salvar');

// survey paginado
Route::get('/survey/{pagina}', [SurveyController::class, 'survey'])
    ->name('survey');

// finalização
Route::view('/finalizado', 'finalizado')->name('finalizado');

// relatório em excel
Route::get('/relatorio', RelatorioController::class)->name('relatorio');

