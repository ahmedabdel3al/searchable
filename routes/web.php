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

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $collection = \App\User::all()->toArray();
    // Get current page form url e.x. &page=1
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    // Define how many items we want to be visible in each page
    $perPage = env('PER_PAGE', 5);
    // Slice the collection to get the items to display in current page
    $currentPageItems = array_slice($collection, ($currentPage * $perPage) - $perPage, $perPage);
    // Create our paginate and pass it to the view
    $customCollectionItems = new LengthAwarePaginator($currentPageItems, count($collection), $perPage);
    // set url path for generated links
    $customCollectionItems->setPath(request()->url());
    return $customCollectionItems;
});

Route::get('/search', function (\Illuminate\Http\Request $request) {

    $users = \App\User::filter($request)->get();
    return $users;
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
