<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\JenisKegiatan;
use App\Models\General\DataFakultasBiro;
use App\Models\General\DataPengajuanSarpras;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\TahunAkademik;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Setting\Dekan;
use App\Setting\Rektorat;
use Illuminate\Support\Facades\Storage;
use Redirect;
use File;
use Auth;
use DB; use URL;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\Jabatan;
use App\Models\Master\Pegawai;
use App\Models\Master\HandleProposal;
use App\Models\Master\FormRkat;
use App\Models\Master\ValidatorProposal;
use Illuminate\Support\Facades\Session;

class PengajuanProposalController extends Controller
{
    public function index(Request $request)
    {
        if($request->status == '' || $request->status == 'all'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_pengaju')
                ->where([['proposals.user_id',Auth::user()->user_id],['proposals.is_archived',0]])
                ->orderBy('proposals.id','DESC')
                ->get();
        }
        if($request->status == 'pending'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_pengaju')
                ->where([['proposals.user_id',Auth::user()->user_id],['proposals.is_archived',0],['status_proposals.status_approval',1]])
                ->orderBy('proposals.id','DESC')
                ->get();
        }
        if($request->status == 'accepted'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_pengaju')
                ->where([['proposals.user_id',Auth::user()->user_id],['proposals.is_archived',0],['status_proposals.status_approval',5]])
                ->orderBy('proposals.id','DESC')
                ->get();
        }
        if($request->status == 'denied'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_pengaju')
                ->where([['proposals.user_id',Auth::user()->user_id],['proposals.is_archived',0],['status_proposals.status_approval',4]])
                ->orderBy('proposals.id','DESC')
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $query = DB::table('status_proposals')->select('status_approval','id_proposal')->where('id_proposal','=',$data->id)->get();
                if($query){
                    foreach($query as $get){                
                        if($get->status_approval == 1){
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-informasi" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-show me-2 text-success"></i>Lihat Isi Data Proposal</a>
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-primary"></i>Lihat Sarpras</a>
                                    <a class="dropdown-item lihat-anggaran" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-money me-2 text-info"></i>Lihat Anggaran</a>
                                    <a class="dropdown-item delete" name="delete" id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-trash me-2 text-danger"></i>Hapus Proposal</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 2){
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-informasi" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-show me-2 text-success"></i>Lihat Isi Data Proposal</a>
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2" text-primary></i>Lihat Sarpras</a>
                                    <a class="dropdown-item arsip-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-warning"></i>Batalkan Proposal</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 3) {
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-primary"></i>Lihat Sarpras</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 4) {
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-informasi" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-show me-2 text-success"></i>Lihat Isi Data Proposal</a>
                                    <a class="dropdown-item lihat-anggaran" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-money me-2 text-info"></i>Lihat Anggaran</a>
                                    <a class="dropdown-item arsip-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-warning"></i>Batalkan Proposal</a>
                                    </div>
                                </div>';
                        } else {
                            return '';
                        }
                    }
                } else {
                    return 'x';
                }
                
            })->addColumn('status', function($data){
                $btn = $this->statusProposal($data->id);                
                return $btn;
            })->addColumn('preview_with_name', function($data){                
                return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal">'.$data->nama_kegiatan.'</a>';
            })->addColumn('lampiran', function($data){
                $ifExist = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($ifExist > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="btn btn-outline-info btn-sm v-lampiran"><i class="bx bx-xs bx-file"></i></a>';
                } else {
                    return '<i class="bx bx-minus-circle text-secondary"></i>';
                }
            })
            ->rawColumns(['action','preview_with_name','status','lampiran'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $checkLap = DB::table('status_laporan_proposals')->rightJoin('proposals','proposals.id','=','status_laporan_proposals.id_laporan_proposal')
            ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id','status_laporan_proposals.status_approval AS slp','status_proposals.status_approval AS status_approval')
            ->where('proposals.user_id',Auth::user()->user_id)
            ->whereIn('status_proposals.status_approval',[1,3,5]) # Check status approval to activate new proposal button
            ->get();
        $dataFormRkat = FormRkat::all();
        return view('general.pengajuan-proposal.index', compact('datas','checkLap','dataFormRkat'));
    }

    protected function statusProposal($id)
    {
        $query = DB::table('status_proposals')->select('status_approval','keterangan_ditolak')->where('id_proposal','=',$id)->get();
        if($query){
            foreach($query as $data){                
                if($data->status_approval == 2){
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak Atasan</span><span class="badge bg-danger badge-notifications">Cek alasan ditolak</span></a>';
                } elseif($data->status_approval == 3) {
                    return '<small><i class="text-success">ACC Atasan</i></small>';
                } elseif($data->status_approval == 4) {
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Pending Rektorat&nbsp;</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 5) {
                    return '<small><i class="text-success">ACC Rektorat</i></small>';
                } else {
                    return '<small><i class="text-secondary">Menunggu Validasi</i></small>';
                }
            }
        } else {
            return 'x';
        }
    }

    public function tampilkanWizard(Request $request)
    {
        $getJenisKegiatan = JenisKegiatan::all();
        $getFakultasBiro = DataFakultasBiro::select('nama_fakultas_biro','id')->get();
        $getTahunAkademik = TahunAkademik::select('id','year','is_active')->where('is_active',1)->first();
        return view('general.pengajuan-proposal.wizard-proposal', compact('getJenisKegiatan','getFakultasBiro','getTahunAkademik'));
    }

    public function faculties($id)
    {
        $datas = DataFakultasBiro::leftJoin('data_prodi_biros','data_prodi_biros.id_fakultas_biro','=','data_fakultas_biros.id')
            ->where('data_prodi_biros.id_fakultas_biro',$id)
            ->pluck('data_prodi_biros.nama_prodi_biro','data_prodi_biros.id');
        return json_encode($datas);
    }

    public function insertProposal(Request $request)
    {
        
        $request->validate([
            'id_jenis_kegiatan' => 'required',
            'nama_kegiatan'     => 'required',
            'pendahuluan'       => 'required',
            'tujuan_manfaat'    => 'required',
            'tgl_event'         => 'required',
            'peserta'           => 'required',
            'detil_kegiatan'    => 'required',
            'penutup'           => 'required',
            'lokasi_tempat'     => 'required',
            'berkas.*'          => 'file|mimes:pdf,doc,docx|max:2048'
        ],[
            'id_jenis_kegiatan.required'    => 'Anda belum memilih kategori proposal', 
            'nama_kegiatan.required'        => 'Anda belum menginput nama kegiatan', 
            'pendahuluan.required'          => 'Anda belum menginput pendahuluan', 
            'tujuan_manfaat.required'       => 'Anda belum menginput tujuan manfaat', 
            'tgl_event.required'            => 'Anda belum menginput tgl event', 
            'peserta.required'              => 'Anda belum menginput peserta', 
            'detil_kegiatan.required'       => 'Anda belum menginput detil kegiatan', 
            'penutup.required'              => 'Anda belum menginput penutup',
            'lokasi_tempat.required'        => 'Anda belum menginput lokasi kegiatan',
            'berkas.*.max'                  => 'Ukuran berkas tidak boleh melebihi 2MB', 
            'berkas.*.mimes'                => 'File harus berjenis (pdf atau docx)',
        ]); 
        
        $getIdTahunAkademik = TahunAkademik::where('is_active',1)->select('id')->first();

        $post = Proposal::updateOrCreate(['id' => $request->id],
                [
                    'id_tahun_akademik'     => $getIdTahunAkademik->id,
                    'id_jenis_kegiatan'     => $request->id_jenis_kegiatan,
                    'id_form_rkat'          => $request->pilihan_rkat,
                    'user_id'               => Auth::user()->user_id,
                    'id_fakultas_biro'      => $request->id_fakultas_biro,
                    'id_prodi_biro'         => $request->id_prodi_biro,
                    'nama_kegiatan'         => $request->nama_kegiatan,
                    'pendahuluan'           => $request->pendahuluan,
                    'tujuan_manfaat'        => $request->tujuan_manfaat,
                    'tgl_event'             => $request->tgl_event,
                    'lokasi_tempat'         => $request->lokasi_tempat,
                    'peserta'               => $request->peserta,
                    'detil_kegiatan'        => $request->detil_kegiatan,
                    'penutup'               => $request->penutup,
                    'validasi'              => 1,
                ]);        

        $latest_id = Proposal::latest()->first();
        $latest = $latest_id ? $latest_id->id : '1';                

        # Insert data into data_pengajuan_sarpras
        $dataSet = [];
        foreach ($request->kolom as $sarpras) {
            // Validasi setiap inputan
            if (!empty($sarpras['tgl_kegiatan']) && 
                !empty($sarpras['sarpras_item']) && 
                !empty($sarpras['jumlah'])) {

                // Jika sumber_sarpras kosong, default ke '1'
                $sumber_dana = !empty($sarpras['sumber_sarpras']) ? $sarpras['sumber_sarpras'] : 1;

                // Tambahkan ke $dataSet
                $dataSet[] = [
                    'id_proposal'   => $latest,
                    'tgl_kegiatan'  => $sarpras['tgl_kegiatan'],
                    'sarpras_item'  => $sarpras['sarpras_item'],
                    'jumlah'        => $sarpras['jumlah'],
                    'sumber_dana'   => $sumber_dana,
                    'status'        => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        // Insert data jika $dataSet tidak kosong
        if (!empty($dataSet)) {
            DataPengajuanSarpras::insert($dataSet);
        }

        # Insert data into data_rencana_anggaran
        $dataRenang = [];
        foreach($request->rows as $k => $renang){
            if  (!empty($renang['item']) && 
                !empty($renang['biaya_satuan']) && 
                !empty($renang['quantity']) && 
                !empty($renang['frequency'])) {

                    // Jika sumber_anggaran kosong, default ke '1'
                $sumber_dana_anggaran = !empty($sarpras['sumber_anggaran']) ? $sarpras['sumber_anggaran'] : 1;

                $dataRenang[] = [
                    'id_proposal'   => $latest,
                    'item'          => $renang['item'],
                    'biaya_satuan'  => $renang['biaya_satuan'],
                    'quantity'      => $renang['quantity'],
                    'frequency'     => $renang['frequency'],
                    'sumber_dana'   => $sumber_dana_anggaran,
                    'status'        => 1,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }
        }
        if (!empty($dataRenang)) {
            $post = DataRencanaAnggaran::insert($dataRenang);
        }

        $post = DB::table('status_proposals')->insert(
            [
                'id_proposal'       => $latest,
                'status_approval'   => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

        # Insert data into lampiran_proposals
        if($request->berkas != ''){
            $fileNames = [];
            foreach($request->berkas as $file){
                $fileName = md5(time().'_'.Auth::user()->user_id).$file->getClientOriginalName();
                $file->move(public_path('uploads-lampiran/lampiran-proposal'),$fileName);
                $fileNames[] = 'uploads-lampiran/lampiran-proposal/'.$fileName;
            }

            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){
                if(!empty($request->nama_berkas[$x]) && !empty($fileNames[$x]) && !empty($request->keterangan[$x])) {
                    $insertData[] = [
                        'id_proposal'   => $latest,
                        'nama_berkas'   => $request->nama_berkas[$x],
                        'berkas'        => $fileNames[$x],
                        'link_gdrive'   => '',
                        'keterangan'    => $request->keterangan[$x],
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }
            }
            if (!empty($insertData)) {
                $post = DB::table('lampiran_proposals')->insert($insertData);
            }
        } else {
            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){
                if(!empty($request->nama_berkas[$x]) && !empty($request->link_gdrive[$x]) && !empty($request->keterangan[$x])) {
                    $insertData[] = [
                        'id_proposal'   => $latest,
                        'nama_berkas'   => $request->nama_berkas[$x],
                        'berkas'        => '',
                        'link_gdrive'   => $request->link_gdrive[$x],
                        'keterangan'    => $request->keterangan[$x],
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }
            }
            if (!empty($insertData)) {
                $post = DB::table('lampiran_proposals')->insert($insertData);
            }
            return redirect()->route('submission-of-proposal.index');
        }

        # get Email Dekan
        $emailDekanBiro = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->where([['jabatans.kode_jabatan','=','PEG'],['jabatan_pegawais.id_fakultas_biro',$request->id_fakultas_biro]])
            ->select('pegawais.email')
            ->first();
        # get Email Admin Umum
        // $emailADU = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
        //     ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
        //     ->where('jabatans.kode_jabatan','=','ADU')
        //     ->select('pegawais.email')
        //     ->first();
        $listEmail = strtolower(['bennyalfian@uvers.ac.id',$emailDekanBiro->email]);
        $getPegawaiName = Pegawai::select('nama_pegawai')->where('user_id',Auth::user()->user_id)->first();

        if (isset($listEmail) && count($listEmail) > 0){
            $isiData = [
                'name' => 'Pengajuan Proposal Kegiatan oleh '.$getPegawaiName->nama_pegawai.'',
                'body' => 'Anda memiliki pengajuan proposal kegiatan: '.$request->nama_kegiatan.'',
            ];
            Mail::to($listEmail)->send(new KirimEmail($isiData));
        } else {
            return 'No valid email addresses found';
        }  
        
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Proposal::where('id',$id)->delete(); 
        DB::table('lampiran_proposals')->where('id_proposal',$id)->delete();    
        return response()->json($post);
    }

    public function checkstatus(Request $request)
    {
        $datas = DataPengajuanSarpras::where('id_proposal',$request->proposal_id)->get();
        
        if($datas->count() > 0) {
            $html = '<table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tgl Kegiatan</th>
                                <th>Sarpras Item</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody>';
                    foreach($datas as $no => $item){
                    $html .= '<tr>
                                <td>'.++$no.'</td>
                                <td>'.tanggal_indonesia($item->tgl_kegiatan).'</td>
                                <td>'.$item->sarpras_item.'</td>
                                <td>'.$item->jumlah.'</td>';
                                    if($item->status == '1'){
                                        $x = '<span class="badge bg-label-warning">Pending</span>';
                                    }else if($item->status == '2'){
                                        $x = '<span class="badge bg-label-success">Disetujui</span>';
                                    }elseif($item->status == '3'){
                                        $x = '<span class="badge bg-label-danger">Ditolak</span>';
                                    }else{
                                        $x = '<span class="badge bg-label-success">Disetujui</span>';
                                    }
                    $html .=    '<td>'.$x.'</td>';
                            if($item->status == '3'){
                                $html .= '<td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-alasan="'.$item->alasan.'" data-placement="bottom" title="Detil keterangan ditolak" data-original-title="Detil keterangan ditolak" class="alasan"><i class="bx bx-show-alt bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-tgl="'.$item->tgl_kegiatan.'" data-item="'.$item->sarpras_item.'" data-jumlah="'.$item->jumlah.'" data-sumber="'.$item->sumber_dana.'" data-placement="bottom" title="Edit data sarpras" data-original-title="Edit data sarpras" class="edit-post"><i class="bx bx-edit bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-post"><i class="bx bx-trash bx-xs"></i></a></td>';
                            } else {
                                $html .= '<td></td></tr>';
                            }
                    }
    
                    $html .= '</tbody>
                        </table>';
        } else {
            $html = '<table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tgl Kegiatan</th>
                                <th>Sarpras Item</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" style="text-align: center;">No data available in table</td>
                            </tr>
                        </tbody>
                        </table>';
        }
        return response()->json(['card' => $html]);
    }

    public function updatepengajuan(Request $request){
        $post = DataPengajuanSarpras::where('id',$request->e_sarpras_id)->update([
            'tgl_kegiatan'  => $request->e_tgl_kegiatan,
            'sarpras_item'  => $request->e_sarpras_item,
            'jumlah'        => $request->e_jumlah,
            'sumber_dana'   => $request->e_sumber,
            'status'        => 1,
            'alasan'        => '',
        ]);
        return response()->json($post);
    }

    public function hapusItem(Request $request)
    {
        $post = DataPengajuanSarpras::where('id',$request->id)->delete(); 
        return response()->json($post);
    }

    public function previewproposal($id)
    {
        $ID = decrypt($id);
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
            ->leftJoin('form_rkats','form_rkats.id','=','proposals.id_form_rkat')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user_dosen','form_rkats.kode_renstra')
            ->where('proposals.id',$ID)
            ->orderBy('proposals.id','DESC')
            ->get();

        $sarpras = DataPengajuanSarpras::where('id_proposal',$ID)->get();
        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $grandTotal = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();

        # Get and show the QRCode
        $getQR = DB::table('status_proposals')
            ->leftJoin('proposals','proposals.id','=','status_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->select('status_proposals.status_approval','status_proposals.generate_qrcode','pegawais.nama_pegawai AS nama_dosen')
            ->where([['status_proposals.id_proposal',$ID],['status_proposals.status_approval',5]])
            ->get();
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate('Proposal belum disetujui!'));

        # get lampiran
        $data_lampiran = DB::table('lampiran_proposals')->where('id_proposal',$ID)->get();

        foreach($datas as $r){ 
            # Get Pengusul according to proposal.user_id
            $getPengusul = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diusulkan_oleh')
                ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
                ->where('proposals.id','=',$r->id)
                ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                ->first();

            if($r->id_fakultas_biro != null){
                $getDiketahui = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
                    ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                    ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                    ->where('jabatan_pegawais.id_fakultas_biro','=',$r->id_fakultas_biro)
                    ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                    ->first();
            } else {
                $getDiketahui = Pegawai::leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                    ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                    ->leftJoin('validator_proposals','validator_proposals.diketahui_oleh','=','jabatans.id')
                    ->where('jabatans.kode_jabatan','=','RKT')
                    ->select('validator_proposals.diketahui_oleh','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                    ->first();
            }

            $getDisetujui = Pegawai::leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->leftJoin('handle_proposals','handle_proposals.id_pegawai','=','pegawais.id')
                ->whereJsonContains('handle_proposals.id_jenis_kegiatan',(string) $r->id_jenis_kegiatan)
                ->select('jabatans.kode_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                ->first();
                
        }
        
    
        $fileName = 'proposal_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.pengajuan-proposal.preview-proposal', compact('datas','sarpras','anggarans','grandTotal','getQR','qrcode','data_lampiran','getPengusul','getDiketahui','getDisetujui'));
        $pdf->setPaper('F4','P');
        $pdf->getDomPDF()->set_option("isPhpEnabled", true);
        $pdf->getFontMetrics()->get_font("helvetica", "bold");
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    public function showQR($slug)
    {
        $initial = ''.URL::to('/').'/in/'.$slug;
        $datas = DB::table('status_proposals')
            ->leftJoin('proposals','proposals.id','=','status_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->select('proposals.id AS id_proposal','proposals.id_jenis_kegiatan','proposals.nama_kegiatan','proposals.tgl_event','proposals.id_fakultas_biro','status_proposals.status_approval','status_proposals.generate_qrcode','pegawais.nama_pegawai AS nama_dosen','status_proposals.updated_at','jenis_kegiatans.nama_jenis_kegiatan')
            ->where('status_proposals.generate_qrcode',$initial)
            ->get();

        # Get Pengusul according to proposal.user_id
        foreach($datas as $r){
            $getPengusul = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diusulkan_oleh')
                ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
                ->where('proposals.id','=',$r->id_proposal)
                ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                ->first();

            if($r->id_fakultas_biro != null){
                $getDiketahui = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
                    ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                    ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                    ->where('jabatan_pegawais.id_fakultas_biro','=',$r->id_fakultas_biro)
                    ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                    ->first();
            } else {
                $getDiketahui = Pegawai::leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                    ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                    ->leftJoin('validator_proposals','validator_proposals.diketahui_oleh','=','jabatans.id')
                    ->where('jabatans.kode_jabatan','=','RKT')
                    ->select('validator_proposals.diketahui_oleh','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                    ->first();
            }

            $getDisetujui = Pegawai::leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->leftJoin('handle_proposals','handle_proposals.id_pegawai','=','pegawais.id')
                ->whereJsonContains('handle_proposals.id_jenis_kegiatan',(string) $r->id_jenis_kegiatan)
                ->select('jabatans.kode_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan','jabatans.nama_jabatan')
                ->first();
        }

        return view('general.pengajuan-proposal.show_qrcode', compact('datas','getPengusul','getDiketahui','getDisetujui'));
    }

    public function viewlampiran(Request $request)
    {
        $datas = DB::table('lampiran_proposals')->where('id_proposal',$request->proposal_id)->select('id','nama_berkas','berkas','link_gdrive','keterangan')->get();
        $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Berkas</th>
                            <th>Ket</th>
                            <th>Lihat</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($datas as $no => $data){
                        $html .= 
                            '<tr>
                                <td>'.++$no.'</td>
                                <td>'.$data->nama_berkas.'</td>
                                <td>'.$data->keterangan.'</td>
                                <td>';
                                if($data->berkas != ''){
                                    $html .= '<button type="button" name="view" id="'.$data->id.'" class="view btn btn-outline-primary btn-sm"><a href="'.asset('/'.$data->berkas).'" target="_blank"><i class="bx bx-show"></i></a></button>';
                                } else {
                                    $html .= '<a href="'.$data->link_gdrive.'" target="_blank">'.$data->link_gdrive.'</a>';
                                }
                                
                                $html .= '</td>
                            </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }

    public function checkanggaran(Request $request)
    {
        $checkKeteranganDiTolak = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->select('keterangan_ditolak')->get();
        $datas = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->get();
        if($checkKeteranganDiTolak->count() > 0){
            foreach($checkKeteranganDiTolak as $dataKet){
                if($dataKet->keterangan_ditolak == ''){
                    $html = '';
                } else {
                    $html = '<i class="bx bx-spa mb-1"></i> Keterangan pending:  <p style="color: #f3920b; font-size: 13px; font-style:italic;">'.$dataKet->keterangan_ditolak.'</p>
                    <hr>';
                }
            }
        } else {
            $html .= '';
        }

        if($datas->count() > 0){
        $html .= '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Biaya Satuan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
        
            foreach($datas as $no => $item){
                $html .= '<tr>
                        <td>'.++$no.'</td>
                        <td>'.$item->item.'</td>
                        <td>'.currency_IDR($item->biaya_satuan).'</td>
                        <td>'.$item->quantity.'</td>';
                    if($item->status == '1'){
                        $html .= '<td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-id="'.$item->id.'" data-item="'.$item->item.'" data-biaya-satuan="'.$item->biaya_satuan.'" data-quantity="'.$item->quantity.'" data-frequency="'.$item->frequency.'" data-sumber-dana="'.$item->sumber_dana.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-anggaran"><i class="bx bx-edit bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-anggaran-post"><i class="bx bx-trash bx-xs"></i></a></td>';
                    } else {
                        $html .= '<td></td></tr>';
                    }
            }
            $html .= '</tbody>
                </table>';
        } else {
            $html = '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Biaya Satuan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" style="text-align: center;">No data available in table</td>
                        </tr>
                    </tbody>';
        }
        return response()->json(['card' => $html]);
    }

    public function updateAnggaranItem(Request $request){
        $post = DataRencanaAnggaran::where('id',$request->e_anggaran_id)->update([
            'item'          => $request->e_anggaran_item,
            'biaya_satuan'  => $request->e_anggaran_biaya_satuan,
            'quantity'      => $request->e_anggaran_quantity,
            'frequency'     => $request->e_anggaran_frequency,
            'sumber_dana'   => $request->e_anggaran_sumber_dana
        ]);
        return response()->json($post);
    }

    public function hapusItemAnggaran(Request $request)
    {
        $post = DataRencanaAnggaran::where('id',$request->id)->delete(); 
        return response()->json($post);
    }

    public function checkinformasi(Request $request)
    {
        $datas = Proposal::where('id',$request->proposal_id)->get();
        $html = '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Deskripsi</th>
                            <th>Isi Data</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach($datas as $no => $item){
                    $html .= '<tr><td>Nama Kegiatan</td><td>'.$item->nama_kegiatan.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-nama-kegiatan="'.$item->nama_kegiatan.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-nama-kegiatan"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Pendahuluan</td><td>'.$item->pendahuluan.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-pendahuluan="'.$item->pendahuluan.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-pendahuluan"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Tujuan dan Manfaat</td><td>'.$item->tujuan_manfaat.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-tujuan-manfaat="'.$item->tujuan_manfaat.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-tujuan-manfaat"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Tanggal Kegiatan</td><td>'.$item->tgl_event.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-tglevent="'.$item->tgl_event.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-tglevent"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Lokasi / Tempat</td><td>'.$item->lokasi_tempat.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-lokasitempat="'.$item->lokasi_tempat.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-lokasitempat"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Peserta</td><td>'.$item->peserta.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-peserta="'.$item->peserta.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-peserta"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Detil Kegiatan</td><td>'.$item->detil_kegiatan.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-detilkegiatan="'.$item->detil_kegiatan.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-detilkegiatan"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Penutup</td><td>'.$item->penutup.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-penutup="'.$item->penutup.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-penutup"><i class="bx bx-edit bx-xs"></i></a></td></tr>';
                }
        return response()->json(['card' => $html]);
    }

    public function updateNamaKegiatan(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_nama_kegiatan)->update([
            'nama_kegiatan' => $request->e_nama_kegiatan
        ]);
        return response()->json($post);
    }

    public function updatePendahuluan(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_pendahuluan)->update([
            'pendahuluan' => $request->e_pendahuluan
        ]);
        return response()->json($post);
    }

    public function updateTujuanManfaat(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_tujuan_manfaat)->update([
            'tujuan_manfaat' => $request->e_tujuan_manfaat
        ]);
        return response()->json($post);
    }

    public function updateTanggalEvent(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_tglevent)->update([
            'tgl_event' => $request->e_tglevent
        ]);
        return response()->json($post);
    }

    public function updateLokasiTempat(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_lokasitempat)->update([
            'lokasi_tempat' => $request->e_lokasitempat
        ]);
        return response()->json($post);
    }

    public function updatePeserta(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_peserta)->update([
            'peserta' => $request->e_peserta
        ]);
        return response()->json($post);
    }

    public function updateDetilKegiatan(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_detilkegiatan)->update([
            'detil_kegiatan' => $request->e_detilkegiatan
        ]);
        return response()->json($post);
    }

    public function updatePenutup(Request $request)
    {
        $post = Proposal::where('id',$request->props_id_penutup)->update([
            'penutup' => $request->e_penutup
        ]);
        return response()->json($post);
    }

    public function submitUlangProposal(Request $request)
    {
        # Get Data Proposals
        $getDataProposal = Proposal::leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->where('proposals.id',$request->id_proposal)
            ->select('proposals.id_fakultas_biro','proposals.nama_kegiatan','pegawais.nama_pegawai')
            ->first();
        # get Email Dekan
        $emailDekanBiro = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->where([['jabatans.kode_jabatan','=','PEG'],['jabatan_pegawais.id_fakultas_biro',$getDataProposal->id_fakultas_biro]])
            ->select('pegawais.email')
            ->first();
        # get Email Admin Umum
        $emailADU = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->where('jabatans.kode_jabatan','=','ADU')
            ->select('pegawais.email')
            ->first();
        $listEmail = [$emailADU->email,$emailDekanBiro->email];

        $isiData = [
            'name' => 'Revisi Proposal Kegiatan oleh '.$getDataProposal->nama_pegawai.'',
            'body' => 'Revisi Proposal Kegiatan: '.$getDataProposal->nama_kegiatan.' telah selesai dilakukan.',
        ];

        $post = DB::table('status_proposals')->where('id_proposal',$request->id_proposal)->update([
            'status_approval' => 1,
            'keterangan_ditolak' => ''
        ]);
        Mail::to($listEmail)->send(new KirimEmail($isiData));
        return response()->json($post);
    }

    public function arsipProposal(Request $request)
    {
        $post = Proposal::where('id',$request->id_proposal)->update([
            'is_archived' => 1
        ]);
        return response()->json($post);
    }

    public function submitUlangAnggaran(Request $request)
    {
        # Get Data Proposals
        $getDataProposal = Proposal::leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->where('proposals.id',$request->id_proposal)
            ->select('proposals.id_fakultas_biro','proposals.nama_kegiatan','pegawais.nama_pegawai')
            ->first();
        # get Email Dekan
        $emailDekanBiro = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->where([['jabatans.kode_jabatan','=','PEG'],['jabatan_pegawais.id_fakultas_biro',$getDataProposal->id_fakultas_biro]])
            ->select('pegawais.email')
            ->first();
        # get Email Admin Umum
        $emailADU = JabatanPegawai::rightJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->where('jabatans.kode_jabatan','=','ADU')
            ->select('pegawais.email')
            ->first();
        $listEmail = [$emailADU->email,$emailDekanBiro->email];

        $isiData = [
            'name' => 'Revisi Anggaran Proposal Kegiatan oleh '.$getDataProposal->nama_pegawai.'',
            'body' => 'Revisi Anggaran Proposal Kegiatan: '.$getDataProposal->nama_kegiatan.' telah selesai dilakukan.',
        ];

        $post = DB::table('status_proposals')->where('id_proposal',$request->id_proposal)->update([
            'status_approval' => 1,
            'keterangan_ditolak' => ''
        ]);
        Mail::to($listEmail)->send(new KirimEmail($isiData));
        return response()->json($post);
    }
}
