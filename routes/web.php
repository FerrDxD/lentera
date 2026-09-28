<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\JuriController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\LombaController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Juri\JuriDashboardController;
use App\Http\Controllers\Juri\PenilaianController;
use App\Http\Controllers\PublicLeaderboardController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', [PublicLeaderboardController::class, 'index'])->name('home');

// Auth Dashboard Dispatcher
Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('juri.dashboard');
})->name('dashboard');

// Admin Route Group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Lomba CRUD & Lock Actions
    Route::resource('lomba', LombaController::class)->except(['create', 'edit', 'show']);
    Route::post('lomba/{lomba}/lock', [LombaController::class, 'lock'])->name('lomba.lock');
    Route::post('lomba/{lomba}/unlock', [LombaController::class, 'unlock'])->name('lomba.unlock');

    // Kriteria CRUD per Lomba
    Route::get('lomba/{lomba}/kriteria', [KriteriaController::class, 'index'])->name('lomba.kriteria.index');
    Route::post('lomba/{lomba}/kriteria', [KriteriaController::class, 'store'])->name('lomba.kriteria.store');
    Route::put('lomba/{lomba}/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('lomba.kriteria.update');
    Route::delete('lomba/{lomba}/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('lomba.kriteria.destroy');

    // Peserta CRUD per Lomba
    Route::get('lomba/{lomba}/peserta', [PesertaController::class, 'index'])->name('lomba.peserta.index');
    Route::post('lomba/{lomba}/peserta', [PesertaController::class, 'store'])->name('lomba.peserta.store');
    Route::put('lomba/{lomba}/peserta/{peserta}', [PesertaController::class, 'update'])->name('lomba.peserta.update');
    Route::delete('lomba/{lomba}/peserta/{peserta}', [PesertaController::class, 'destroy'])->name('lomba.peserta.destroy');

    // Juri Management & Assign
    Route::get('lomba/{lomba}/juri', [JuriController::class, 'index'])->name('lomba.juri.index');
    Route::post('lomba/{lomba}/juri/assign', [JuriController::class, 'assign'])->name('lomba.juri.assign');
    Route::delete('lomba/{lomba}/juri/{user}', [JuriController::class, 'unassign'])->name('lomba.juri.unassign');
    Route::post('juri/create', [JuriController::class, 'storeUser'])->name('juri.store');

    // Rekap & Leaderboard
    Route::get('lomba/{lomba}/rekap', [RekapController::class, 'show'])->name('lomba.rekap');
});

// Juri Route Group
Route::middleware(['auth', 'role:juri'])->prefix('juri')->name('juri.')->group(function () {
    Route::get('dashboard', [JuriDashboardController::class, 'index'])->name('dashboard');
    Route::get('nilai/{lomba}', [PenilaianController::class, 'form'])->name('penilaian.form');
    Route::post('nilai/{lomba}/{peserta}', [PenilaianController::class, 'store'])->name('penilaian.store');
});

require __DIR__.'/settings.php';
