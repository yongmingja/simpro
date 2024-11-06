<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use DB;
use Auth;

class DataFpkuController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataFpku::all();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })
            ->rawColumns(['action','nama_pegawai'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataPegawai = Pegawai::select('id','nama_pegawai')->get();
        return view('general.data-fpku.index', compact('getDataPegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat_undangan' => 'required',
            'undangan_dari'     => 'required',
            'nama_kegiatan'     => 'required',
            'tgl_kegiatan'      => 'required',
        ],[
            'no_surat_undangan.required'    => 'Anda belum menginputkan no surat undangan',
            'undangan_dari.required'        => 'Anda belum menginputkan undangan dari',
            'nama_kegiatan.required'        => 'Anda belum menginputkan nama kegiatan',
            'tgl_kegiatan.required'         => 'Anda belum menginputkan tanggal kegiatan',
        ]);

        $checkDate = $request->input('cek_tanggal');
        if($checkDate == null) { $checkDate = $request->input('cek_tanggal') ?? 0; } 
        else { $checkDate = $request->input('cek_tanggal') ?? 1; }

        // $checkDate = $request->input('cek_tanggal');
        // if($checkDate == 1){
        //     $today = date('Y-m-d', strtotime(now()));
        //     $checkDiff = date_diff($today, $request->tgl_kegiatan);
        //     if($checkDiff >= 14){
        //         # code ..
        //     }
        // }

        $post = DataFpku::updateOrCreate(['id' => $request->id],
        [
            'cek_tanggal'       => $checkDate,
            'no_surat_undangan' => $request->no_surat_undangan,
            'undangan_dari'     => $request->undangan_dari,
            'nama_kegiatan'     => $request->nama_kegiatan,
            'tgl_kegiatan'      => $request->tgl_kegiatan,
            'peserta_kegiatan'  => $request->input('id_pegawais'),
            'dibuat_oleh'       => Auth::user()->id,
            'catatan'           => $request->catatan
        ]);

        $latest_id = DataFpku::latest()->first();

        if($latest_id == ''){
            $latest = '1';
        } else {
            $latest = $latest_id->id;
        }

        foreach($request->kolom as $key => $keperluan){
            $dataSet[] = [
                'id_fpku'           => $latest,
                'isi_keperluan'     => $keperluan['isi_keperluan'],
                'created_at'        => now(),
                'updated_at'        => now()
            ];
        }                
        $post = DB::table('fpku_keperluans')->insert($dataSet);

        $post = DB::table('status_fpkus')->insert([
            'id_fpku'           => $latest,
            'status_approval'   => 1,
            'created_at'        => now(),
            'updated_at'        => now()
        ]);

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = DataFpku::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = DataFpku::where('id',$id)->delete();     
        return response()->json($post);
    }
}
