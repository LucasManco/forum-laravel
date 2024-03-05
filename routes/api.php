<?php

use App\Http\Controllers\API\QuestionsController;
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


// Route::apiResource('users', \App\Http\Controllers\API\UsersController::class );

Route::post('signin',[\App\Http\Controllers\API\UsersController::class,'signin']);
Route::post('login',[\App\Http\Controllers\API\UsersController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [\App\Http\Controllers\API\UsersController::class, 'logout']);    
    Route::apiResource('questions', \App\Http\Controllers\API\QuestionsController::class );
    Route::apiResource('answers', \App\Http\Controllers\API\AnswersController::class );
        
});