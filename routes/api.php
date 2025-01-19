<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;

Route::apiResource('tours', TourController::class);
Route::apiResource('hotels', HotelController::class);
Route::apiResource('bookings', BookingController::class);

/**
 * Index bookings without scopes.
 */
Route::get('/bookings/index-filter', [BookingController::class, 'indexFilter']);

Route::get('/bookings/export', [BookingController::class, 'export']);
Route::get('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
