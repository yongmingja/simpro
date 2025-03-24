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
            ->where('tahun_akademiks.is_active',1)
            ->orderBy('form_rkats.status_validasi','ASC')
            ->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->status_validasi == 1){
                    return '<i class="text-success">ACC Rektorat</i>';
                } elseif($data->status_validasi == 2){
                    return '<i class="text-danger">Ditolak</i>';
                } else {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Tolak" data-original-title="Tolak" class="tombol-no"><i class="bx bx-sm bx-shield-x text-danger"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju" data-placement="bottom" data-original-title="Setuju" class="tombol-yes"><i class="bx bx-sm bx-check-shield text-success"></i></a>';
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
