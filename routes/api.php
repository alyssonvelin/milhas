<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ApiemprestaController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/emprestainstituicoes',[ApiemprestaController::class,'instituicoes'])->name('empresta.instituicoes');
Route::get('/emprestaconvenios',[ApiemprestaController::class,'convenios'])->name('empresta.convenios');
Route::post('/empresta',[ApiemprestaController::class,'index'])->name('empresta.index');

