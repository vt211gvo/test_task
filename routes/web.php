<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarRentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [CarRentController::class, 'index']);
Route::get('/get-bookings', [CarRentController::class, 'getBookings']);

// Fallback Route
Route::fallback(function(){
    return "<h1>404 Route not exitst!</h1>";
});

