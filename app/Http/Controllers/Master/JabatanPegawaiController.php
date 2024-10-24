<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\Pegawai;
use App\Models\Master\Jabatan;

class JabatanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = JabatanPegawai::leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->select('jabatan_pegawais.id AS id','pegawais.id AS id_pegawai','pegawais.nama_pegawai','pegawais.user_id','jabatans.nama_jabatan')
            ->get();
        $datas = $query->unique('id_pegawai');
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';

                return $button;
            })->addColumn('jabatan_nama', function($data){
                $getJabatan = Jabatan::where('golongan_jabatan', 2)->get();
                $jabeg = JabatanPegawai::where([['id_pegawai', $data->id_pegawai]])->get();
                $jabatan = array();
                foreach ($getJabatan as $dataJabatan) {
                    foreach ($jabeg as $data) {
                        if($data->id_jabatan == $dataJabatan->id)
                        {
                            $jabatan[] = '<span class="badge '.$dataJabatan->warna_label.'">'.$dataJabatan->nama_jabatan.'</span>'.'&nbsp;';
                        }
                    }
                }
                return implode($jabatan);
            })
            ->rawColumns(['action','jabatan_nama'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getPegawai = Pegawai::select('id','user_id','nama_pegawai')->orderBy('nama_pegawai','ASC')->get();
        $getJabatan = Jabatan::select('id','nama_jabatan')->where('golongan_jabatan',2)->orderBy('nama_jabatan','ASC')->get();
        return view('master.jabatan-pegawai.index', compact('getPegawai','getJabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required',
            'id_jabatan' => 'required',
        ],[
            'id_pegawai.required' => 'Anda belum memilih pegawai',
            'id_jabatan.required' => 'Anda belum memilih jabatan',
        ]);

        $post = JabatanPegawai::updateOrCreate(['id' => $request->id],
                [
                    'id_pegawai' => $request->id_pegawai,
                    'id_jabatan' => $request->id_jabatan,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = JabatanPegawai::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = JabatanPegawai::where('id',$id)->delete();     
        return response()->json($post);
    }
}
