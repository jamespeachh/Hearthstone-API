<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\HearthstoneController;
use Illuminate\Support\Facades\Route;


Route::get('/hearthstoneView', [HearthstoneController::class, 'sendData']);
Route::post('/form-submit', [FormController::class, 'submit']);
Route::get('/FinalMessage', [FormController::class, 'index']);


