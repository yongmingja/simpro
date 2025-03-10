<?php

namespace App\Http\Controllers\DekanPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\LaporanProposal;
use App\Models\Master\HandleProposal;
use App\Models\Master\JabatanPegawai;
use Auth; use DB;

class LaporanProposalController extends Controller
{
    public function index(Request $request)
    {
        $getJabatanIs = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->where([['jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id],['jabatans.kode_jabatan','=','PEG']]) # Remember this is not only for DKN but for BRO as well, so this is not the best query
            ->select('jabatan_pegawais.id_fakultas_biro')
            ->first();

        if($request->status == '' || $request->status == 'all'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where('proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro)
                ->get();
        }
        if($request->status == 'pending'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',1]])
                ->get();
        }
        if($request->status == 'accepted'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',5]])
                ->get();
        }
        if($request->status == 'denied'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',4]])
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $query = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i> view report</a>';
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })->addColumn('action', function($data){
                $query = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->get();
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
        return view('dekan-page.laporan-proposal.index');
    }

    protected function statusLaporanProposal($id)
    {
        $query = DB::table('status_laporan_proposals')->select('status_approval','keterangan_ditolak')->where('id_laporan_proposal','=',$id)->get();
        if($query){
            foreach($query as $data){                
                if($data->status_approval == 1){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                } elseif($data->status_approval == 2){
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 3) {
                    return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Diterima</span>';
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

    public function approvalDeanY(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->proposal_id)->update(['status_approval' => 3, 'keterangan_ditolak' => '']);
        return response()->json($post);
    }

    public function approvalDeanN(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->propsl_id)->update([
            'status_approval'       => 2,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }
}
