<?php

namespace App\Http\Controllers\DekanPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataRencanaAnggaran;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\JabatanAkademik;
use Auth;
use DB;

class DataProposalController extends Controller
{
    public function index(Request $request)
    {
        $checkJabatanAk = jabatanAkademik::leftJoin('jabatans','jabatans.id','=','jabatan_akademiks.id_jabatan')
            ->where('jabatan_akademiks.id_pegawai',Auth::guard('pegawai')->user()->id)
            ->select('jabatan_akademiks.id_fakultas')
            ->first();

        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai AS nama_user','mahasiswas.name AS nama_user')
            ->where('proposals.id_fakultas',$checkJabatanAk->id_fakultas)
            ->orderBy('status_proposals.status_approval','ASC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $btn = $this->statusProposal($data->id);                
                return $btn;
            })->addColumn('preview', function($data){
                # check any attachment
                $q = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($q > 0){
                    $button = '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="btn btn-outline-info btn-sm v-lampiran"><i class="bx bx-xs bx-file"></i></a>';
                    return $button;
                } else {
                    return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>';
                }
            })
            ->rawColumns(['action','preview'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dekan-page.data-proposal.index');
    }

    public function rencana(Request $request)
    {
        $datas = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->get();
        
            $html = '<div class="card">
            <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Biaya Satuan</th>
                    <th>Qty</th>
                    <th>Freq</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>';
        foreach($datas as $no => $data){
            $total = 0;
            $grandTotal = 0;
            $html .= '<tr>
                    <td>'.++$no.'</td>
                    <td>'.$data->item.'</td>
                    <td>'.currency_IDR($data->biaya_satuan).'</td>
                    <td>'.$data->quantity.'</td>
                    <td>'.$data->frequency.'</td>';
            $total = $data->biaya_satuan * $data->quantity * $data->frequency; 
            $html .= '<td>'.currency_IDR($total).'</td>
                    </tr>';
                    
            }
            $grandTotal = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->select(DB::raw('sum(biaya_satuan * quantity * frequency) as total'))->get()->sum('total');
            $html .= '<tr><td colspan="5" style="text-align: right;"><b>Grand Total</b></td>
                    <td><b>'.currency_IDR($grandTotal).'</b></td>
                </tr>';

                $html .= '</tbody>
                </table>            
                </div>            
            </div>';
            $html .= '<div class="modal-footer mt-3">';
            if($data->status != '1'){
                $html .= '<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>';
            } else {
                $html .= '<button type="submit" class="btn btn-primary btn-block tombol-validasi" id="'.$request->proposal_id.'">Validasi</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>';
            }
                $html .= '</div>';
        return response()->json(['card' => $html]);
    }

    public function approvalDeanY(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update(['status_approval' => 3, 'keterangan_ditolak' => '']);
        return response()->json($post);
    }

    public function approvalDeanN(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->propsl_id)->update([
            'status_approval'       => 2,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }

    protected function statusProposal($id)
    {
        $query = DB::table('status_proposals')->select('status_approval','keterangan_ditolak')->where('id_proposal','=',$id)->get();
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
                    return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Diterima WR</span>';
                } else {
                    return '<span class="badge bg-label-secondary">Pending</span>';
                }
            }
        } else {
            return 'x';
        }
    }
}
