<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFakultasBiro;

class DataFakultasBiroController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataFakultasBiro::all();

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
        return view('general.data-fakultas-biro.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas_biro' => 'required',
            'kode_fakultas_biro' => 'required',
        ],[
            'nama_fakultas_biro.required' => 'Anda belum menginputkan nama fakultas atau biro',
            'kode_fakultas_biro.required' => 'Anda belum menginputkan kode fakultas atau biro',
        ]);

        $post = DataFakultasBiro::updateOrCreate(['id' => $request->id],
                [
                    'nama_fakultas_biro' => $request->nama_fakultas_biro,
                    'kode_fakultas_biro' => $request->kode_fakultas_biro,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = DataFakultasBiro::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = DataFakultasBiro::where('id',$id)->delete();     
        return response()->json($post);
    }
}
