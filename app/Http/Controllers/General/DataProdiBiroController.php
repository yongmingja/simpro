<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFakultasBiro;
use App\Models\General\DataProdiBiro;

class DataProdiBiroController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataProdiBiro::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','data_prodi_biros.id_fakultas_biro')
            ->select('data_prodi_biros.id AS id','data_prodi_biros.*','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.created_at AS created_at','data_prodi_biros.updated_at AS updated_at')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';

                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataFakultasBiro = DataFakultasBiro::all();
        return view('general.data-prodi-biro.index', compact('getDataFakultasBiro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi_biro'    => 'required',
            'kode_prodi_biro'    => 'required',
            'id_fakultas_biro'   => 'required',
        ],[
            'nama_prodi_biro.required'   => 'Anda belum menginputkan nama prodi atau biro',
            'kode_prodi_biro.required'   => 'Anda belum menginputkan kode prodi atau biro',
            'id_fakultas_biro.required'  => 'Anda belum memilih fakultas atau biro',
        ]);

        $post = DataProdiBiro::updateOrCreate(['id' => $request->id],
                [
                    'nama_prodi_biro'    => $request->nama_prodi_biro,
                    'kode_prodi_biro'    => $request->kode_prodi_biro,
                    'id_fakultas_biro'   => $request->id_fakultas_biro,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = DataProdiBiro::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = DataProdiBiro::where('id',$id)->delete();     
        return response()->json($post);
    }
}
