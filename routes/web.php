<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('auth.homepage');
})->name('base');

Auth::routes();
Route::post('login','Auth\AuthPegawaiController@postLogin')->name('postLogin');
Route::get('logout','Auth\AuthPegawaiController@logout')->name('logout');
Route::group(['middleware' => ['auth:pegawai', 'check.email']], function (){
    Route::get('/home', 'HomeController@index')->name('home');
});
Route::post('/ubah-peran', function(Request $request){
    $peran = $request->peran;
    if ($peran) {
        session(['selected_peran' => $peran]);
        return response()->json(['message' => 'Peran berhasil diubah']);
    } 
})->name('ubah-peran');

Route::get('/fpku/{slug}','General\UndanganFpkuController@qrundangan');
Route::get('/fpku-rep/{slug}','General\LaporanFpkuController@qrlaporan');
Route::get('/in/{slug}', array('as' => 'page.show', 'uses' => 'General\PengajuanProposalController@showQR'));
Route::get('/report/{slug}','General\LaporanProposalController@qrlaporan');
Route::get('preview-undangan-fpku/{id}','General\UndanganFpkuController@previewUndangan')->name('preview-undangan'); # Public
Route::get('preview-proposal/{id}','General\PengajuanProposalController@previewproposal')->name('preview-proposal'); # Public
Route::get('preview-laporan-fpku/{id}','General\LaporanFpkuController@previewlaporanfpku')->name('preview-laporan-fpku'); # Public
Route::get('preview-laporan-proposal/{id}','General\LaporanProposalController@previewlaporan')->name('preview-laporan-proposal'); # Public

# Another Apps
Route::redirect('/simak-uvers-webpage', 'https://sia.uvers.ac.id/')->name('simak-uvers');

Route::middleware(['auth:pegawai','verified', 'cekrole:SADM,ADU'])->group(function() {
    Route::get('data-dash-admin','AdminPage\DataUser\DataAdminController@dashAdmin')->name('data-dash-admin');
    Route::resource('data-jenis-kegiatan', 'General\JenisKegiatanController');
    Route::resource('data-fakultas-biro', 'General\DataFakultasBiroController');
    Route::resource('data-prodi-biro', 'General\DataProdiBiroController');
    Route::resource('data-proposal', 'AdminPage\DataProposalController');
    Route::resource('data-jabatan', 'Master\JabatanController');
    Route::resource('data-jabatan-pegawai', 'Master\JabatanPegawaiController');
    Route::resource('data-pegawai', 'AdminPage\DataUser\DataPegawaiController');
    Route::resource('data-fpku','General\DataFpkuController');
    Route::resource('validator-proposal','AdminPage\ValidatorProposalController');
    Route::resource('handle-proposal','AdminPage\HandleProposalController');
    Route::resource('tahun-akademik','General\TahunAkademikController');
    Route::get('validasi-proposal','AdminPage\DataProposalController@validasi')->name('validasi-proposal');
    Route::post('valid-y','AdminPage\DataProposalController@validY')->name('valid-y');
    Route::post('valid-n','AdminPage\DataProposalController@validN')->name('valid-n');
    Route::post('import-dosen','AdminPage\DataUser\DataDosenController@importDataDosen')->name('import-data-dosen');
    Route::post('/y-selected-id','AdminPage\DataProposalController@validYAll')->name('y-selected-id');
    Route::post('import-pegawai','AdminPage\DataUser\DataPegawaiController@importDataPegawai')->name('import-data-pegawai');
    Route::post('broadcast-undangan','General\DataFpkuController@broadcastUndangan')->name('broadcast-undangan');
    Route::get('check-jabatan-akademik-user','Master\JabatanAkademikController@checkjabatanakademik')->name('check-jabatan-akademik-user');
    Route::get('check-jabatan-pegawai','Master\JabatanPegawaiController@checkjabatan')->name('check-jabatan-pegawai');
    Route::post('reset-pass-pegawai','AdminPage\DataUser\DataPegawaiController@resetPass')->name('reset-pass-pegawai');
    Route::post('switch-period','General\TahunAkademikController@switchPeriode')->name('change-period-status');
});

/* 
|---------------------------------------------
| All routes for all general 
|---------------------------------------------
*/
Route::middleware(['auth:pegawai','verified'])->group(function() {
    Route::resource('submission-of-proposal', 'General\PengajuanProposalController');
    Route::resource('form-rkat', 'Master\FormRkatController');
    Route::get('/proposal-baru','General\PengajuanProposalController@tampilkanWizard')->name('tampilan-proposal-baru');
    Route::post('insert-proposal-baru','General\PengajuanProposalController@insertProposal')->name('insert-proposal');
    Route::get('list-faculties/{id}','General\PengajuanProposalController@faculties')->name('list-faculties');
    Route::get('check-status','General\PengajuanProposalController@checkstatus')->name('check-status-proposal');
    
    
    Route::get('proposal-report/{id}','General\LaporanProposalController@indexlaporan')->name('index-laporan');
    Route::post('insert-laporan-proposal','General\LaporanProposalController@insertLaporanProposal')->name('insert-laporan-proposal');
    Route::get('my-report','General\LaporanProposalController@laporansaya')->name('my-report');
    Route::delete('delete-my-report','General\LaporanProposalController@hapuslaporan')->name('delete-my-report');
    Route::get('view-lampiran','General\PengajuanProposalController@viewlampiran')->name('view-lampiran-proposal');
    Route::get('check-anggaran','General\PengajuanProposalController@checkanggaran')->name('check-anggaran-proposal');
    // Route::post('update-anggaran-item','General\PengajuanProposalController@updateAnggaranItem')->name('update-anggaran-item');
    // Route::delete('delete-item-anggaran','General\PengajuanProposalController@hapusItemAnggaran')->name('delete-item-anggaran');
    Route::get('check-informasi','General\PengajuanProposalController@checkinformasi')->name('check-informasi-proposal');
    Route::post('update-nama-kegiatan','General\PengajuanProposalController@updateNamaKegiatan')->name('update-nama-kegiatan');
    Route::post('update-pendahuluan','General\PengajuanProposalController@updatePendahuluan')->name('update-pendahuluan');
    Route::post('update-tujuan-manfaat','General\PengajuanProposalController@updateTujuanManfaat')->name('update-tujuan-manfaat');
    Route::post('update-tglevent','General\PengajuanProposalController@updateTanggalEvent')->name('update-tglevent');
    Route::post('update-lokasitempat','General\PengajuanProposalController@updateLokasiTempat')->name('update-lokasitempat');
    Route::post('update-peserta','General\PengajuanProposalController@updatePeserta')->name('update-peserta');
    Route::post('update-detilkegiatan','General\PengajuanProposalController@updateDetilKegiatan')->name('update-detilkegiatan');
    Route::post('update-penutup','General\PengajuanProposalController@updatePenutup')->name('update-penutup');
    Route::post('resubmit-proposal','General\PengajuanProposalController@submitUlangProposal')->name('re-submit-proposal');
    Route::get('undangan-fpku','General\UndanganFpkuController@index')->name('undangan-fpku');  
    Route::get('index-laporan-fpku','General\LaporanFpkuController@indexFpku')->name('index-laporan-fpku');
    Route::get('buat-laporan-fpku/{id}','General\LaporanFpkuController@buatLaporan')->name('buat-laporan-fpku');
    Route::post('insert-laporan-fpku','General\LaporanFpkuController@insertLaporanFpku')->name('insert-laporan-fpku');
    Route::get('view-lampiran-fpku','General\LaporanFpkuController@viewlampiran')->name('view-lampiran-fpku');    
    Route::get('view-lampiran-data-fpku','General\DataFpkuController@viewlampiranfpku')->name('view-lampiran-data-fpku');
    Route::get('user-profile','AdminPage\DataUser\DataPegawaiController@profile')->name('profile');
    Route::get('change-password', 'AdminPage\DataUser\DataPegawaiController@getPass');
    Route::post('change-password', 'AdminPage\DataUser\DataPegawaiController@postPass')->name('change-password');
    Route::post('update-email-address','AdminPage\DataUser\DataPegawaiController@updateEmail')->name('update-email-address');
    Route::post('arsip-proposal','General\PengajuanProposalController@arsipProposal')->name('arsip-proposal');
    Route::post('resubmit-anggaran','General\PengajuanProposalController@submitUlangAnggaran')->name('re-submit-anggaran');
    Route::get('view-lampiran-laporan-fpku','RektoratPage\DashboardController@viewlampiranLaporanFpku')->name('view-lampiran-laporan-fpku');
    Route::delete('delete-laporan-fpku','General\LaporanFpkuController@hapusLaporanFpku')->name('delete-laporan-fpku');
    Route::get('data-form-rkat','Master\FormRkatController@dataForm')->name('data-form-rkat');
    Route::get('lihat-detail-anggaran','DekanPage\DataProposalController@lihatDetailAnggaran')->name('lihat-detail-anggaran');
    Route::get('lihat-detail-realisasi-anggaran','DekanPage\LaporanProposalController@lihatDetailRealisasiAnggaran')->name('lihat-detail-realisasi-anggaran');
    Route::get('lihat-detail-anggaran-fpku','RektoratPage\DashboardController@lihatDetailAnggaran')->name('lihat-detail-anggaran-fpku');
    Route::get('get-recent-peran','Auth\PeranController@getRecentPeran')->name('get-recent-peran');

    # Route Revisi Laporan Proposal
    Route::get('check-lap-informasi','General\LaporanProposalController@checkInformasi')->name('check-informasi-lap-proposal');
    Route::post('update-hasil-kegiatan','General\LaporanProposalController@updateHasilKegiatan')->name('update-hasil-kegiatan');
    Route::post('update-catatan-kegiatan','General\LaporanProposalController@updateCatatanKegiatan')->name('update-catatan-kegiatan');
    Route::post('update-penutup-laporan-proposal','General\LaporanProposalController@updatePenutup')->name('update-penutup-laporan-proposal');

    # Route Revisi Realisasi Anggaran Laporan Proposal
    Route::get('index-revisi-anggaran-laporan-proposal/{id}','General\UpdateItemController@indexRevisiAnggaranLaporanProposal')->name('index-revisi-anggaran-laporan-proposal');
    Route::get('page-revisi-anggaran-laporan-proposal/{id}','General\UpdateItemController@pageRevisiAnggaranLaporanProposal')->name('page-revisi-anggaran-laporan-proposal');
    Route::post('page-revisi-anggaran-laporan-proposal','General\UpdateItemController@simpanAnggaranLaporanProposal')->name('revisi-anggaran-laporan-proposal-store');
    Route::post('edit-item-anggaran-laporan-proposal','General\UpdateItemController@editItemAnggaranLaporanProposal')->name('edit-item-anggaran-laporan-proposal');
    Route::delete('delete-item-anggaran-laporan-proposal','General\UpdateItemController@hapusItemAnggaranLaporanProposal')->name('delete-item-anggaran-laporan-proposal');

    Route::get('check-done-revision','General\LaporanProposalController@doneRevision')->name('check-done-revision');
    Route::post('confirm-done-revision','General\LaporanProposalController@confirmDoneRevision')->name('confirm-done-revision');

    # Route update items
    Route::get('index-update-sarpras/{id}','General\UpdateItemController@indexUpdateSarpras')->name('index-update-sarpras');
    Route::get('page-update-sarpras/{id}','General\UpdateItemController@pageUpdateSarpras')->name('page-update-sarpras');
    Route::post('page-update-sarpras','General\UpdateItemController@simpanSarpras')->name('update-sarpras-store');
    Route::post('edit-item-sarpras','General\UpdateItemController@editItemSarpras')->name('edit-item-sarpras');
    Route::delete('delete-item-sarpras','General\UpdateItemController@hapusItemSarpras')->name('delete-item-sarpras');

    Route::get('index-update-anggaran/{id}','General\UpdateItemController@indexUpdateAnggaran')->name('index-update-anggaran');
    Route::get('page-update-anggaran/{id}','General\UpdateItemController@pageUpdateAnggaran')->name('page-update-anggaran');
    Route::post('page-update-anggaran','General\UpdateItemController@simpanAnggaran')->name('update-anggaran-store');
    Route::post('edit-item-anggaran','General\UpdateItemController@editItemAnggaran')->name('edit-item-anggaran');
    Route::delete('delete-item-anggaran','General\UpdateItemController@hapusItemAnggaran')->name('delete-item-anggaran');

    Route::get('check-done-revision-proposal','General\PengajuanProposalController@doneRevision')->name('check-done-revision-proposal');
    Route::post('confirm-done-revision-proposal','General\PengajuanProposalController@confirmSubmitUlang')->name('confirm-done-revision-proposal');

    Route::get('view-lampiran-laporan-proposal','General\LaporanProposalController@viewlampiran')->name('view-lampiran-laporan-proposal');

});

/* 
|---------------------------------------------
| All routes for dekan
|---------------------------------------------
*/

Route::middleware(['auth:pegawai','verified', 'cekrole:PEG,UCC,RKT'])->group(function() {
    Route::view('/dekan', 'dashboard.dekan-dashboard')->name('dashboard-dekan');
    Route::get('data-dash-dekan','AdminPage\DataUser\DataDekanController@dashDekan')->name('data-dash-dekan');
    Route::get('data-dash-dekan','AdminPage\DataUser\DataDekanController@dashDekan')->name('data-dash-dekan');
    Route::resource('page-data-proposal', 'DekanPage\DataProposalController');
    Route::get('rencana-anggaran-proposal','DekanPage\DataProposalController@rencana')->name('rencana-anggaran-proposal');
    Route::post('dean-approval-y','DekanPage\DataProposalController@approvalDeanY')->name('dean-approval-y');
    Route::post('dean-approval-n','DekanPage\DataProposalController@approvalDeanN')->name('dean-approval-n');
    Route::resource('page-laporan-proposal','DekanPage\LaporanProposalController');
    Route::post('dean-report-approval-y','DekanPage\LaporanProposalController@approvalDeanY')->name('dean-report-approval-y');
    Route::post('dean-report-approval-n','DekanPage\LaporanProposalController@approvalDeanN')->name('dean-report-approval-n');
    Route::get('status-sarpras','DekanPage\DataProposalController@statusSarpras')->name('status-sarpras');
});

/* 
|---------------------------------------------
| All routes for rektorat
|---------------------------------------------
*/

Route::middleware(['auth:pegawai','verified', 'cekrole:WAREK,RKT'])->group(function() {
    Route::get('/rektorat', 'RektoratPage\DashboardController@index')->name('dashboard-rektorat');
    Route::post('approval-n','RektoratPage\DashboardController@approvalN')->name('approval-n');
    Route::post('approval-y','RektoratPage\DashboardController@approvalY')->name('approval-y');
    Route::post('tambah-delegasi-proposal','RektoratPage\DashboardController@tambahDelegasiProposal')->name('tambah-delegasi-proposal');
    Route::get('index-hal-laporan','RektoratPage\DashboardController@indexlaporan')->name('index-hal-laporan');
    Route::post('laporan-selesai','RektoratPage\DashboardController@selesailaporan')->name('laporan-selesai');
    Route::get('rundanganfpku','RektoratPage\DashboardController@indexUndanganFpku')->name('rundanganfpku');
    Route::post('confirmundanganfpku','RektoratPage\DashboardController@confirmUndanganFpku')->name('confirmundanganfpku');
    Route::get('rlaporanfpku','RektoratPage\DashboardController@indexLaporanFpku')->name('rlaporanfpku');
    Route::post('confirmlaporanfpku','RektoratPage\DashboardController@confirmLaporanFpku')->name('confirmlaporanfpku');
    Route::post('ignorelaporanfpku','RektoratPage\DashboardController@ignoreLaporanFpku')->name('ignorelaporanfpku');
    Route::post('r-report-approval-n','RektoratPage\DashboardController@approvalRektorN')->name('r-report-approval-n');
    Route::get('rform-rkat','RektoratPage\FormRkatController@index')->name('index-form-rkat');
    Route::post('rkat-approval-y','RektoratPage\FormRkatController@approvalY')->name('rkat-approval-y');
    Route::post('rkat-approval-n','RektoratPage\FormRkatController@approvalN')->name('rkat-approval-n');
});

Route::middleware(['auth:pegawai','verified', 'cekrole:SADM,WAREK,RKT,PEGS'])->group(function() {
    Route::get('lihat-history-delegasi','RektoratPage\DashboardController@lihatHistoryDelegasi')->name('lihat-history-delegasi');
    Route::get('lihat-history-delegasi-proposal','RektoratPage\DashboardController@lihatHistoryDelegasiProposal')->name('lihat-history-delegasi-proposal');
});

Route::middleware(['auth:pegawai','verified', 'cekrole:WAREK,RKT,SADM'])->group(function(){
    Route::get('index-export-proposal','General\LaporanProposalController@indexExportProposal')->name('index-export-proposal');
    Route::get('show-data-proposal-html/{year}/{lembaga}','General\LaporanProposalController@showDataProposalHtml')->name('show-data-proposal-html');
    Route::get('download-proposal-excel/{year}/{lembaga}','General\LaporanProposalController@downloadProposalExcel')->name('download-proposal-excel');
    Route::get('index-export-fpku','General\LaporanFpkuController@indexExportFpku')->name('index-export-fpku');
    Route::get('show-data-fpku-html/{year}','General\LaporanFpkuController@showDataFpkuHtml')->name('show-data-fpku-html');
    Route::get('download-fpku-excel/{year}','General\LaporanFpkuController@downloadFpkuExcel')->name('download-fpku-excel');
    Route::get('index-monitoring-proposals','RektoratPage\DashboardController@indexMonitoringProposal')->name('index-monitoring-proposals');
    Route::get('index-monitoring-laporan-proposals','RektoratPage\DashboardController@indexMonitoringLaporanProposal')->name('index-monitoring-laporan-proposals');
    Route::get('index-monitoring-fpkus','RektoratPage\DashboardController@indexMonitoringFpku')->name('index-monitoring-fpkus');
    Route::get('index-monitoring-laporan-fpkus','RektoratPage\DashboardController@indexMonitoringLaporanFpku')->name('index-monitoring-laporan-fpkus');
    Route::get('status-monitoring-sarpras','RektoratPage\DashboardController@statusSarpras')->name('status-monitoring-sarpras');
    Route::post('import-rkat','Master\FormRkatController@importDataRkat')->name('import-data-rkat');
});
