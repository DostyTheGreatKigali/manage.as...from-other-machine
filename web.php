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

Route::get('/', function () {
//    return view('welcome');
    return redirect()->route('voyager.dashboard');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('dashboard', function () {
        $base_url = url('/').'/admin/dashboard';
		return view('dashboard', [
            'base_url' => $base_url
        ]);
    });

Route::get('dashboard/supporters/print', 'SupportersForPrintController@showSupporters');
Route::get('dashboard/supporters', 'SupportersController@showSupporters');
Route::get('dashboard/supporters/details/{supporter_id}', 'SupportersController@showSupporterDetails');
Route::get('dashboard/supporters/regional', 'SupportersController@regionalSupporters');
Route::post('dashboard/supporters/update-card-printed', 'SupportersController@updateCardPrinted');
Route::get('dashboard/transactions', 'TransactionsController@showTransactions');
Route::get('dashboard/charts', 'HomeController@showChart');
Route::get('dashboard/supporters/unpaid', 'SupportersController@showUnpaidSupporters');
    
});

// Route::get('supporter/details/', 'SupportersController@showUnauthorizedSupporterDetails');
Route::get('supporter/details/{supporter_id}/{supporter_number}/{supporter_phone}', 'SupportersController@showUnauthorizedSupporterDetails');
// Route::get('supporter/details/{supporter_id}', 'SupportersController@showUnauthorizedSupporterDetails');

// QR CODE GENERATION TESTING
Route::get('/code', function () {
    \QrCode::size(500)
            ->format('png')
            ->generate('www.google.com', public_path('ktk_codes_supporters/qrcode.png'));
return view('qrCode');
});

// DOWNLOADING QR CODE IMAGE
Route::get('show/download', 'DownloadsController@showDownload');
Route::get('download/{file}', 'DownloadsController@download');
Route::get('get/download/{supporter_id}', 'DownloadsController@getDownload');

    //CHECKING PHP INFORMATION
Route::get('/phpinfo', function () {
        phpinfo(); die;
    });

// REFRESH COMMANDS
Route::get('/clear-cache', function() {
    $exitCode = \Illuminate\Support\Facades\Artisan::call('cache:clear');
    $exitCode = \Illuminate\Support\Facades\Artisan::call('config:clear');
    $exitCode = \Illuminate\Support\Facades\Artisan::call('view:clear');
    $exitCode = \Illuminate\Support\Facades\Artisan::call('route:clear');
    // $exitCode = \Illuminate\Support\Facades\Artisan::call('storage:link');
    return $exitCode;
    // return what you want
 });
 
Route::get('/storage/link', function() {
    
    $exitCode = \Illuminate\Support\Facades\Artisan::call('storage:link');
    return $exitCode;
 });

 Route::get('/logout-independent', function () {
    auth()->logout();
    return redirect()->route('voyager.dashboard');
});