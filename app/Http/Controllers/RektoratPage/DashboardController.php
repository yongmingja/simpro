<?php

namespace App\Http\Controllers\RektoratPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use Auth;
use DB;
use URL;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $checkState = Auth::user()->id_jenis_kegiatan;
        $exp_arr = explode(",",$checkState);
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','dosens.name AS nama_user','mahasiswas.name AS nama_user')
            ->whereIn('proposals.id_jenis_kegiatan',$exp_arr)
            ->orderBy('proposals.id','DESC')
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
                            $button = '&nbsp;&nbsp;'; 
                            $button .= '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-outline-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                            $button .= '&nbsp;&nbsp;';
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-outline-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>';                            
                            return $button;
                        } elseif($state->status_approval == 4){
                            return '&nbsp;&nbsp;<span class="badge bg-label-danger">Ditolak</span>';
                        } elseif($state->status_approval == 5) {
                            return '&nbsp;&nbsp;<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Diterima</span>';
                        } else {
                            return '&nbsp;&nbsp;<span class="badge bg-label-secondary">Pending</span>';
                        }
                    }
                } else {
                    return '';
                }
            })
            ->rawColumns(['action','validasi'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dashboard.rektorat-dashboard');
    }

    public function approvalY(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update([
            'status_approval' => 5,
            'generate_qrcode' => ''.URL::to('/').'/in/'.time().'.png'
        ]);
        return response()->json($post);
    }

    public function approvalN(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update([
            'status_approval' => 4
        ]);
        return response()->json($post);
    }

    public function indexlaporan(Request $request)
    {
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','dosens.name AS nama_user','mahasiswas.name AS nama_user','laporan_proposals.created_at AS tgl_proposal')
            ->orderBy('proposals.id','DESC')
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
}
