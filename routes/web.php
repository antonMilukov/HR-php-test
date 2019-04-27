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

Route::get('/', 'Controller@index')->name('index');
Route::get('/temp', 'Controller@temp')->name('temp');
Route::get('/table-orders', 'Controller@tableOrders')->name('table-orders');
Route::get('/order/{orderId}', 'Controller@orderForm')->name('order-form');
Route::post('/order/{orderId}', 'Controller@orderFormSave')->name('order-form-save');
