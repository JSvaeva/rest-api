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

Route::group(array('namespace' => 'App\Http\Controllers'), function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', 'UserController@user')->name('user');

        Route::post('/blogPosts', 'BlogPostController@create')->name('blogPosts.create');
        Route::put('/blogPosts/{id}', 'BlogPostController@update')->name('blogPosts.update');
        Route::delete('/blogPosts/{id}', 'BlogPostController@destroy')->name('blogPosts.destroy');
        Route::post('/blogPosts/{id}/comments/leavecomment', 'BlogPostController@leaveComment')->name('blogPosts.leaveComment');

        Route::put('/comments/{id}', 'CommentsController@update')->name('comments.update');
        Route::delete('/comments/{id}', 'CommentsController@destroy')->name('comments.destroy');

        Route::put('/users/{id}', 'UserController@update')->name('users.update');
        Route::delete('/users/{id}', 'UserController@destroy')->name('users.destroy');

        Route::get('/auth/logout', 'UserController@logout')->name('auth.logout');
    });

    Route::get('/blogPosts/{type}/{field}', 'BlogPostController@index')->name('blogPosts.index');
    Route::get('/blogPosts/{id}', 'BlogPostController@show')->name('blogPosts.show');
    
    
    Route::get('/blogPosts/{id}/comments/all', 'BlogPostController@getBlogComments')->name('blogPosts.comments');
    Route::get('/comments/{id}', 'CommentsController@show')->name('comments.show');
    
    Route::get('/users', 'UserController@index')->name('users.index');
    Route::get('/users/{id}', 'UserController@show')->name('users.show');
    
    Route::get('/users/{id}/comments', 'UserController@getUserComments')->name('users.comments');
    Route::get('/users/{id}/blogPosts', 'UserController@getUserBlogPosts')->name('users.blogPosts');
    
    Route::post('/auth/register', 'UserController@register')->name('auth.register');
    Route::get('/auth/login', 'UserController@login')->name('auth.login');
    
});