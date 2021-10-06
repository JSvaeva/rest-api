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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', 'UserController@user')->name('user');
});

Route::group(array('namespace' => 'App\Http\Controllers'), function () {
    Route::name('blogPosts.')->group(function () {
        Route::get('/blogPosts/{type}/{field}', 'BlogPostController@index')->name('index');
        Route::post('/blogPosts', 'BlogPostController@create')->name('create');
        Route::get('/blogPosts/{id}', 'BlogPostController@show')->name('show');
        Route::put('/blogPosts/{id}', 'BlogPostController@update')->name('update');
        Route::delete('/blogPosts/{id}', 'BlogPostController@destroy')->name('destroy');
        Route::post('/blogPosts/{id}/comments/leavecomment', 'BlogPostController@leaveComment')->name('leaveComment');
        Route::get('/blogPosts/{id}/comments/all', 'BlogPostController@getBlogComments')->name('comments');
    });

    Route::name('comments.')->group(function () {
        Route::get('/comments/{id}', 'CommentsController@show')->name('show');
        Route::put('/comments/{id}', 'CommentsController@update')->name('update');
        Route::delete('/comments/{id}', 'CommentsController@destroy')->name('destroy');
    });

    Route::name('users.')->group(function () {
        Route::get('/users', 'UserController@index')->name('index');
        Route::get('/users/{id}', 'UserController@show')->name('show');
        Route::put('/users/{id}', 'UserController@update')->name('update');
        Route::delete('/users/{id}', 'UserController@destroy')->name('destroy');
        Route::get('/users/{id}/comments', 'UserController@getUserComments')->name('comments');
        Route::get('/users/{id}/blogPosts', 'UserController@getUserBlogPosts')->name('blogPosts');
    });

    Route::name('auth.')->group(function () {
        Route::post('/auth/register', 'UserController@register')->name('register');
        Route::get('/auth/login', 'UserController@login')->name('login');
        Route::get('/auth/logout', 'UserController@logout')->name('logout');
    });
});