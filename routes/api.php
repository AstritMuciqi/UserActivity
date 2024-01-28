<?php

use App\Http\Controllers\UserController;
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


Route::get('/getUsers', [UserController::class, 'getUsersByRole']);

Route::post('/switch-role/{user}', [UserController::class, 'switchRole'])->name('switchRole');

Route::delete('/delete-user/{user}', [UserController::class, 'deleteUser'])->name('deleteUser');

Route::get('/users-with-balance/{userEmail}', [UserController::class, 'getUsersWithBalance']);

Route::put('/update-user-balance/{user}', [UserController::class, 'updateBalance']);

Route::post('/send-balance/{selectedUser}', [UserController::class, 'sendBalance']);





