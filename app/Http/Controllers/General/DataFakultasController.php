<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFakultas;

class DataFakultasController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataFakultas::all();

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
        return view('general.data-fakultas.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required',
            'kode_fakultas' => 'required',
        ],[
            'nama_fakultas.required' => 'Anda belum menginputkan nama fakultas atau biro',
            'kode_fakultas.required' => 'Anda belum menginputkan kode fakultas atau biro',
        ]);

        $post = DataFakultas::updateOrCreate(['id' => $request->id],
                [
                    'nama_fakultas' => $request->nama_fakultas,
                    'kode_fakultas' => $request->kode_fakultas,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = DataFakultas::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = DataFakultas::where('id',$id)->delete();     
        return response()->json($post);
    }
}
