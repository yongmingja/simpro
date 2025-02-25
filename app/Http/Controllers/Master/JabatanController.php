<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Jabatan;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $datas = Jabatan::orderBy('nama_jabatan','ASC')->get();

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
        return view('master.jabatan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jabatan'          => 'required',
            'nama_jabatan'          => 'required',
        ],[
            'kode_jabatan.required'     => 'Anda belum menginputkan kode jabatan',
            'nama_jabatan.required'     => 'Anda belum menginputkan nama jabatan',
        ]);

        $post = Jabatan::updateOrCreate(['id' => $request->id],
                [
                    'kode_jabatan'      => $request->kode_jabatan,
                    'nama_jabatan'      => $request->nama_jabatan,
                    'nama_jabatan'      => $request->nama_jabatan,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Jabatan::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Jabatan::where('id',$id)->delete();     
        return response()->json($post);
    }
}
