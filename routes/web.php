<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\PublicacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'formularioLogin'])->middleware('guest')->name('login');
Route::post('/iniciar-sesion', [AuthController::class, 'login'])->middleware(['guest', 'throttle:6,1'])->name('login.post');
Route::get('/registro', [AuthController::class, 'formularioRegistro'])->middleware('guest')->name('registro');
Route::post('/registro', [AuthController::class, 'registro'])->middleware(['guest', 'throttle:10,1'])->name('registro.post');

Route::middleware('guest')->group(function () {
    Route::get('/recuperar-contrasena', [PasswordRecoveryController::class, 'showRequest'])->name('password.recovery.request');
    Route::post('/recuperar-contrasena', [PasswordRecoveryController::class, 'sendCode'])
        ->middleware('throttle:10,1')->name('password.recovery.send');
    Route::get('/verificar-codigo', [PasswordRecoveryController::class, 'showVerification'])->name('password.recovery.verify.form');
    Route::post('/verificar-codigo', [PasswordRecoveryController::class, 'verifyCode'])
        ->middleware('throttle:10,1')->name('password.recovery.verify');
    Route::post('/verificar-codigo/reenviar', [PasswordRecoveryController::class, 'resendCode'])
        ->middleware('throttle:10,1')->name('password.recovery.resend');
    Route::get('/crear-nueva-contrasena', [PasswordRecoveryController::class, 'showResetForm'])->name('password.recovery.reset.form');
    Route::post('/crear-nueva-contrasena', [PasswordRecoveryController::class, 'resetPassword'])
        ->middleware('throttle:5,1')->name('password.recovery.reset');
    Route::get('/contrasena-actualizada', [PasswordRecoveryController::class, 'success'])->name('password.recovery.success');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/salir', [AuthController::class, 'salir'])->name('salir');

    Route::middleware('can:administrar')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [PublicacionController::class, 'index'])->name('index');
        Route::post('/publicaciones', [PublicacionController::class, 'guardar'])->name('store');
        Route::put('/publicaciones/{publicacion}', [PublicacionController::class, 'actualizar'])->name('update');
        Route::delete('/publicaciones/{publicacion}', [PublicacionController::class, 'eliminar'])->name('destroy');
    });
});
