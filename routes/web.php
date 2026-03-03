<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ApotekaController;
use App\Http\Controllers\LekController;
use App\Http\Controllers\DobavljacController;
use App\Http\Controllers\KorisnikController;
use App\Http\Controllers\ReceptController;
use App\Http\Controllers\ProdajaController;
use App\Http\Controllers\NarudzbenicaController;
use App\Http\Controllers\ZalihaController;
use App\Http\Controllers\ReportController;
Route::get('/', [PublicController::class, 'landing'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pretraga', [PublicController::class, 'pretraga'])->name('pretraga');
    Route::get('/lek/{lek}', [PublicController::class, 'lekDetalji'])->name('lek.detalji');

    Route::middleware('role:F,A,C')->group(function () {
        Route::resource('lekovi', LekController::class)->parameters(['lekovi' => 'lek']);
    });

    Route::middleware('role:F,A')->group(function () {
        Route::resource('prodaje', ProdajaController::class)->only(['index', 'create', 'store', 'show'])->parameters(['prodaje' => 'prodaja']);
        Route::post('/prodaje/validate-recept', [ProdajaController::class, 'validateRecept'])->name('prodaje.validate-recept');
    });

    Route::middleware('role:F,A,C')->group(function () {
        Route::resource('recepti', ReceptController::class)->except(['edit', 'update', 'destroy'])->parameters(['recepti' => 'recept']);
        Route::get('/recepti/validacija', [ReceptController::class, 'validacija'])->name('recepti.validacija');
    });

    Route::middleware('role:F,A,C')->group(function () {
        Route::get('/zalihe', [ZalihaController::class, 'index'])->name('zalihe.index');
    });
    Route::middleware('role:A,C')->group(function () {
        Route::get('/zalihe/{apoteka}/{lek}/edit', [ZalihaController::class, 'edit'])->name('zalihe.edit');
        Route::put('/zalihe/{apoteka}/{lek}', [ZalihaController::class, 'update'])->name('zalihe.update');
        Route::post('/zalihe/dodaj', [ZalihaController::class, 'dodajLek'])->name('zalihe.dodaj');
    });

    Route::middleware('role:A,C')->group(function () {
        Route::get('/narudzbenice/nova', [NarudzbenicaController::class, 'create'])->name('narudzbenice.create');
        Route::resource('narudzbenice', NarudzbenicaController::class)->only(['index', 'store', 'show'])->parameters(['narudzbenice' => 'narudzbenica']);
        Route::post('/narudzbenice/{narudzbenica}/status', [NarudzbenicaController::class, 'updateStatus'])->name('narudzbenice.status');
        Route::post('/narudzbenice/{narudzbenica}/isporuceno', [NarudzbenicaController::class, 'markDelivered'])->name('narudzbenice.isporuceno');
        Route::post('/narudzbenice/{narudzbenica}/otkazi', [NarudzbenicaController::class, 'cancel'])->name('narudzbenice.otkazi');
        Route::get('/narudzbenice/dobavljac/{dobavljac}/lekovi', [NarudzbenicaController::class, 'getLekovi'])->name('narudzbenice.dobavljac-lekovi');
    });

    Route::middleware('role:A,C')->group(function () {
        Route::resource('dobavljaci', DobavljacController::class)->parameters(['dobavljaci' => 'dobavljac']);
        Route::post('/dobavljaci/{dobavljac}/lekovi', [DobavljacController::class, 'addLek'])->name('dobavljaci.add-lek');
    });

    Route::middleware('role:C')->group(function () {
        Route::resource('apoteke', ApotekaController::class)->parameters(['apoteke' => 'apoteka']);
    });

    Route::middleware('role:A,C')->group(function () {
        Route::resource('korisnici', KorisnikController::class)->parameters(['korisnici' => 'korisnik']);
    });

    Route::middleware('role:A,C')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/prodaja', [ReportController::class, 'prodaja'])->name('prodaja');
            Route::get('/zalihe', [ReportController::class, 'zalihe'])->name('zalihe');
            Route::get('/lekovi', [ReportController::class, 'lekovi'])->name('lekovi');
            Route::get('/recepti', [ReportController::class, 'recepti'])->name('recepti');
            Route::get('/dobavljaci', [ReportController::class, 'dobavljaci'])->name('dobavljaci');
        });
    });
});
