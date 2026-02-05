<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionnaireController;

/*
|--------------------------------------------------------------------------
| Rotas públicas – CPA
|--------------------------------------------------------------------------
*/

// raiz redireciona para welcome
Route::get('/', function () {
    return redirect()->route('welcome');
});

// página inicial
Route::view('/welcome', 'welcome')->name('welcome');

// perfil do respondente
Route::get('/perfil', [QuestionnaireController::class, 'perfil'])->name('perfil');
Route::post('/perfil', [QuestionnaireController::class, 'salvarPerfil']);

// questionário paginado
Route::get('/questionario/{pagina}', [QuestionnaireController::class, 'questionario']);
Route::post('/questionario/{pagina}', [QuestionnaireController::class, 'salvarPagina']);

// finalização
Route::view('/finalizado', 'finalizado')->name('finalizado');
