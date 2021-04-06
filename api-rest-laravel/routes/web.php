<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});


//  Rutas Del API


    //Rutas de prueba

    Route::get('posts', 'PostController@prueba');
    Route::get('categories', 'CategoryController@prueba');
    Route::get('user', 'UserController@prueba');


    // Rutas del User Controller //

    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('/user/update', 'UserController@update');




