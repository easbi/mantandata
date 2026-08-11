<?php

use App\Http\Controllers\AnomalyImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/anomalies', [AnomalyImportController::class, 'index'])->name('anomalies.index');
Route::get('/anomalies/import', [AnomalyImportController::class, 'create'])->name('anomalies.import');
Route::post('/anomalies/import', [AnomalyImportController::class, 'store'])->name('anomalies.import.store');
Route::post('/anomalies/{case}/status', [AnomalyImportController::class, 'updateStatus'])->name('anomalies.updateStatus');
Route::post('/anomalies/{case}/followup', [AnomalyImportController::class, 'storeFollowup'])->name('anomalies.storeFollowup');
Route::get('/anomalies/{case}', [AnomalyImportController::class, 'show'])->name('anomalies.show');

Route::get('/alokasi-petugas', [App\Http\Controllers\AlokasiPetugasController::class, 'index'])->name('alokasi.index');
Route::post('/alokasi-petugas', [App\Http\Controllers\AlokasiPetugasController::class, 'store'])->name('alokasi.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
