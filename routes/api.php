<?php

declare(strict_types=1);

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyVersionController;
use Illuminate\Support\Facades\Route;

Route::post('company', CompanyController::class);
Route::get('company/{edrpou}/versions', CompanyVersionController::class);
