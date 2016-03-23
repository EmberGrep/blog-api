<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$router->get('/', function () {
    return [
        'version' => Config::get('app.version'),
    ];
});

$router->group(['prefix' => 'blogs'], function($router) {
    $router->get('/', 'BlogsController@index');
    $router->post('/', 'BlogsController@store');
    $router->get('/{id}', 'BlogsController@find');
    $router->patch('/{id}', 'BlogsController@update');
    $router->delete('/{id}', 'BlogsController@delete');
});

$router->group(['prefix' => 'comments'], function($router) {
    $router->get('/', 'CommentsController@index');
    $router->post('/', 'CommentsController@store');
    $router->get('/{id}', 'CommentsController@find');
    $router->patch('/{id}', 'CommentsController@update');
    $router->delete('/{id}', 'CommentsController@delete');
});

$router->any('reset', 'ResetController@reset');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
