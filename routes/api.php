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
Route::post('/blogPosts/{id}/comments/leavecomment', 'App\Http\Controllers\BlogPostController@leaveComment')->name('blogPosts.leaveComment');
Route::get('/blogPosts/{id}/comments/all', 'App\Http\Controllers\BlogPostController@getBlogComments')->name('blogPosts.comments');

Route::get('/comments/{id}', 'App\Http\Controllers\CommentsController@show')->name('comment.show');
Route::put('/comments/{id}', 'App\Http\Controllers\CommentsController@update')->name('comment.update');
Route::delete('/comments/{id}', 'App\Http\Controllers\CommentsController@destroy')->name('comment.destroy');

Route::get('/users', 'App\Http\Controllers\UserController@index')->name('users.index');
Route::post('/users', 'App\Http\Controllers\UserController@create')->name('users.create');
Route::get('/users/{id}', 'App\Http\Controllers\UserController@show')->name('users.show');
Route::put('/users/{id}', 'App\Http\Controllers\UserController@update')->name('users.update');
Route::delete('/users/{id}', 'App\Http\Controllers\UserController@destroy')->name('users.destroy');
Route::get('/users/{id}/comments', 'App\Http\Controllers\UserController@getUserComments')->name('users.comments');
Route::get('/users/{id}/blogPosts', 'App\Http\Controllers\UserController@getUserBlogPosts')->name('users.blogPosts');

Route::put('/login', 'App\Http\Controllers\UserController@login')->name('users.login');
Route::put('/logout', 'App\Http\Controllers\UserController@logout')->name('users.logout');

//->middleware('auth');
//need to be logged in to create comment