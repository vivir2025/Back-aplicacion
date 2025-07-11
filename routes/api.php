<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\VisitaController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Perfil de usuario
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);
    
    // Sedes
    Route::apiResource('sedes', SedeController::class);
    
    // Pacientes
    Route::apiResource('pacientes', PacienteController::class);
    Route::get('/pacientes/buscar/{identificacion}', [PacienteController::class, 'buscarPorIdentificacion']);
    
    // Medicamentos
    Route::apiResource('medicamentos', MedicamentoController::class);
    Route::get('/medicamentos/buscar', [MedicamentoController::class, 'index']);
    
    // Visitas
    Route::apiResource('visitas', VisitaController::class);
    Route::get('/visitas/buscar-paciente/{identificacion}', [VisitaController::class, 'buscarPaciente']);
});