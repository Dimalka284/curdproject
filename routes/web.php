<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StudentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ProductController::class, 'index']); 
Route::get('/index', [ProductController::class, 'create']); 
Route::resource('products', ProductController::class); 
Route::post('/products/delete', [ProductController::class, 'destory'])->name('products.destory'); 
Route::post('/products/update',[ProductController::class, 'Update'])->name('products.update');


Route::get('home',[CategoryController::class,'index']);
Route::get('users', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index')->middleware('auth');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

//View Customers
Route::get('/customer', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customer',[CustomerController::class,'store'])->name('customers.store');

Route::put('/customer', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customer',[CustomerController::class,'destroy'])->name('customers.destroy');


//View Student
Route::get('/student',[StudentController::class,'index']);

// PayHere Test Routes
use App\Http\Controllers\PayHereTestController;
Route::get('/payhere/test', [PayHereTestController::class, 'index'])->name('payhere.test');
Route::get('/payhere/debugger', [PayHereTestController::class, 'debugger'])->name('payhere.debugger');
Route::get('/payhere/return', [PayHereTestController::class, 'returnUrl'])->name('payment.success');
Route::get('/payhere/cancel', [PayHereTestController::class, 'cancelUrl'])->name('payment.cancel');
Route::post('/payhere/notify', [PayHereTestController::class, 'notifyUrl'])->name('payment.notify');




