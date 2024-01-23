<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CodigoAreaController;
use App\Http\Controllers\ItemComprobanteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ItemEstadoController;
use App\Http\Controllers\EstadoParadaController;
use App\Http\Controllers\RecorridoEstadoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemTipoController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\ProveedorItemController;
use App\Http\Controllers\RecorridoController;
use App\Http\Controllers\TipoDocumentoController;
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
Route::prefix('auth')->group(function () {
    Route::post("registrar", [AuthController::class, 'registrar']);
    Route::post("login", [AuthController::class, 'login']);
});


Route::middleware(['auth:api'])->group(function () {

    // auth
    Route::prefix('auth')->group(function () {
        Route::get("/autenticado", [AuthController::class, 'autenticado']);
    });

    // Recorridos
    Route::prefix('recorridos')->group(function () {
        Route::get('/{recorrido_id?}', [RecorridoController::class, 'findAll']);
        Route::post('', [RecorridoController::class, 'create']);
        Route::patch('origen/{recorrido}', [RecorridoController::class, 'updateOrigen']);
        Route::patch('origen-remover/{recorrido}', [RecorridoController::class, 'removeOrigen']);
        Route::patch('destino/{recorrido}', [RecorridoController::class, 'updateDestino']);
        Route::patch('destino-remover/{recorrido}', [RecorridoController::class, 'removeDestino']);

        Route::patch('/{recorrido}/estado', [RecorridoController::class, 'updateEstado']);
    });

    // Paradas
    Route::prefix('paradas')->group(function () {
        Route::get('/{parada_id?}', [ParadaController::class, 'findAll']);
        Route::post('', [ParadaController::class, 'create']);
        Route::put('/{parada}', [ParadaController::class, 'update']);
        Route::patch('/estado/{parada}', [ParadaController::class, 'updateEstado']);
        Route::delete('/{parada}', [ParadaController::class, 'delete']);
    });

    // Items
    Route::prefix('items')->group(function () {
        Route::get('/{item_id?}', [ItemController::class, 'findAll']);
        Route::post('', [ItemController::class, 'create']);
        Route::put('{item}', [ItemController::class, 'update']);
        Route::patch('/estado/{item}', [ItemController::class, 'updateEstado']);
    });

    // comprobantes items
    Route::prefix('comprobantes-items')->group(function () {
        Route::post('/url-temporaria', [ItemComprobanteController::class, 'generarUrlTemporaria']);
        Route::patch('/url-temporaria/{ItemComprobante}', [ItemComprobanteController::class, 'updateComprobante']);
    });

    // Clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/{cliente_id?}', [ClienteController::class, 'findAll']);
        Route::post('', [ClienteController::class, 'create']);
        Route::put('/{cliente}', [ClienteController::class, 'update']);
    });

    // Empresas
    Route::get('empresas', [EmpresaController::class, 'findAll']);
    // Proveedores
    Route::get('items-proveedores', [ProveedorItemController::class, 'findAll']);
    // Estados items
    Route::get('items-estados', [ItemEstadoController::class, 'findAll']);
    // Tipo items
    Route::get('items-tipos', [ItemTipoController::class, 'findAll']);
    // Estados paradas
    Route::get('paradas-estados', [EstadoParadaController::class, 'findAll']);
    // Estados recorridos
    Route::get('recorridos-estados', [RecorridoEstadoController::class, 'findAll']);
    // Tipo documentos
    Route::get('tipos-documentos', [TipoDocumentoController::class, 'findAll']);
    // Codigos area
    Route::get('codigos-area', [CodigoAreaController::class, 'findAll']);

});


Route::post('armar-recorrido', [RecorridoController::class, 'armarRecorrido']);
