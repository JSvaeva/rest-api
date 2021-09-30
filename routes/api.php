<?php

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::resource('blogPosts', 'App\Http\Controllers\BlogPostController');
Route::get('/blogPosts/{type}/{field}', 'App\Http\Controllers\BlogPostController@index')->name('blogPosts.index');
Route::post('/blogPosts', 'App\Http\Controllers\BlogPostController@store')->name('blogPosts.store');
Route::get('/blogPosts/create', 'App\Http\Controllers\BlogPostController@create')->name('blogPosts.create');
Route::get('/blogPosts/{blogPost}', 'App\Http\Controllers\BlogPostController@show')->name('blogPosts.show');
Route::put('/blogPosts/{blogPost}', 'App\Http\Controllers\BlogPostController@update')->name('blogPosts.update');
Route::delete('/blogPosts/{blogPost}', 'App\Http\Controllers\BlogPostController@destroy')->name('blogPosts.destroy');
Route::get('/blogPosts/{blogPost}/edit', 'App\Http\Controllers\BlogPostController@edit')->name('blogPosts.edit');
