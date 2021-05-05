<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiAuthMiddleware;


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

    // Route::get('posts', 'PostController@prueba');
    // Route::get('categories', 'CategoryController@prueba');
    // Route::get('user', 'UserController@prueba');



    // Rutas del User Controller //

    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::put('/user/update', 'UserController@update')->middleware(ApiAuthMiddleware::class);
    Route::post('/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/user/avatar/{filename}', 'UserController@getImage');
    Route::get('/user/user/{id}', 'UserController@getUser');


    
    // Rutas del Category Controller //

    Route::resource('/category', 'CategoryController');

    // Rutas del Post Controller // 

    Route::resource('/post', 'PostController');
    Route::post('/post/upload', 'PostController@upload');
    Route::get('/post/image/{filename}', 'PostController@getImage');
    Route::get('/post/user/{id}', 'PostController@getPostByUser');
    Route::get('/post/category/{id}', 'PostController@getPostByCategory');











