<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.homepage');
})->name('base');

Auth::routes();

Route::get('/login-admin', 'Auth\LoginController@showAdminLoginForm')->name('login.admin');
Route::get('/login-mahasiswa', 'Auth\LoginController@showMahasiswaLoginForm')->name('login.mahasiswa');
Route::get('/login-dosen', 'Auth\LoginController@showDosenLoginForm')->name('login.dosen');
Route::get('/login-dekan', 'Auth\LoginController@showDekanLoginForm')->name('login.dekan');
Route::get('/login-rektorat', 'Auth\LoginController@showRektoratLoginForm')->name('login.rektorat');

Route::post('/login-admin', 'Auth\LoginController@adminLogin');
Route::post('/login-mahasiswa', 'Auth\LoginController@mahasiswaLogin');
Route::post('/login-dosen', 'Auth\LoginController@dosenLogin');
Route::post('/login-dekan', 'Auth\LoginController@dekanLogin');
Route::post('/login-rektorat', 'Auth\LoginController@rektoratLogin');

/* 
|---------------------------------------------
| All routes for admin
|---------------------------------------------
*/

Route::view('/home', 'home')->middleware('auth');
Route::group(['middleware' => 'auth:admin'], function () {
    Route::view('/admin', 'dashboard.admin-dashboard')->name('dashboard-admin');
    Route::get('data-dash-admin','AdminPage\DataUser\DataAdminController@dashAdmin')->name('data-dash-admin');
    Route::resource('data-user-admin', 'AdminPage\DataUser\DataAdminController');
    Route::resource('data-user-mahasiswa', 'AdminPage\DataUser\DataMahasiswaController');
    Route::resource('data-user-dosen', 'AdminPage\DataUser\DataDosenController');
    Route::resource('data-user-dekan', 'AdminPage\DataUser\DataDekanController');
    Route::resource('data-user-rektorat', 'AdminPage\DataUser\DataRektoratController');
    Route::resource('data-jenis-kegiatan', 'General\JenisKegiatanController');
    Route::resource('data-fakultas', 'General\DataFakultasController');
    Route::resource('data-prodi', 'General\DataProdiController');
    Route::resource('data-proposal', 'AdminPage\DataProposalController');
    Route::get('validasi-proposal','AdminPage\DataProposalController@validasi')->name('validasi-proposal');
    Route::post('valid-y','AdminPage\DataProposalController@validY')->name('valid-y');
    Route::post('valid-n','AdminPage\DataProposalController@validN')->name('valid-n');
    Route::post('import-mahasiswa','AdminPage\DataUser\DataMahasiswaController@importDataMahasiswa')->name('import-data-mahasiswa');
    Route::post('import-dosen','AdminPage\DataUser\DataDosenController@importDataDosen')->name('import-data-dosen');
});

Route::get('preview-proposal/{id}','General\PengajuanProposalController@previewproposal')->name('preview-proposal');
Route::get('/in/{slug}', array('as' => 'page.show', 'uses' => 'General\PengajuanProposalController@showQR'));
Route::get('preview-laporan-proposal/{id}','General\LaporanProposalController@previewlaporan')->name('preview-laporan-proposal');
Route::get('/report/{slug}','General\LaporanProposalController@qrlaporan');

/* 
|---------------------------------------------
| All routes for mahasiswa
|---------------------------------------------
*/

Route::group(['middleware' => 'auth:mahasiswa'], function () {
    Route::view('/mahasiswa', 'dashboard.mahasiswa-dashboard')->name('dashboard-mahasiswa');
});

/* 
|---------------------------------------------
| All routes for dosen
|---------------------------------------------
*/

Route::group(['middleware' => 'auth:dosen'], function () {
    Route::view('/dosen', 'dashboard.dosen-dashboard')->name('dashboard-dosen');
});

/* 
|---------------------------------------------
| All routes for mahasiswa and dekan
|---------------------------------------------
*/
Route::group(['middleware' => 'auth:mahasiswa,dosen'], function () {
    Route::resource('submission-of-proposal', 'General\PengajuanProposalController');
    Route::get('/proposal-baru','General\PengajuanProposalController@tampilkanWizard')->name('tampilan-proposal-baru');
    Route::post('insert-proposal-baru','General\PengajuanProposalController@insertProposal')->name('insert-proposal');
    Route::get('list-faculties/{id}','General\PengajuanProposalController@faculties')->name('list-faculties');
    Route::get('check-status','General\PengajuanProposalController@checkstatus')->name('check-status-proposal');
    Route::post('update-peganjuan-sarpras','General\PengajuanProposalController@updatepengajuan')->name('update-peganjuan-sarpras');
    Route::get('proposal-report/{id}','General\LaporanProposalController@indexlaporan')->name('index-laporan');
    Route::post('insert-laporan-proposal','General\LaporanProposalController@insertLaporanProposal')->name('insert-laporan-proposal');
    Route::get('my-report','General\LaporanProposalController@laporansaya')->name('my-report');
    Route::delete('delete-my-report','General\LaporanProposalController@hapuslaporan')->name('delete-my-report');
});

/* 
|---------------------------------------------
| All routes for dekan
|---------------------------------------------
*/

Route::group(['middleware' => 'auth:dekan'], function () {
    Route::view('/dekan', 'dashboard.dekan-dashboard')->name('dashboard-dekan');
    Route::get('data-dash-dekan','AdminPage\DataUser\DataDekanController@dashDekan')->name('data-dash-dekan');
    Route::resource('page-data-proposal', 'DekanPage\DataProposalController');
    Route::get('rencana-anggaran-proposal','DekanPage\DataProposalController@rencana')->name('rencana-anggaran-proposal');
    Route::post('dean-approval-y','DekanPage\DataProposalController@approvalDeanY')->name('dean-approval-y');
    Route::post('dean-approval-n','DekanPage\DataProposalController@approvalDeanN')->name('dean-approval-n');
});

/* 
|---------------------------------------------
| All routes for rektorat
|---------------------------------------------
*/

Route::group(['middleware' => 'auth:rektorat'], function () {
    Route::get('/rektorat', 'RektoratPage\DashboardController@index')->name('dashboard-rektorat');
    Route::post('approval-n','RektoratPage\DashboardController@approvalN')->name('approval-n');
    Route::post('approval-y','RektoratPage\DashboardController@approvalY')->name('approval-y');
    Route::get('index-hal-laporan','RektoratPage\DashboardController@indexlaporan')->name('index-hal-laporan');
    Route::post('laporan-selesai','RektoratPage\DashboardController@selesailaporan')->name('laporan-selesai');
});
