<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;

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

Route::post('/survey/{pagina}', [SurveyController::class, 'salvarPagina'])
    ->name('survey.salvar');

// finalização
Route::view('/finalizado', 'finalizado')->name('finalizado');
