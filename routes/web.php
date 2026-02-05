<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionnaireController;

Route::get('/', [QuestionnaireController::class, 'show']);
Route::post('/submit', [QuestionnaireController::class, 'store'])->name('submit');
Route::view('/thanks', 'thanks')->name('thanks');
