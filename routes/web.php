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

Route::resource('/','ItemsController');
Route::get('/SearchByItemCode','ItemsController@searchByItemCode')->name('search.code');
Route::get('/MIRS-add','MIRSController@MIRScreate')->name('mirs.add');
Route::get('/findMasterItem','ItemsController@searchItemMaster')->name('searchItemMaster');
Route::post('/sessionMIRSitem','MIRSController@addingSessionItem')->name('selecting.item');
Route::DELETE('/removeSessions/{id}','MIRSController@deletePartSession')->name('delete.session');
Route::post('mirs-storedata','MIRSController@StoringMIRS')->name('mirs.store');
Route::post('download-pdf','PDFController@pdf')->name('mirs-download');
Route::get('search-mirs','MIRSController@fullMIRSNo')->name('full-mirs');
Route::post('denied','MIRSController@DeleteDenied')->name('DeleteDenied');
Route::post('MCTstore','MCTController@StoreMCT')->name('Storing.MCT');
Route::get('MCTpreview','MCTController@previewMCT')->name('previewMCT');
Route::post('mct-download','PDFController@mctpdf')->name('print-mct');
Route::get('MRT-create','MRTController@CreateMRT')->name('create.mrt');
Route::post('MRT-store','MRTController@StoreMRT')->name('storing.mrt');
Route::post('MRT-session','MRTController@addToSession')->name('Session.addings');
Route::delete('MRT-delete/{id}','MRTController@deletePartSession')->name('mrtSession.deleting');
Route::get('MIRS-index','MIRSController@Indexgrid')->name('MIRSgridview');
Route::get('findmirs','MIRSController@searchMIRSNo')->name('finding.mirs');
Route::get('summary-mrt','MRTController@summaryMRT')->name('summary.mrt');
Route::get('mrt-find-date','MRTController@MRTSearchdate')->name('mrt.summary.find');
