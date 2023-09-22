<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RequestController;
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

Route::group([
  'middleware' => 'auth',
  'as' => 'requests.'
], function () {
  Route::get('/', [RequestController::class, 'view'])->name('index');
  
  Route::group([
    'prefix' => '/chamado'
  ], function () {
    Route::group([
      'middleware' => 'role:client'
    ], function () {
      Route::get('/criar', [RequestController::class, 'createView'])->name('create');
      Route::post('/', [RequestController::class, 'create'])->name('post');
    });
    
    Route::group([
      'prefix' => '/{id}'
    ], function () {
      Route::put('/', [RequestController::class, 'update'])->name('put');
      Route::get('/', [RequestController::class, 'detailView'])->name('detail');
      Route::post('/comentario', [CommentController::class, 'create'])->name('comments.post');
    });

  });
});

Route::group([
  'controller' => AuthController::class
], function () {
  Route::group([
    'middleware' => 'guest'
  ], function () {
    Route::group([
      'prefix' => '/entrar',
      'as' => 'login.'
    ], function () {
      Route::get('/', 'loginView')->name('index');
      Route::post('/', 'loginRequest')->name('post');
    });

    Route::group([
      'prefix' => '/cadastrar',
      'as' => 'register.'
    ], function () {
      Route::get('/', 'registerView')->name('index');
      Route::post('/', 'registerRequest')->name('post');
    });
  });
  
  Route::get('/sair', 'logoutRequest')->name('logout.index');
});

