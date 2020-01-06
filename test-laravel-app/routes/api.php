<?php

use Illuminate\Routing\Router;

/**
 * @var Router $router
 */

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

$router->get('auth', 'Auth\AuthController@show');
$router->post('auth', 'Auth\AuthController@store');
