<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionnaireController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/perfil', [QuestionnaireController::class, 'perfil'])->name('perfil');
Route::post('/perfil', [QuestionnaireController::class, 'salvarPerfil']);


Route::get('/questionario/{pagina}', [QuestionnaireController::class, 'questionario']);
Route::post('/questionario/{pagina}', [QuestionnaireController::class, 'salvarPagina']);

Route::view('/finalizado', 'finalizado')->name('finalizado');




require __DIR__.'/auth.php';
