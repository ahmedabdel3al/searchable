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

use App\User;
use App\Classes\CustomPagination;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $usersArray = \App\User::all()->toArray();
     $customPaginate =new CustomPagination($usersArray,5);
     return $customPaginate->paginate();
});

Route::get('/search', function (\Illuminate\Http\Request $request) {
    $users =User::filter($request)->get();
    return $users;
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
