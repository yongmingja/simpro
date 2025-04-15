<?php

namespace App\Http\Controllers\RektoratPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\FormRkat;
use Auth;

class FormRkatController extends Controller
{
    public function index(Request $request)
    {
        $datas = FormRkat::leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','form_rkats.id_fakultas_biro')
            ->select('form_rkats.id AS id','form_rkats.*','data_fakultas_biros.nama_fakultas_biro')
            ->where('tahun_akademiks.is_active',1)
            ->orderBy('form_rkats.status_validasi','ASC')
            ->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->status_validasi == 1){
                    return '<small><i class="text-success">ACC Rektorat</i></small>';
                } elseif($data->status_validasi == 2){
                    return '<small><i class="text-danger">Ditolak</i></small>';
                } else {
                    $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Tolak" data-original-title="Tolak" class="tombol-no btn btn-xs btn-danger"><i class="bx bx-x bx-xs"></i></a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju" data-placement="bottom" data-original-title="Setuju" class="tombol-yes btn btn-xs btn-success"><i class="bx bx-xs bx-check-double"></i></a>';
                    return $button;
                }
            })
            ->rawColumns(['action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.form-rkat.index');
    }

    public function approvalY(Request $request)
    {
        $post = FormRkat::where('id',$request->rkat_id)->update([
            'status_validasi'   => 1,
            'validated_by'      => Auth::user()->id
        ]);
        return response()->json($post);
    }

    public function approvalN(Request $request)
    {
        $post = FormRkat::where('id',$request->rkat_id)->update([
            'status_validasi'   => 2,
            'validated_by'      => Auth::user()->id
        ]);
        return response()->json($post);
    }
}
