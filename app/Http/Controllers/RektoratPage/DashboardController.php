<?php

namespace App\Http\Controllers\RektoratPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use App\Models\General\LaporanFpku;
use Auth;
use DB;
use URL;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai AS nama_user','mahasiswas.name AS nama_user')
            ->where('proposals.is_archived',0)
            ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan()) // filter WR yang akan handle pengecekan proposal, namun diubah semua default ke role WRAK
            ->orderBy('status_proposals.status_approval','ASC')
            ->get();

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
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="View Lampiran" data-original-title="View Lampiran" class="btn btn-info btn-sm v-lampiran"><i class="bx bx-xs bx-show"></i></a>';
                }else{
                    return '<small>Tidak ada lampiran</small>';
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
        $datas = DB::table('handle_proposals')->select('id_jenis_kegiatan')->where('user_id',Auth::user()->user_id)->get();
        if($datas){
            foreach($datas as $data){
                $getID = $data->id_jenis_kegiatan;
            }
        }else{
            $getID = '';
        }
        $exp_arr = explode(",",$getID);
        return $exp_arr;
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
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai','mahasiswas.name AS nama_user','laporan_proposals.created_at AS tgl_proposal')
            ->whereIn('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
            ->orderBy('laporan_proposals.status_laporan','ASC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $query = LaporanProposal::where('id_proposal',$data->id)->select('status_laporan')->get();
                if($query->count() > 0){
                    return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i> view report</a>';
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })->addColumn('action', function($data){
                $query = LaporanProposal::where('id_proposal',$data->id)->select('status_laporan')->get();
                if($query->count() > 0){
                    foreach($query as $q){
                        if($q->status_laporan == 1){
                            return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Sudah diverifikasi</span>';
                        } else {
                            return '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-outline-warning btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i> Click as done <div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div></a>';
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
        return view('general.rektorat.index-laporan');
    }

    public function selesailaporan(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->proposal_id)->update([
            'status_laporan' => 1,
            'qrcode' => ''.URL::to('/').'/report/'.time().'.png'
        ]);
        return response()->json($post);
    }

    public function indexUndanganFpku(Request $request)
    {
        $datas = DataFpku::orderBy('id','DESC')->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkState = DB::table('status_fpkus')->where('id_fpku',$data->id)->select('status_approval')->first();
                if($checkState->status_approval == 1){
                    return '<a href="javascript:void(0)" name="validasi" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Validasi Undangan" data-placement="bottom" data-original-title="Validasi Undangan" class="btn btn-warning btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a><div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div>';
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
            })
            ->rawColumns(['action','nama_pegawai','undangan'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.rektorat.index-undangan-fpku');
    }

    public function confirmUndanganFpku(Request $request)
    {
        $post = DB::table('status_fpkus')->where('id_fpku',$request->id)->update([
            'status_approval' => 2,
            'generate_qrcode' => ''.URL::to('/').'/fpku/'.time().'.png'
        ]);
        return response()->json($post);
    }

    public function indexLaporanFpku(Request $request)
    {
        $datas = DataFpku::orderBy('id','DESC')->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkState = LaporanFpku::where('id_fpku',$data->id)->select('status_laporan')->get();
                if($checkState->count() > 0){
                    foreach($checkState as $rs){
                        if($rs->status_laporan == 2){
                            return '<a href="javascript:void(0)" class="btn btn-success btn-sm disabled"><i class="bx bx-xs bx-check-double"></i></a>';
                        } else {
                            return '<a href="javascript:void(0)" name="validasi" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Validasi Laporan" data-placement="bottom" data-original-title="Validasi Laporan" class="btn btn-warning btn-sm tombol-yes-laporan"><i class="bx bx-xs bx-check-double"></i></a><div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div>';                    
                        }
                    }
                } else {
                    return "<span class='badge bg-label-secondary'>Belum submit</span>";
                }
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku">'.$data->undangan_dari.'</a>';
            })
            ->rawColumns(['action','nama_pegawai','undangan'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.rektorat.index-laporan-fpku');
    }

    public function confirmLaporanFpku(Request $request)
    {
        $post = LaporanFpku::where('id_fpku',$request->id)->update([
            'status_laporan' => 2,
            'qrcode' => ''.URL::to('/').'/fpku-rep/'.time().'.png'
        ]);
        return response()->json($post);
    }
}
