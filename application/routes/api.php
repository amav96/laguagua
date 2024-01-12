<?php

use App\Http\Controllers\Auth\AuthController;
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

// Recorridos
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('recorridos')->group(function () {
        Route::post('', [RecorridoController::class, 'create']);
    });
});


Route::post('armar-recorrido', [RecorridoController::class, 'armarRecorrido']);
