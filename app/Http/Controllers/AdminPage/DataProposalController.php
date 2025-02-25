<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataPengajuanSarpras;
use Response;
use DB;

class DataProposalController extends Controller
{
    public function index(Request $request)
    {
        $datas = Proposal::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->select('proposals.id AS id','proposals.*','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','jenis_kegiatans.nama_jenis_kegiatan')
            ->orderBy('proposals.id','DESC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $countVal = DataPengajuanSarpras::where([['id_proposal',$data->id],['status','1']])->get();
                if($countVal->count() > 0){
                    foreach($countVal as $result){
                        $totalData = $countVal->count();
                        if($result->status == '1'){
                            return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Validasi Sarpras" data-original-title="Validasi Sarpras" class="validasi-proposal"><span class="badge bg-label-primary badge-sm">Validasi sarpras</span><span class="badge bg-danger badge-notifications">'.$totalData.'</span></a>';
                        } else {
                            return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Validasi Sarpras" data-original-title="Validasi Sarpras" class="validasi-proposal"><span class="badge bg-label-primary badge-sm">Validasi sarpras</span></a>';
                        }
                    }
                } else {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Validasi Sarpras" data-original-title="Validasi Sarpras" class="validasi-proposal"><span class="badge bg-label-primary badge-sm">Validasi sarpras</span></a>';
                }
                
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
        return view('admin-page.data-proposal.index');
    }

    public function validasi(Request $request)
    {
        $datas = DataPengajuanSarpras::where('id_proposal',$request->proposal_id)->get();
        
            $html = '<div class="card">
            <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th><input type="checkbox" name="main_checkbox"><label></label></th>
                    <th>#</th>
                    <th>Tgl Kegiatan</th>
                    <th>Sarpras Item</th>
                    <th>Jumlah</th>
                    <th width="25%;">Aksi</th>
                </tr>
                </thead>
                <tbody>';
        foreach($datas as $no => $data){
            $html .= '<tr>
                <td><input type="checkbox" name="detail_checkbox" data-id="'.$data->id.'"><label></label></td>
                <td>'.++$no.'</td>
                <td>'.tanggal_indonesia($data->tgl_kegiatan).'</td>
                <td>'.$data->sarpras_item.'</td>
                <td>'.$data->jumlah.'</td>
                <td style="text-align: center;">';
                if($data->status == '1'){
                    $html .= '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                } else if($data->status == '2'){
                    $html .= '<span class="badge bg-label-success">Diterima</span>';
                } else if($data->status == '3'){
                    $html .= '<span class="badge bg-label-danger">Ditolak</span>';
                } else {
                    $html .= '<span class="badge bg-label-success">Diterima</span>';
                }
                $html .= '</td>   
            </tr>';
        }
            $html .= '</tbody>
                </table> 
                <div style="font-size: 12px;"><p class="mt-2 text-warning"><i>*Silahkan gunakan centang untuk validasi semua sarpras</i></p>   </div>        
                </div>            
            </div>';
        return response()->json(['card' => $html]);
    }

    public function validY(Request $request)
    {
        $post = DataPengajuanSarpras::where('id',$request->sarpras_id)->update([
            'status' => 2
        ]);
        return response()->json($post);
    }

    public function validN(Request $request)
    {
        $post = DataPengajuanSarpras::where('id',$request->sarpras_id)->update([
            'status' => 3,
            'alasan' => $request->alasan
        ]);
        return response()->json($post);
    }

    public function validYAll(Request $request)
    {
        $id   = $request->id;
        $post = DataPengajuanSarpras::whereIn('id', $id)->update([
            'status' => 2
        ]);
    
        if ($post) {
            return Response::json(array('msg' => 'berhasil'), 200);
        }else{
            return Response::json(array('msg' => 'gagal'), 200);
        }
    }
}
