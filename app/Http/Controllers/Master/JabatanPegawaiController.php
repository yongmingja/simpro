<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\Pegawai;
use App\Models\Master\Jabatan;
use App\Models\General\DataFakultasBiro;
use Auth;

class JabatanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = JabatanPegawai::leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->select('jabatan_pegawais.id AS id','jabatan_pegawais.id_jabatan','pegawais.id AS id_pegawai','pegawais.nama_pegawai','pegawais.user_id','jabatans.id AS idjab','jabatans.nama_jabatan','jabatan_pegawais.ket_jabatan','jabatans.kode_jabatan')
            ->orderBy('pegawais.nama_pegawai','ASC')
            ->get();
        $datas = $query->unique('id_pegawai');
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_pegawai.'" data-toggle="tooltip" data-placement="bottom" title="Lihat Jabatan" data-original-title="Lihat Jabatan" class="edit btn btn-primary btn-xs edit-post"><i class="bx bx-xs bx-show"></i></a>';
            })->addColumn('jabatan_nama', function($data){
                $getJabatan = Jabatan::all();
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
            })->addColumn('ket_jabatan', function($data){
                $getJabatan = Jabatan::all();
                $jabeg = JabatanPegawai::where([['id_pegawai', $data->id_pegawai]])->get();
                $jabatan = array();
                foreach ($getJabatan as $dataJabatan) {
                    foreach ($jabeg as $data) {
                        if($data->id_jabatan == $dataJabatan->id)
                        {
                            $jabatan[] = '<span class="badge '.$dataJabatan->warna_label.'">'.$data->ket_jabatan.'</span>'.'&nbsp;';
                        }
                    }
                }
                return implode($jabatan);
            })
            ->rawColumns(['action','jabatan_nama','ket_jabatan'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getPegawai = Pegawai::select('id','user_id','nama_pegawai')->orderBy('nama_pegawai','ASC')->get();
        $getJabatan = Jabatan::select('id','nama_jabatan')->orderBy('nama_jabatan','ASC')->get();
        $getFakultasBiro = DataFakultasBiro::select('id','nama_fakultas_biro')->orderBy('id','ASC')->get();
        return view('master.jabatan-pegawai.index', compact('getPegawai','getJabatan','getFakultasBiro'));
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
                    'id_pegawai'        => $request->id_pegawai,
                    'id_jabatan'        => $request->id_jabatan,
                    'id_fakultas_biro'  => $request->id_fakultas_biro,
                    'ket_jabatan'       => $request->ket_jabatan,
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

    public function checkjabatan(Request $request)
    {
        $datas = JabatanPegawai::leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->select('jabatan_pegawais.id AS id','jabatan_pegawais.ket_jabatan','pegawais.id AS id_pegawai','pegawais.nama_pegawai','pegawais.user_id','jabatans.kode_jabatan','jabatans.nama_jabatan')
            ->where('jabatan_pegawais.id_pegawai',$request->user_id)
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
                $html .= '<tr>
                        <td>'.++$no.'</td>
                        <td>'.$item->ket_jabatan.'</td>
                        <td>';
                        if($item->id_pegawai == Auth::guard('pegawai')->user()->id AND $item->kode_jabatan == "SADM"){
                            $html .= '<button type="button" data-toggle="tooltip" data-placement="bottom" title="Unable to remove" class="btn btn-secondary btn-xs"><i class="bx bx-xs bx-trash" style="cursor:not-allowed;"></i></button></td>';
                        } else {
                            $html .= '<button type="button" name="delete" id="'.$item->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button></td>';
                        }
                        
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
