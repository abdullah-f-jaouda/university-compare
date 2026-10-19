<?php

use App\Http\Controllers\CompareController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\UniversityController;
use App\Models\Program;
use App\Models\University;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'universitiesCount' => University::active()->count(),
        'programsCount' => Program::active()->count(),
    ]);
})->name('home');

Route::get('/universities', [UniversityController::class, 'index'])->name('universities.index');
Route::get('/universities/{university}', [UniversityController::class, 'show'])->name('universities.show');

Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');

Route::get('/compare', [CompareController::class, 'show'])->name('compare');
