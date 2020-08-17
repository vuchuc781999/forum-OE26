<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('setlocale/{locale}', 'LocaleController@setLocale')->name('set_locale');

Route::group(['middleware' => 'locale'], function () {
    Auth::routes();

    Route::get('/', 'HomeController@index')->name('home');

    Route::resource('questions', 'QuestionController');

    Route::resource('contents', 'ContentController')->only([
        'show',
    ]);

    Route::resource('answers', 'AnswerController')->only([
        'store',
        'edit',
        'update',
        'destroy',
    ]);

    Route::get('searched', 'SearchController@searchedQuestions')->name('search');
});

Route::get('search/{query}', 'SearchController@searchQuestion');
Route::get('search', 'SearchController@searchQuestion');
