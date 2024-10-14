<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('auth.homepage');
})->name('base');

Auth::routes();


Route::post('login','Auth\AuthPegawaiController@postLogin')->name('postLogin');
Route::get('logout','Auth\AuthPegawaiController@logout')->name('logout');

/* 
|---------------------------------------------
| All routes for admin
|---------------------------------------------
*/

Route::get('/homepage-ui','HomeController@uiModul')->name('ui-modul');

// Route::view('/home', 'home')->middleware('auth');
Route::get('/home', 'HomeController@index')->middleware('auth:pegawai,mahasiswa')->name('home');
Route::post('/ubah-peran', function(Request $request){
    $peran = $request->peran;
    if ($peran) {
        session(['selected_peran' => $peran]);
        return response()->json(['message' => 'Peran berhasil diubah']);
    } 
})->name('ubah-peran');

# Another Apps
Route::redirect('/simak-uvers-webpage', 'https://sia.uvers.ac.id/')->name('simak-uvers');


Route::middleware(['auth:pegawai','verified', 'cekrole:SADM,ADU'])->group(function() {
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
    Route::post('/y-selected-id','AdminPage\DataProposalController@validYAll')->name('y-selected-id');

    Route::resource('data-jabatan', 'Master\JabatanController');
    Route::resource('data-jabatan-akademik', 'Master\JabatanAkademikController');
    Route::get('daftar-fakultas/{id}','Master\JabatanAkademikController@faculties')->name('daftar-fakultas');
    Route::resource('data-jabatan-pegawai', 'Master\JabatanPegawaiController');

    Route::resource('data-pegawai', 'AdminPage\DataUser\DataPegawaiController');
    Route::post('import-pegawai','AdminPage\DataUser\DataPegawaiController@importDataPegawai')->name('import-data-pegawai');
});

/* 
|---------------------------------------------
| All routes for mahasiswa
|---------------------------------------------
*/

/* 
|---------------------------------------------
| All routes for dosen
|---------------------------------------------
*/

Route::middleware(['auth:pegawai','verified', 'cekrole:DSN'])->group(function() {
    Route::view('/dosen', 'dashboard.dosen-dashboard')->name('dashboard-dosen');
});

/* 
|---------------------------------------------
| All routes for all general 
|---------------------------------------------
*/
Route::middleware(['auth:pegawai,mahasiswa','verified'])->group(function() {
    Route::view('/mahasiswa', 'dashboard.mahasiswa-dashboard')->name('dashboard-mahasiswa');
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

    Route::get('preview-proposal/{id}','General\PengajuanProposalController@previewproposal')->name('preview-proposal');
    Route::get('/in/{slug}', array('as' => 'page.show', 'uses' => 'General\PengajuanProposalController@showQR'));
    Route::get('preview-laporan-proposal/{id}','General\LaporanProposalController@previewlaporan')->name('preview-laporan-proposal');
    Route::get('/report/{slug}','General\LaporanProposalController@qrlaporan');
    Route::get('view-lampiran','General\PengajuanProposalController@viewlampiran')->name('view-lampiran-proposal');
});

/* 
|---------------------------------------------
| All routes for dekan
|---------------------------------------------
*/

Route::middleware(['auth:pegawai','verified', 'cekrole:DKN'])->group(function() {
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

Route::middleware(['auth:pegawai','verified', 'cekrole:WRAK,WRSDP'])->group(function() {
    Route::get('/rektorat', 'RektoratPage\DashboardController@index')->name('dashboard-rektorat');
    Route::post('approval-n','RektoratPage\DashboardController@approvalN')->name('approval-n');
    Route::post('approval-y','RektoratPage\DashboardController@approvalY')->name('approval-y');
    Route::get('index-hal-laporan','RektoratPage\DashboardController@indexlaporan')->name('index-hal-laporan');
    Route::post('laporan-selesai','RektoratPage\DashboardController@selesailaporan')->name('laporan-selesai');
});
