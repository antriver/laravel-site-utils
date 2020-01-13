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

// Login
$router->get('auth', 'AuthController@show');
$router->post('auth', 'AuthController@store');

// Email Verification
$router->get('email-verifications', 'EmailVerificationController@index');
$router->get('email-verifications/{id}', 'EmailVerificationController@show');
$router->post('email-verifications', 'EmailVerificationController@store');
$router->post('email-verifications/{id}/resend', 'EmailVerificationController@resend');
$router->post('email-verifications/{id}/verify', 'EmailVerificationController@verify');
$router->delete('email-verifications/{id}', 'EmailVerificationController@destroy');

// Forgot Password
$router->post('forgot-password', 'ForgotPasswordController@store');

// Password Reset
$router->get('password-resets/{token}', 'PasswordResetController@show');
$router->patch('users/{user}/password', 'PasswordResetController@reset');

// Users
$router->post('users', 'RegisterController@store');
$router->patch('users/{user}', 'UserController@update');

// Amazon SNS
$router->post('sns/ses-bounce', 'SnsController@sesBounce');
$router->post('sns/ses-complaint', 'SnsController@sesComplaint');

