<?php

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/blogPosts/{type}/{field}', 'App\Http\Controllers\BlogPostController@index')->name('blogPosts.index');
Route::post('/blogPosts', 'App\Http\Controllers\BlogPostController@create')->name('blogPosts.create');
Route::get('/blogPosts/{id}', 'App\Http\Controllers\BlogPostController@show')->name('blogPosts.show');
Route::put('/blogPosts/{id}', 'App\Http\Controllers\BlogPostController@update')->name('blogPosts.update');
Route::delete('/blogPosts/{id}', 'App\Http\Controllers\BlogPostController@destroy')->name('blogPosts.destroy');

//->middleware('auth');
//need to be logged in to create comment