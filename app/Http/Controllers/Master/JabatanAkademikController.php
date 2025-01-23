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
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_pegawai.'" data-toggle="tooltip" data-placement="bottom" title="Lihat Jabatan" data-original-title="Lihat Jabatan" class="edit btn btn-primary btn-xs edit-post"><i class="bx bx-xs bx-show"></i></a>';
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
                    'id_pegawai'    => $request->id_pegawai,
                    'id_jabatan'    => $request->id_jabatan,
                    'id_fakultas'   => $request->id_fakultas,
                    'id_prodi'      => $request->id_prodi,
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

    public function checkjabatanakademik(Request $request)
    {
        $datas = JabatanAkademik::leftJoin('pegawais','pegawais.id','=','jabatan_akademiks.id_pegawai')
            ->leftJoin('jabatans','jabatans.id','=','jabatan_akademiks.id_jabatan')
            ->leftJoin('data_fakultas','data_fakultas.id','=','jabatan_akademiks.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','jabatan_akademiks.id_prodi')
            ->select('jabatan_akademiks.id AS id','pegawais.id AS id_pegawai','pegawais.nama_pegawai','pegawais.user_id','jabatans.nama_jabatan','data_prodis.nama_prodi','data_fakultas.nama_fakultas')
            ->where('jabatan_akademiks.id_pegawai',$request->user_id)
            ->orderBy('jabatan_akademiks.id','DESC')
            ->get();

        foreach($datas as $employee){
            $html = '<table class="table table-borderless table-sm" style="line-height:0.5em;">
                <tr>
                    <td width="25%;">Nama Pegawai</td>
                    <td>:&nbsp;&nbsp;'.$employee->nama_pegawai.'</td>
                </tr>
                <tr>
                    <td>N I P</td>
                    <td>:&nbsp;&nbsp;'.$employee->user_id.'</td>
                </tr>                
            </table>';
        }

        $html .= '<table class="table table-bordered table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
        if($datas->count() > 0){
            foreach($datas as $no => $item){
                if($item->nama_jabatan == "Dosen"){
                    $varName = $item->nama_jabatan.'&nbsp;<span class="badge bg-label-warning">'.str_replace("Program Studi","",$item->nama_prodi).'</span>';
                } else {
                    $varName = $item->nama_jabatan;
                }
                $html .= '<tr>
                        <td>'.++$no.'</td>
                        <td>'.$varName.'</td>
                        <td><button type="button" name="delete" id="'.$item->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button></td>';
            }
            $html .= '</tbody>
                </table>';
        } else {
            $html .= '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">No data available in table</td>
                        </tr>
                    </tbody>';
        }
        return response()->json(['card' => $html]);
    }
}
