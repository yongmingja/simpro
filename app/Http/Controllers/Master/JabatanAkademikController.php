<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\JabatanAkademik;
use App\Models\Master\Pegawai;
use App\Models\Master\Jabatan;
use App\Models\General\DataFakultas;

class JabatanAkademikController extends Controller
{
    public function index(Request $request)
    {
        $query = JabatanAkademik::leftJoin('pegawais','pegawais.id','=','jabatan_akademiks.id_pegawai')
            ->leftJoin('jabatans','jabatans.id','=','jabatan_akademiks.id_jabatan')
            ->select('jabatan_akademiks.id AS id','pegawais.id AS id_pegawai','pegawais.nama_pegawai','pegawais.user_id','jabatans.nama_jabatan')
            ->orderBy('jabatan_akademiks.id','DESC')
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
                $getJabatan = Jabatan::where('golongan_jabatan', 1)->get();
                $jabeg = JabatanAkademik::where([['id_pegawai', $data->id_pegawai]])->get();
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
        $getJabatan = Jabatan::select('id','nama_jabatan')->where('golongan_jabatan',1)->orderBy('nama_jabatan','ASC')->get();
        $getFakultas = DataFakultas::select('id','nama_fakultas')->get();
        return view('master.jabatan-akademik.index', compact('getPegawai','getJabatan','getFakultas'));
    }

    public function faculties($id)
    {
        $datas = DataFakultas::leftJoin('data_prodis','data_prodis.id_fakultas','=','data_fakultas.id')
            ->where('data_prodis.id_fakultas',$id)
            ->pluck('data_prodis.nama_prodi','data_prodis.id');
        return json_encode($datas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required',
            'id_jabatan' => 'required',
            // 'id_fakultas' => 'required',
        ],[
            'id_pegawai.required' => 'Anda belum memilih pegawai',
            'id_jabatan.required' => 'Anda belum memilih jabatan',
            // 'id_fakultas.required' => 'Anda belum memilih fakultas',
        ]);

        $post = JabatanAkademik::updateOrCreate(['id' => $request->id],
                [
                    'id_pegawai' => $request->id_pegawai,
                    'id_jabatan' => $request->id_jabatan,
                    'id_fakultas' => $request->id_fakultas,
                    'id_prodi' => $request->id_prodi,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = JabatanAkademik::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = JabatanAkademik::where('id',$id)->delete();     
        return response()->json($post);
    }
}
