<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use App\Models\General\DataRealisasiAnggaran;
use App\Models\General\Proposal;
use App\Setting\Dekan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;
use Auth; use URL;
use App\Models\Master\ValidatorProposal;
use App\Models\Master\HandleProposal;
use Illuminate\Support\Facades\Session;

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
            if(!empty($renang['item']) && !empty($renang['biaya_satuan']) && !empty($renang['quantity']) && !empty($renang['frequency']) && !empty($renang['sumber'])){
                $dataRenang[] = [
                    'id_proposal'   => $getId,
                    'item'          => $renang['item'],
                    'biaya_satuan'  => $renang['biaya_satuan'],
                    'quantity'      => $renang['quantity'],
                    'frequency'     => $renang['frequency'],
                    'sumber_dana'   => $renang['sumber'],
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
                if(!empty($request->nama_berkas[$x]) && !empty($fileNames[$x]) && !empty($request->keterangan[$x])) {
                    $insertData[] = [
                        'id_proposal'   => $getId,
                        'nama_berkas'   => $request->nama_berkas[$x],
                        'berkas'        => $fileNames[$x],
                        'keterangan'    => $request->keterangan[$x],
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
                if(!empty($request->nama_berkas[$x]) && !empty($request->link_gdrive[$x]) && !empty($request->keterangan[$x])) {
                    $insertData[] = [
                        'id_proposal'   => $getId,
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
                        return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i></a>';
                    } else {
                        return '<a href="'.Route('index-laporan',encrypt(['id' => $data->id])).'" class="btn btn-outline-primary btn-sm"><i class="bx bx-plus-circle bx-tada-hover bx-xs"></i> Buat Laporan</a>';
                    }
                } else {
                    return '<span class="badge bg-label-secondary">Pending</span>';
                }
            })->addColumn('action', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return $this->statusLaporanProposal($data->id);
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })
            ->rawColumns(['laporan','action'])
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
            $getDiketahui = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
                ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
                ->where('jabatan_pegawais.id_fakultas_biro','=',$r->id_fakultas_biro)
                ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                ->first();

            $queryGetJenisKegiatan = HandleProposal::select('handle_proposals.id_jabatan')
                ->leftJoin('proposals','proposals.id_jenis_kegiatan', '=', 'handle_proposals.id_jenis_kegiatan')
                ->whereJsonContains('handle_proposals.id_jenis_kegiatan',(string) $r->id_jenis_kegiatan)
                ->get();

            $getDisetujui = DB::table('pegawais')->leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->whereIn('jabatan_pegawais.id_jabatan',$queryGetJenisKegiatan)
                ->select('jabatans.kode_jabatan','pegawais.nama_pegawai')
                ->first();
        }
        
        $fileName = 'laporan_proposal_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.laporan-proposal.preview-laporan-proposal', compact('datas','getQR','qrcode','anggarans','realisasianggarans','grandTotalAnggarans','grandTotalRealisasiAnggarans','data_lampiran','getPengusul','getDiketahui','getDisetujui'));
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    protected function statusLaporanProposal($id)
    {
        $query = DB::table('status_laporan_proposals')->select('status_approval','keterangan_ditolak')->where('id_laporan_proposal','=',$id)->get();
        if($query){
            foreach($query as $data){                
                if($data->status_approval == 1){
                    return '<button type="button" name="delete" id="'.$id.'" data-toggle="tooltip" data-placement="bottom" title="Hapus Laporan" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                } elseif($data->status_approval == 2){
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 3) {
                    return '<span class="badge bg-label-warning"><i class="bx bx-check-double bx-xs"></i> Proses Verifikasi</span>';
                } elseif($data->status_approval == 4) {
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Pending WR</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 5) {
                    return '<span class="badge bg-label-success"><i class="bx bx-check-shield bx-xs"></i> Verified</span>';
                } else {
                    return '<span class="badge bg-label-secondary">Pending</span>';
                }
            }
        } else {
            return 'x';
        }
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

            $getDiketahui = ValidatorProposal::leftJoin('jabatans','jabatans.id','=','validator_proposals.diketahui_oleh')
                ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
                ->leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
                ->where('jabatan_pegawais.id_fakultas_biro','=',$r->id_fakultas_biro)
                ->select('jabatans.nama_jabatan','pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
                ->first();

            $queryGetJenisKegiatan = HandleProposal::select('handle_proposals.id_jabatan')
                ->leftJoin('proposals','proposals.id_jenis_kegiatan', '=', 'handle_proposals.id_jenis_kegiatan')
                ->whereJsonContains('handle_proposals.id_jenis_kegiatan',(string) $r->id_jenis_kegiatan)
                ->get();

            $getDisetujui = DB::table('pegawais')->leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
                ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->whereIn('jabatan_pegawais.id_jabatan',$queryGetJenisKegiatan)
                ->select('jabatans.kode_jabatan','jabatans.nama_jabatan','pegawais.nama_pegawai')
                ->first();
        }

        return view('rektorat-page.data-proposal.show_qrcode', compact('datas','getPengusul','getDiketahui','getDisetujui'));
    }
}
