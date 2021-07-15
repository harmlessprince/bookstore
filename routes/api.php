<?php

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

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Route::get('authors/{author}', 'AuthorController@show');
    //authors
    Route::apiResource('authors', 'AuthorController');

    //books
    Route::apiResource('books', 'BookController');
    Route::get('books/{book}/relationships/authors', 'BooksAuthorsRelationshipsController@index')->name('books.relationships.authors');
    Route::patch('books/{book}/relationships/authors', 'BooksAuthorsRelationshipsController@update')->name('books.relationships.authors');

    Route::get('books/{book}/authors', function () {
        return true;
    })->name('books.authors');
});
