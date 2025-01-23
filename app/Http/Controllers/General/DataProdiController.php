<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFakultas;
use App\Models\General\DataProdi;

class DataProdiController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataProdi::leftJoin('data_fakultas','data_fakultas.id','=','data_prodis.id_fakultas')
            ->select('data_prodis.id AS id','data_prodis.*','data_fakultas.nama_fakultas')
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
        $getDataFakultas = DataFakultas::all();
        return view('general.data-prodi.index', compact('getDataFakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_prodi'    => 'required',
            'kode_prodi'    => 'required',
            'id_fakultas'   => 'required',
        ],[
            'nama_prodi.required'   => 'Anda belum menginputkan nama prodi atau biro',
            'kode_prodi.required'   => 'Anda belum menginputkan kode prodi atau biro',
            'id_fakultas.required'  => 'Anda belum memilih fakultas atau biro',
        ]);

        $post = DataProdi::updateOrCreate(['id' => $request->id],
                [
                    'nama_prodi'    => $request->nama_prodi,
                    'kode_prodi'    => $request->kode_prodi,
                    'id_fakultas'   => $request->id_fakultas,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = DataProdi::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = DataProdi::where('id',$id)->delete();     
        return response()->json($post);
    }
}
