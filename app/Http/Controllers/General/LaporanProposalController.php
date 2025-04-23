<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use App\Models\General\DataRealisasiAnggaran;
use App\Models\General\Proposal;
use App\Models\General\TahunAkademik;
use App\Setting\Dekan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;
use Auth; use URL;
use App\Models\Master\ValidatorProposal;
use App\Models\Master\HandleProposal;
use App\Models\Master\Pegawai;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanProposalExport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LaporanProposalController extends Controller
{
    public function indexlaporan(Request $request, $id)
    {
        $ID = decrypt($id);
        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $grandTotal = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();
        
        return view('general.laporan-proposal.index-laporan', ['anggarans' => $anggarans, 'grandTotal' => $grandTotal, 'id' => $ID]);
    }

    public function insertLaporanProposal(Request $request)
    {
        $request->validate([
            'berkas.*'     => 'file|mimes:pdf,doc,docx|max:2048'
        ],[
            'berkas.*.mimes'  => 'File harus berjenis (pdf atau docx)', 
            'berkas.*.max'    => 'Ukuran berkas tidak boleh melebihi 2MB', 
        ]);

        $getId = $request->id_pro;
        $post = LaporanProposal::updateOrCreate([
            'id_proposal'                   => $getId,
            'hasil_kegiatan'                => $request->hasil_kegiatan,
            'evaluasi_catatan_kegiatan'     => $request->evaluasi_catatan_kegiatan,
            'penutup'                       => $request->penutup,
        ]);

        $post = DB::table('status_laporan_proposals')->insert(
            [
                'id_laporan_proposal' => $getId,
                'status_approval'     => 1,
                'created_at'          => now(),
                'updated_at'          => now()
            ]);

        $dataRenang = [];
        foreach($request->rows as $k => $renang){
            if (!empty($renang['item']) && 
                !empty($renang['biaya_satuan']) && 
                !empty($renang['quantity']) && 
                !empty($renang['frequency'])){

                $sumber_dana_anggaran = !empty($renang['sumber']) ? $renang['sumber'] : 1;

                $dataRenang[] = [
                    'id_proposal'   => $getId,
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
            $post = DataRealisasiAnggaran::insert($dataRenang);
        }

        # Insert data into lampiran_laporan_proposals
        if($request->berkas != ''){
            $fileNames = [];
            foreach($request->berkas as $file){
                $fileName = md5(time().'_'.Auth::user()->user_id).$file->getClientOriginalName();
                $file->move(public_path('uploads-lampiran/lampiran-laporan-proposal'),$fileName);
                $fileNames[] = 'uploads-lampiran/lampiran-laporan-proposal/'.$fileName;
            }

            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){

                if(!empty($fileNames[$x])) {
                    $default_nama_berkas = !empty($request->nama_berkas[$x]) ? $request->nama_berkas[$x] : time().'-default';
                    $default_keterangan  = !empty($request->keterangan[$x]) ? $request->keterangan[$x] : '-';
    
                    $insertData[] = [
                        'id_proposal'   => $getId,
                        'nama_berkas'   => $default_nama_berkas,
                        'berkas'        => $fileNames[$x],
                        'keterangan'    => $default_keterangan,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }
            }
            if (!empty($insertData)) {
                $post = DB::table('lampiran_laporan_proposals')->insert($insertData);
            }
        } else {
            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){

                if(!empty($request->link_gdrive[$x])) {
                    $default_nama_berkas = !empty($request->nama_berkas[$x]) ? $request->nama_berkas[$x] : time().'-default';
                    $default_keterangan  = !empty($request->keterangan[$x]) ? $request->keterangan[$x] : '-';
    
                    $insertData[] = [
                        'id_proposal'   => $getId,
                        'nama_berkas'   => $default_nama_berkas,
                        'berkas'        => '',
                        'link_gdrive'   => $request->link_gdrive[$x],
                        'keterangan'    => $default_keterangan,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }
            }
            if (!empty($insertData)) {
                $post = DB::table('lampiran_laporan_proposals')->insert($insertData);
            }
            return redirect()->route('my-report');
        }

        return response()->json($post);
    }

    public function laporansaya(Request $request)
    {
        # check proposal(s)
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user_dosen','laporan_proposals.created_at AS tgl_proposal')
            ->where([['proposals.user_id',Auth::user()->user_id],['status_proposals.status_approval',5]])
            ->orderBy('proposals.id','DESC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $checkStatusProposal = DB::table('status_proposals')->where([['status_approval',5],['id_proposal',$data->id]])->get();
                if($checkStatusProposal->count() > 0){
                    $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                    if($query->count() > 0){
                        return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal"><small class="text-info"><i class="bx bx-file bx-xs"></i> Lihat</small></a>';
                    } else {
                        return '<a href="'.Route('index-laporan',encrypt(['id' => $data->id])).'" class="text-primary"><i class="bx bx-plus-circle bx-tada-hover bx-xs"></i> <small>Buat Laporan</small></a>';
                    }
                } else {
                    return '<small><i class="text-secondary">Pending</i></small>';
                }
            })->addColumn('status', function($data){
                if (DB::table('status_laporan_proposals')->where('id_laporan_proposal', $data->id)->exists()) {
                    return $this->statusLaporanProposal($data->id);
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada</small>';
                }                
            })->addColumn('action', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    foreach($query as $get){
                        if($get->status_approval == 2 || $get->status_approval == 4){
                            $dropdownMenu = '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown">
                                        <small class="text-danger"><i class="bx bx-revision"></i> Revisi</small>
                                    </button>
                                <div class="dropdown-menu">';
                            $dropdownMenu .= '<small>
                                <a class="dropdown-item revisi-informasi text-danger" data-id="'.$data->id.'" href="javascript:void(0);"><i class="bx bx-show me-2"></i>Revisi Isi Laporan</a>
                                <a class="dropdown-item revisi-anggaran text-danger" data-id="'.$data->id.'" href="'.route('index-revisi-anggaran-laporan-proposal', encrypt(['id' => $data->id])).'"><i class="bx bx-money me-2"></i>Revisi Anggaran</a>
                                <a class="dropdown-item done-revision text-danger" data-id="'.$data->id.'" href="javascript:void(0);"><i class="bx bx-check-double me-2"></i>Selesai Revisi</a>
                                <a class="dropdown-item delete text-danger" id="'.$data->id.'" href="javascript:void(0);"><i class="bx bx-trash me-2"></i>Hapus Laporan</a></small>';
                            $dropdownMenu .= '</div></div>';
                            return $dropdownMenu;
                        } elseif($get->status_approval == 1){
                            return '<button type="button" name="delete" id="' . $data->id . '" data-toggle="tooltip" data-placement="bottom" title="Hapus Laporan" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                        } else {
                            return '<small><i class="bx bx-minus-circle bx-xs"></i></small>';
                        }
                    }
                } else {
                    return '<small><i class="bx bx-minus-circle"></i></small>';
                }
            })
            ->rawColumns(['laporan','status','action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.laporan-proposal.laporan-saya');
    }

    public function previewlaporan($id)
    {
        $ID = decrypt($id);
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->leftJoin('form_rkats','form_rkats.id','=','proposals.id_form_rkat')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','laporan_proposals.id AS id_laporan','laporan_proposals.*','laporan_proposals.penutup AS lap_penutup','laporan_proposals.created_at AS tgl_laporan','pegawais.nama_pegawai AS nama_user_dosen','form_rkats.kode_renstra')
            ->where('proposals.id',$ID)
            ->orderBy('proposals.id','DESC')
            ->get();
        
        # Get and show the QRCode
        $getQR = DB::table('status_laporan_proposals')
            ->leftJoin('proposals','proposals.id','=','status_laporan_proposals.id_laporan_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->select('status_laporan_proposals.status_approval','status_laporan_proposals.generate_qrcode','pegawais.nama_pegawai AS nama_dosen')
            ->where([['status_laporan_proposals.id_laporan_proposal',$ID],['status_laporan_proposals.status_approval',5]])
            ->get();
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate('Unverified!'));

        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $realisasianggarans = DataRealisasiAnggaran::where('id_proposal',$ID)->get();
        $grandTotalAnggarans = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();
        $grandTotalRealisasiAnggarans = DataRealisasiAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotalRealisasi'))->where('id_proposal',$ID)->first();

        $data_lampiran = DB::table('lampiran_laporan_proposals')->where('id_proposal',$ID)->get();

        # Get Pengusul according to proposal.user_id
        $getPengusul = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diusulkan_oleh')
            ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
            ->where('proposals.id','=',$ID)
            ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
            ->first();

        foreach($datas as $r){
            if($r->id_fakultas_biro != null){
                $getDiketahui = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
                    ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                    ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                    ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
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
        
        $fileName = 'laporan_proposal_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.laporan-proposal.preview-laporan-proposal', [
            'datas'=> $datas,
            'getQR' => $getQR,
            'qrcode' => $qrcode,
            'anggarans' =>$anggarans,
            'realisasianggarans' => $realisasianggarans,
            'grandTotalAnggarans' => $grandTotalAnggarans,
            'grandTotalRealisasiAnggarans' => $grandTotalRealisasiAnggarans,
            'data_lampiran' => $data_lampiran,
            'getPengusul' => $getPengusul,
            'getDiketahui' => $getDiketahui,
            'getDisetujui' => $getDisetujui
        ]);
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    protected function statusLaporanProposal($id)
    {
        $query = DB::table('status_laporan_proposals')
            ->select('status_approval', 'keterangan_ditolak')
            ->where('id_laporan_proposal', '=', $id)
            ->get();

        if ($query->isNotEmpty()) { 
            $data = $query->first();
            
            switch ($data->status_approval) {
                case 1:
                    return '<small><i class="text-warning">Menunggu validasi atasan</i></small>';
                case 2:
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="' . $data->keterangan_ditolak . '" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="text-danger"><small><i>Ditolak Atasan&nbsp;</i></small></span>
                    <span class="badge bg-danger badge-notifications">?</span>
                    </a>';
                case 3:
                    return '<small><i class="text-warning">Menunggu validasi rektorat</i></small>';
                case 4:
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="' . $data->keterangan_ditolak . '" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="text-danger"><small><i>Ditolak Rektorat&nbsp;</i></small></span>
                    <span class="badge bg-danger badge-notifications">?</span></a>';
                case 5:
                    return '<small><i class="text-success">ACC Rektorat</i></small>';
                default:
                    return '<small><i class="text-warning">Menunggu validasi atasan</i></small>';
            }
        } 
        return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada</small>';

    }

    public function hapuslaporan(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->id)->delete();
        $post = DataRealisasiAnggaran::where('id_proposal',$request->id)->delete();
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->id)->delete();
        return response()->json($post);
    }

    public function qrlaporan($slug)
    {
        $initial = ''.URL::to('/').'/report/'.$slug;
        $datas = DB::table('status_laporan_proposals')->leftJoin('proposals','proposals.id','=','status_laporan_proposals.id_laporan_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->select('proposals.id AS id_proposal','proposals.id_jenis_kegiatan','proposals.nama_kegiatan','proposals.tgl_event','proposals.id_fakultas_biro','pegawais.nama_pegawai AS nama_dosen','jenis_kegiatans.nama_jenis_kegiatan','status_laporan_proposals.updated_at')
            ->where('status_laporan_proposals.generate_qrcode',$initial)
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
                    ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
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

        return view('general.laporan-proposal.show_qrcode', compact('datas','getPengusul','getDiketahui','getDisetujui'));
    }

    public function indexExportProposal(Request $request)
    {
        $checkYear = TahunAkademik::select('year','id')->get();

        if($request->tahun_proposal == null || $request->tahun_proposal == '[semua]'){
            $datas = Proposal::leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','proposals.id_tahun_akademik')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.nama_kegiatan','proposals.id_tahun_akademik','proposals.tgl_event','proposals.is_archived','proposals.created_at','pegawais.nama_pegawai','status_laporan_proposals.status_approval','tahun_akademiks.year')
                ->orderBy('proposals.tgl_event','DESC')
                ->get();
        } else {
            $datas = Proposal::leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','proposals.id_tahun_akademik')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.nama_kegiatan','proposals.id_tahun_akademik','proposals.tgl_event','proposals.is_archived','proposals.created_at','pegawais.nama_pegawai','status_laporan_proposals.status_approval','tahun_akademiks.year')
                ->where('proposals.id_tahun_akademik',$request->tahun_proposal)
                ->orderBy('proposals.tgl_event','DESC')
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('status', function($data){
                $statusLabels = [
                    1 => '<small><i class="text-warning">Menunggu validasi atasan</i></small>',
                    2 => '<small><i class="text-danger">Ditolak Atasan</i></small>',
                    3 => '<small><i class="text-warning">Menunggu validasi rektorat</i></small>',
                    4 => '<small><i class="text-danger">Ditolak Rektorat</i></small>',
                    5 => '<small><i class="text-success">ACC Rektorat</i></small>',
                ];
                
                if ($data->is_archived == 1) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Status terakhir: ' 
                        . strip_tags($statusLabels[$data->status_approval] ?? 'Proposal dibatalkan') . '">
                        <small class="text-warning">Dibatalkan oleh user</small>&nbsp;&nbsp;
                        <span class="badge bg-danger badge-notifications">?</span></a>';
                } 
                
                return $statusLabels[$data->status_approval] ?? '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada</small>';
            })
            ->rawColumns(['status'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.laporan-proposal.export-data', compact('checkYear'));
    }

    public function showDataProposalHtml($year)
    {
        if($year == null || $year == '[semua]'){
            $datas = Proposal::leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
                ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('form_rkats', 'form_rkats.id', '=', 'proposals.id_form_rkat')
                ->leftJoin('data_rencana_anggarans', 'data_rencana_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->select(
                    'proposals.id AS id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.is_archived',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.id_laporan_proposal',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    DB::raw('(SELECT SUM(data_rencana_anggarans.biaya_satuan * data_rencana_anggarans.quantity * data_rencana_anggarans.frequency) FROM data_rencana_anggarans WHERE data_rencana_anggarans.id_proposal = proposals.id) as anggaran_proposal'),
                    DB::raw('(SELECT SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) FROM data_realisasi_anggarans WHERE data_realisasi_anggarans.id_proposal = proposals.id) as realisasi_anggaran')
                )
                ->groupBy(
                    'proposals.id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.is_archived',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.id_laporan_proposal',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total'
                )
                ->orderBy('proposals.tgl_event','DESC')
                ->get();


        } else {
            $datas = Proposal::leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
                ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('form_rkats', 'form_rkats.id', '=', 'proposals.id_form_rkat')
                ->leftJoin('data_rencana_anggarans', 'data_rencana_anggarans.id_proposal', '=', 'proposals.id')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->select(
                    'proposals.id AS id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.is_archived',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.id_laporan_proposal',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total',
                    DB::raw('(SELECT SUM(data_rencana_anggarans.biaya_satuan * data_rencana_anggarans.quantity * data_rencana_anggarans.frequency) FROM data_rencana_anggarans WHERE data_rencana_anggarans.id_proposal = proposals.id) as anggaran_proposal'),
                    DB::raw('(SELECT SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) FROM data_realisasi_anggarans WHERE data_realisasi_anggarans.id_proposal = proposals.id) as realisasi_anggaran')
                )
                ->groupBy(
                    'proposals.id',
                    'proposals.nama_kegiatan',
                    'proposals.tgl_event',
                    'proposals.is_archived',
                    'proposals.created_at',
                    'pegawais.nama_pegawai',
                    'status_laporan_proposals.id_laporan_proposal',
                    'status_laporan_proposals.status_approval',
                    'data_fakultas_biros.nama_fakultas_biro',
                    'form_rkats.kode_renstra',
                    'form_rkats.total'
                )
                ->where('proposals.id_tahun_akademik',$year)
                ->orderBy('proposals.tgl_event','DESC')
                ->get();
        }
        $getYear = TahunAkademik::where('id',$year)->select('year')->first();
        if($getYear){
            $getYear = $getYear->year;
        } else {
            $getYear = '[Semua]';
        }
        return view('general.laporan-proposal.show-in-html', ['datas' => $datas, 'getYear' => $getYear]);
    }

    public function downloadProposalExcel($year)
    {
        $getYear = TahunAkademik::where('id',$year)->select('year')->first();
        if($getYear){
            $getYear = $getYear->year;
        } else {
            $getYear = '[Semua]';
        }
        $fileName = 'data_proposal_dan_laporan_proposal_'.$getYear.'.xlsx';

        // Simpan file Excel ke penyimpanan sementara
        Excel::store(new LaporanProposalExport($year), $fileName, 'local');

        return Response::stream(function() use ($fileName) {
            // Buka file dari penyimpanan sementara dan kirim sebagai stream
            $file = Storage::disk('local')->get($fileName);
            echo $file;
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    # Revisi Laporan Proposal
    public function checkInformasi(Request $request)
    {
        $datas = LaporanProposal::where('id_proposal',$request->proposal_id)->get();
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
                    $html .= '<tr><td>Hasil Kegiatan</td><td>'.$item->hasil_kegiatan.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-hasil-kegiatan="'.$item->hasil_kegiatan.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-hasil-kegiatan"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Evaluasi Catatan Kegiatan</td><td>'.$item->evaluasi_catatan_kegiatan.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-catatan="'.$item->evaluasi_catatan_kegiatan.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-catatan"><i class="bx bx-edit bx-xs"></i></a></td></tr>
                        <tr><td>Penutup</td><td>'.$item->penutup.'</td><td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-penutup="'.$item->penutup.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-penutup"><i class="bx bx-edit bx-xs"></i></a></td></tr>';
                }
        return response()->json(['card' => $html]);
    }

    public function updateHasilKegiatan(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->props_id_hasil_kegiatan)->update([
            'hasil_kegiatan' => $request->e_hasil_kegiatan
        ]);
        return response()->json($post);
    }

    public function updateCatatanKegiatan(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->props_id_catatan_kegiatan)->update([
            'evaluasi_catatan_kegiatan' => $request->e_catatan_kegiatan
        ]);
        return response()->json($post);
    }

    public function updatePenutup(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->props_id_penutup)->update([
            'penutup' => $request->e_penutup
        ]);
        return response()->json($post);
    }

    public function doneRevision(Request $request)
    {
        $html = 'Ini adalah halaman Konfirmasi Selesai Revisi. Silakan klik pada check-box lalu ajukan kembali untuk mengkonfirmasi bahwa revisi laporan proposal yang anda buat telah selesai.<br><br>Sebagai catatan, setelah konfirmasi ajukan kembali maka status laporan anda kembali menjadi "Menunggu validasi atasan".';
        return response()->json(['card' => $html]);
    }

    public function confirmDoneRevision(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->id_proposal)->update([
            'status_approval' => 1,
            'keterangan_ditolak' => ''
        ]);
        return response()->json($post);
    }
}
