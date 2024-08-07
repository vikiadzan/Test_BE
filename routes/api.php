<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/borrow', [LibraryController::class, 'borrowBook']);
Route::post('/return', [LibraryController::class, 'returnBook']);
Route::get('/books', [LibraryController::class, 'checkBooks']);
Route::get('/members', [LibraryController::class, 'checkMembers']);
