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

Route::post('/get-xls-data', 'App\Http\Controllers\XlsFileController@getFileData');
Route::get('/get-filters', 'App\Http\Controllers\XlsFileController@getFilterNames');
Route::get('/download', 'App\Http\Controllers\PdfController@downloadFile');
