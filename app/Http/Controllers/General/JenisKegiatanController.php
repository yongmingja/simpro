<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\JenisKegiatan;

class JenisKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $datas = JenisKegiatan::all();

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
        return view('general.jenis-kegiatan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis_kegiatan' => 'required',
        ],[
            'nama_jenis_kegiatan.required' => 'Anda belum menginputkan nama kategori',
        ]);

        $post = JenisKegiatan::updateOrCreate(['id' => $request->id],
                [
                    'nama_jenis_kegiatan' => $request->nama_jenis_kegiatan,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = JenisKegiatan::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = JenisKegiatan::where('id',$id)->delete();     
        return response()->json($post);
    }
}
