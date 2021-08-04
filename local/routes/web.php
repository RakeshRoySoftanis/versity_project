<?php

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

// Route::get('/', function () {
//     return redirect('/');
// });

Route::get('getFields', 'ZohoIntegration@getFields'); //create table
Route::get('syncModule', 'ZohoIntegration@syncModule');

Route::get('getAllRecordsCrm/{module}/{page}', 'Webhook@syncRecordsByPage'); // insert data
Route::get('getWebhook', 'Webhook@sync_getRecordsById')->name('webhook');
Route::get('deleteWebhook', 'Webhook@delete_record')->name('delete');
Route::get('sync_allUsers', 'Webhook@GetallUsers');

// Test routes
Route::get('/zfields', 'ZohoIntegration@zfields');
Route::get('/zlayouts', 'ZohoIntegration@zlayouts');





