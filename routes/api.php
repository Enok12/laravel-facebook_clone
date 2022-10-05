<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function(){

    // Route::post('/posts',[App\Http\Controllers\PostController::class, 'store']);
    // Route::get('/posts',[App\Http\Controllers\PostController::class, 'index']);

    // Route::get('/users/{id}',[App\Http\Controllers\UserController::class, 'show']);

    Route::get('auth-user',[App\Http\Controllers\AuthUserController::class, 'show']);
    Route::apiResources([
        '/posts' => App\Http\Controllers\PostController::class,
        '/users' => App\Http\Controllers\UserController::class,
        '/users/{user}/posts' => App\Http\Controllers\UserPostController::class,

    ]);

});


