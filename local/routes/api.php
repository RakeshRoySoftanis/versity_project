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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//login route
Route::post('/login', 'AuthController@login');
Route::post('/forgetPasswordMail', 'AuthController@forgetPasswordMail');
Route::post('/resetPassword', 'AuthController@resetPassword');
Route::post('/changePassword', 'AuthController@changePassword');

//Register route
Route::post('/register', 'AuthController@register');

//forget password route
Route::post('/forgotpassword', 'ForgotController@forgotpassword');

//reset password route
Route::post('/resetpassword', 'ForgotController@resetpassword');

//current user
Route::get('/user', 'UserController@user')->middleware('auth:api');
Route::get('/my-contact', 'UserController@logged_in_contact')->middleware('auth:api');
Route::post('/save-contact', 'UserController@save_contact')->middleware('auth:api');

//contact 
Route::get('/contact', 'ActivitiesController@contact')->middleware('auth:api');

Route::get('/getFetchOneListByOneCondition', 'ActivitiesController@fetchOne')->middleware('auth:api');
Route::get('/getFetchList', 'ActivitiesController@getFetchList')->middleware('auth:api');




/*
|--------------------------------------------------------------------------
|  Master Panel 
|--------------------------------------------------------------------------
*/
Route::post('/master-login', 'MasterController@login');
Route::get('/master-data', 'MasterController@loggedin_data');

Route::get('/master-portal-client', 'MasterController@portal_client');
Route::post('/master-attempt-client-login', 'MasterController@attempt_login_client');

/*
|--------------------------------------------------------------------------
|  Users Master
|--------------------------------------------------------------------------
*/
Route::get('master-users', 'MasterController@userLists');
Route::post('/save-master-users', 'MasterController@save_master_user');
Route::post('/insert-master-users', 'MasterController@insert_master_user');
Route::post('/update-master-users', 'MasterController@update_master_user');
Route::get('/edit-master-users', 'MasterController@edit_master_user');
Route::get('/delete-master-users', 'MasterController@delete_master_user');
Route::get('/delete-layout', 'MasterController@delete_layout');
Route::get('/userAssignAccount', 'MasterController@userAssignAccount');




/*
|--------------------------------------------------------------------------
|  Setting Master
|--------------------------------------------------------------------------
*/
Route::get('/Setting', 'MasterController@Setting');
Route::post('/update-setting', 'MasterController@updateSetting');
Route::post('/setting-logo', 'MasterController@setting_logo');
Route::post('/setting-color', 'MasterController@setting_color');


/*
|--------------------------------------------------------------------------
|  Admin Panel 
|--------------------------------------------------------------------------
*/
Route::post('/admin-login', 'AdminUserController@loginAdmin');
Route::get('/admin-data', 'AdminUserController@loggedin_data');
Route::get('admin-dashboard', 'AdminUserController@dashboard');
Route::get('admin-contact-list/{id}', 'AdminUserController@showContactList');
Route::post('/admin-attempt-client-login', 'AdminUserController@attempt_login_client');
