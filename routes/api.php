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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('aids', 'AidController');
    Route::resource('projects', 'ProjectController');
    Route::resource('response', 'ResponseController');
    Route::post('/projects/progress', 'ProjectController@updateProgress')->name('project.updateProgress');
    Route::get('projects/{id}/peopleInCharge', 'ProjectController@getPeopleInCharge')->name('project.getPeopleInCharge');
    Route::resource('people', 'PersonController');
    Route::resource('payments', 'PaymentController');
    Route::resource('institutions', 'InstitutionController');
    Route::resource('requests', 'RequestController');
    Route::resource('tasks', 'TaskController');
});
Route::resource('user', 'Auth\RegisterController');
Route::post('login', 'Auth\LoginController@login')->name('user.login');



