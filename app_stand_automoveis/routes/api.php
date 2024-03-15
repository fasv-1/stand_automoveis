<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\LocacaoController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\AuthController;

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

//Para aceder a cada rota neste momento, no header da requesição é necessário passar a key = Authorization e o valor = bearer JWT
Route::prefix('v1')->middleware('jwt.auth')->group(function(){ //convém declarar a versão da api para que as alterações feitas mais tarde, não alterem as integrações pré-existente
    Route::post('me', [AuthController::class, 'me']);
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('locacao', LocacaoController::class);
    Route::apiResource('modelo', ModeloController::class);
});

Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('login', [AuthController::class, 'login']);


