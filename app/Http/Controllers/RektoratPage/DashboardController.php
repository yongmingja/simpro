<?php

namespace App\Http\Controllers\RektoratPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\HandleProposal;
use App\Models\General\LaporanFpku;
use Illuminate\Support\Facades\Mail;
use App\Mail\UndanganFpku;
use Auth;
use DB;
use URL;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if($request->status == '' || $request->status == 'all'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user')
                ->where('proposals.is_archived',0)
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan()) // filter WR yang akan handle pengecekan proposal, namun diubah semua default ke role WRAK
                ->orderBy('status_proposals.status_approval','ASC')
                ->get();
        }
        if($request->status == 'pending') {
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user')
                ->where([['proposals.is_archived',0],['status_proposals.status_approval',1]])
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan()) 
                ->orderBy('status_proposals.status_approval','ASC')
                ->get();
        }
        if($request->status == 'accepted') {
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user')
                ->where([['proposals.is_archived',0],['status_proposals.status_approval',5]])
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan()) 
                ->orderBy('status_proposals.status_approval','ASC')
                ->get();
        }
        if($request->status == 'denied') {
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai AS nama_user')
                ->where([['proposals.is_archived',0],['status_proposals.status_approval',4]])
                ->orWhere('status_proposals.status_approval',2)
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan()) 
                ->orderBy('status_proposals.status_approval','ASC')
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-primary btn-sm"><i class="bx bx-file bx-xs"></i></a>';               
                return $button;
            })->addColumn('validasi', function($data){
                $checkState = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->get();
                if($checkState->count() > 0){
                    foreach($checkState as $state){
                        if($state->status_approval == 3){                            
                            $button = '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                            $button .= '&nbsp;';
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>';                            
                            return $button;
                        } elseif($state->status_approval == 4){
                            return '<span class="badge bg-label-danger">Ditolak</span>';
                        } elseif($state->status_approval == 5) {
                            return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Diterima</span>';
                        } else {
                            return '<span class="badge bg-label-secondary">Pending</span>';
                        }
                    }
                } else {
                    return '';
                }
            })->addColumn('vlampiran', function($data){
                # check any attachment
                $q = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($q > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="v-lampiran" style="font-size: 10px;">lihat lampiran</a>';
                }else{
                    return '<p style="font-size: 10px;">No attachment</p>';
                }
            })
            ->rawColumns(['action','validasi','vlampiran'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dashboard.rektorat-dashboard');
    }

    protected function arrJenisKegiatan()
    {
        $getPeran = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
            ->select('jabatans.kode_jabatan','jabatans.id AS id_jabatan')
            ->first();

            if(session()->get('selected_peran') == null){
                $recentPeranIs = $getPeran->kode_jabatan;
                $recentPeranId = $getPeran->id_jabatan;
            } else {
                $recentPeranIs = session()->get('selected_peran');
                $recentPeranId = $getPeran->id_jabatan;
            }

        $datas = HandleProposal::select('id_jenis_kegiatan')->where('id_jabatan',$recentPeranId)->get();
        if($datas){
            foreach($datas as $data){
                $getID = $data->id_jenis_kegiatan;
            }
        }else{
            $getID = '';
        }
        return $getID;
    }

    public function approvalY(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update([
            'status_approval' => 5,
            'keterangan_ditolak' => '',
            'generate_qrcode' => ''.URL::to('/').'/in/'.time().'.png'
        ]);
        return response()->json($post);
    }

    public function approvalN(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->propsl_id)->update([
            'status_approval' => 4,
            'keterangan_ditolak' => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }

    public function indexlaporan(Request $request)
    {
        if($request->status == '' || $request->status == 'all'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','status_laporan_proposals.keterangan_ditolak','status_laporan_proposals.created_at AS tgl_proposal')
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->get();
        }
        if($request->status == 'pending'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','status_laporan_proposals.keterangan_ditolak','status_laporan_proposals.created_at AS tgl_proposal')
                ->where('status_laporan_proposals.status_approval',1)
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->get();
        }
        if($request->status == 'accepted'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','status_laporan_proposals.keterangan_ditolak','status_laporan_proposals.created_at AS tgl_proposal')
                ->where('status_laporan_proposals.status_approval',5)
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->get();
        }
        if($request->status == 'denied'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','status_laporan_proposals.keterangan_ditolak','status_laporan_proposals.created_at AS tgl_proposal')
                ->where('status_laporan_proposals.status_approval',4)
                ->orWhere('status_laporan_proposals.status_approval',2)
                ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i> view report</a>';
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })->addColumn('action', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    foreach($query as $q){
                        if($q->status_approval == 5){
                            return '<span class="badge bg-label-success"><i class="bx bx-check-shield bx-xs"></i> verified</span>';
                        } elseif($q->status_approval == 4){
                            return '<a href="javascript:void(0)" class="info-ditolak" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                        } elseif($q->status_approval == 3) {
                            return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="ACC Selesai" data-placement="bottom" data-original-title="ACC Selesai" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                        } else {
                            return '<span class="badge bg-label-secondary">Menunggu</span>';
                        }
                    }
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })
            ->rawColumns(['laporan','action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-proposal.index-laporan');
    }

    public function selesailaporan(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->proposal_id)->update([
            'status_approval' => 5,
            'generate_qrcode' => ''.URL::to('/').'/report/'.time().'.png'
        ]);
        return response()->json($post);
    }

    public function approvalRektorN(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->propsl_id)->update([
            'status_approval'       => 4,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }

    public function indexUndanganFpku(Request $request)
    {
        if($request->status == '' || $request->status == 'all'){
            $datas = DataFpku::orderBy('id','DESC')->get();
        }elseif($request->status == 'pending'){
            $datas = DataFpku::leftJoin('status_fpkus','status_fpkus.id_fpku','=','data_fpkus.id')
                ->where('status_fpkus.status_approval',1)->select('data_fpkus.id AS id','data_fpkus.*','status_fpkus.status_approval')->get();
        }elseif($request->status == 'accepted'){
            $datas = DataFpku::leftJoin('status_fpkus','status_fpkus.id_fpku','=','data_fpkus.id')
                ->where('status_fpkus.status_approval',2)->select('data_fpkus.id AS id','data_fpkus.*','status_fpkus.status_approval')->get();
        }else{
            $datas = DataFpku::orderBy('id','DESC')->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkState = DB::table('status_fpkus')->where('id_fpku',$data->id)->select('status_approval')->first();
                if($checkState->status_approval == 1){
                    return '<a href="javascript:void(0)" name="validasi" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Validasi Undangan" data-placement="bottom" data-original-title="Validasi Undangan" class="btn btn-warning btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>&nbsp;<div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div>';
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-success btn-sm disabled"><i class="bx bx-xs bx-check-double"></i></a>';
                }
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-undangan',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Undangan" data-original-title="Preview Undangan" class="preview-undangan">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_fpkus')->where('id_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran" style="font-size: 10px;">lihat lampiran</a>';
                } else {
                    return '<p style="font-size: 10px;">No attachment</p>';
                }
            })
            ->rawColumns(['action','nama_pegawai','undangan','lampirans'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-proposal.index-undangan-fpku');
    }

    public function confirmUndanganFpku(Request $request)
    {
        $post = DB::table('status_fpkus')->where('id_fpku',$request->id)->update([
            'status_approval' => 2,
            'generate_qrcode' => ''.URL::to('/').'/fpku/'.time().'.png'
        ]);

        # setelah di confirm / validasi oleh WRSDP otomatis broadcast ke email peserta
        $datas = DataFpku::where('id',$request->id)->select('peserta_kegiatan')->get();
        if($datas->count() > 0){
            foreach($datas as $data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('email')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->email;                    
                }
            }
        } else {
            return 'Nothing data in the table';
        }
        $emails = implode(", ", $pegawai);
        $isiData = [
            'name' => 'Form Partisipasi Kegiatan Undangan',
            'body' => 'Anda memiliki undangan kegiatan, untuk info lebih detail, silakan login di akun SIMPRO anda. Pada menu Undangan FPKU - Undangan.',
        ];
        Mail::to([$emails])->send(new UndanganFpku($isiData));
        $post = DB::table('status_fpkus')->update([
            'broadcast_email' => 1
        ]);

        return response()->json($post);
    }

    public function indexLaporanFpku(Request $request)
    {
        if($request->status == '' || $request->status == 'all'){
            $datas = LaporanFpku::leftJoin('data_fpkus','data_fpkus.id','=','laporan_fpkus.id_fpku')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('laporan_fpkus.id_fpku AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval')
                ->orderBy('status_laporan_fpkus.status_approval','ASC')
                ->get();
        }elseif($request->status == 'pending'){
            $datas = LaporanFpku::leftJoin('data_fpkus','data_fpkus.id','=','laporan_fpkus.id_fpku')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('laporan_fpkus.id_fpku AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval')
                ->where('status_laporan_fpkus.status_approval',1)
                ->orderBy('status_laporan_fpkus.status_approval','ASC')
                ->get();
        }elseif($request->status == 'accepted'){
            $datas = LaporanFpku::leftJoin('data_fpkus','data_fpkus.id','=','laporan_fpkus.id_fpku')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('laporan_fpkus.id_fpku AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval')
                ->where('status_laporan_fpkus.status_approval',3)
                ->orderBy('status_laporan_fpkus.status_approval','ASC')
                ->get();
        }elseif($request->status == 'denied'){
            $datas = LaporanFpku::leftJoin('data_fpkus','data_fpkus.id','=','laporan_fpkus.id_fpku')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('laporan_fpkus.id_fpku AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval')
                ->where('status_laporan_fpkus.status_approval',2)
                ->orderBy('status_laporan_fpkus.status_approval','ASC')
                ->get();
        }else{
            $datas = LaporanFpku::leftJoin('data_fpkus','data_fpkus.id','=','laporan_fpkus.id_fpku')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->select('laporan_fpkus.id_fpku AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval')
                ->orderBy('status_laporan_fpkus.status_approval','ASC')
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->status_approval == 3){
                    return '<a href="javascript:void(0)" class="text-success"><i class="bx bx-xs bx-check-shield"></i> validated</a>';
                } elseif($data->status_approval == 2){
                    return '<a href="javascript:void(0)" class="text-danger"><i class="bx bx-xs bx-shield-x"></i> denied</a>';
                } else {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_laporan.'" data-placement="bottom" title="Tolak" data-original-title="Tolak" class="tombol-no-laporan"><i class="bx bx-sm bx-shield-x text-danger"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju" data-placement="bottom" data-original-title="Setuju" class="tombol-yes-laporan"><i class="bx bx-sm bx-check-shield text-success"></i></a>&nbsp;<div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span>';                   
                }
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_laporan_fpkus')->where('id_laporan_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran-laporan-fpku" style="font-size: 10px;">lihat lampiran</a>';
                } else {
                    return '<p style="font-size: 10px;">No attachment</p>';
                }
            })
            ->rawColumns(['action','undangan','lampirans'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-proposal.index-laporan-fpku');
    }

    public function confirmLaporanFpku(Request $request)
    {
        $post = DB::table('status_laporan_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','status_laporan_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$request->id)->update([
            'status_approval' => 3,
            'generate_qrcode' => ''.URL::to('/').'/fpku-rep/'.time().'.png'
        ]);
        return response()->json($post);
    }
    public function ignoreLaporanFpku(Request $request)
    {
        $post = DB::table('status_laporan_fpkus')->where('id_laporan_fpku',$request->id_laporan)->update([
            'status_approval' => 2,
            'keterangan_ditolak' => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }

    public function viewlampiranLaporanFpku(Request $request)
    {
        $datas = DB::table('lampiran_laporan_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','lampiran_laporan_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$request->fpku_id)->select('lampiran_laporan_fpkus.id','lampiran_laporan_fpkus.nama_berkas','lampiran_laporan_fpkus.berkas','lampiran_laporan_fpkus.keterangan','lampiran_laporan_fpkus.link_gdrive')->get();
        $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Berkas</th>
                            <th>Lihat</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if($datas->count() > 0){
                        foreach($datas as $no => $data){
                            $html .= 
                                '<tr>
                                    <td>'.++$no.'</td>
                                    <td>'.$data->nama_berkas.'</td>
                                    <td>';
                                    if($data->berkas != ''){
                                        $html .= '<button type="button" name="view" id="'.$data->id.'" class="view btn btn-outline-primary btn-sm"><a href="'.asset('/'.$data->berkas).'" target="_blank"><i class="bx bx-show"></i></a></button>';
                                    } else {
                                        $html .= '<a href="'.$data->link_gdrive.'" target="_blank">'.$data->link_gdrive.'</a>';
                                    }
                                    
                                    $html .= '</td>
                                </tr>';
                        }
                    } else {
                        $html .= 
                        '<tr>
                            <td colspan="3"> No data available in table </td>
                        </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }
}
