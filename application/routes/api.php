<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\RecorridoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth

Route::post("auth/registrar", [AuthController::class, 'registrar']);
Route::post("auth/login", [AuthController::class, 'login']);


Route::middleware(['auth:api'])->group(function () {

    // Recorridos
    Route::prefix('recorridos')->group(function () {
        Route::post('', [RecorridoController::class, 'create']);
        Route::patch('origen/{recorrido}', [RecorridoController::class, 'updateOrigen']);
        Route::patch('destino/{recorrido}', [RecorridoController::class, 'destino']);
    });

    // Paradas
    Route::prefix('paradas')->group(function () {
        Route::post('', [ParadaController::class, 'create']);
        Route::put('/{parada}', [ParadaController::class, 'update']);
        Route::delete('/{parada}', [ParadaController::class, 'delete']);
    });

    // Items
    Route::prefix('items')->group(function () {
        Route::post('', [ItemController::class, 'create']);
    });

    // Clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/{cliente?}', [ClienteController::class, 'findAll']);
        Route::post('', [ClienteController::class, 'create']);
        Route::put('/{cliente}', [ClienteController::class, 'update']);
    });

});


Route::post('armar-recorrido', [RecorridoController::class, 'armarRecorrido']);
