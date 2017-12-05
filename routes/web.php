<?php

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


Route::get('/', 'ProjectsController@index');

Route::get('/logout', function () {
    Auth::logout();
    return rediret('/');
});

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');

Route::resource('productCRUD','ProductCRUDController');

Route::resource('projects','ProjectsController');

Route::get('getMavenlinkProject', 'ProjectsController@getProjectMavenlink');
Route::get('getMavenlinkUser', 'ProjectsController@getUsersMavenlink');
