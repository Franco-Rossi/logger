<?php

use Illuminate\Support\Facades\Route;
use zuma\tools\HealthCheck\HealthCheck;

Route::group(['prefix' => 'logger'], function () {
    Route::post('/log', "LogController@store");
    Route::get('/log', "LogController@show");

    Route::group(['middleware' => 'auth'], function () {
        HealthCheck::serverStatusRoutes();
    });
    
    HealthCheck::healthRoutes();
});
