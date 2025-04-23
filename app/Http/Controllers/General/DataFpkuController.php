<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFpku;
use App\Models\General\TahunAkademik;
use App\Models\Master\Pegawai;
use Illuminate\Support\Facades\Mail;
use App\Mail\UndanganFpku;
use DB;
use Auth;

class DataFpkuController extends Controller
{
    public function index(Request $request)
    {
        $datas = DataFpku::leftJoin('tahun_akademiks','tahun_akademiks.id','=','data_fpkus.id_tahun_akademik')
            ->leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
            ->select('data_fpkus.id AS id','data_fpkus.*','tahun_akademiks.year','pegawais.nama_pegawai AS ketua')
            ->where('tahun_akademiks.is_active',1)
            ->orderBy('data_fpkus.id','DESC')
            ->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkState = DB::table('status_fpkus')->where('id_fpku',$data->id)->select('status_approval')->first();
                if($checkState->status_approval == 1){
                    return '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-danger btn-xs disabled"><i class="bx bx-xs bx-trash"></i></a>';
                }
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })->addColumn('preview_undangan', function($data){
                return '<a href="'.Route('preview-undangan',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Preview FPKU" data-original-title="Preview FPKU" class="preview-undangan">'.$data->nama_kegiatan.'</a>';
            })
            ->rawColumns(['action','nama_pegawai','preview_undangan'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataPegawai = Pegawai::select('id','nama_pegawai')->get();
        $getTahunAkademik = TahunAkademik::select('id','year','is_active')->where('is_active',1)->get();
        $checkNumber = DataFpku::select('no_surat_undangan')->latest()->first();
        if($checkNumber == ''){
            $latestFpkuNumber = 'Belum ada data!';
        } else {
            $latestFpkuNumber = $checkNumber->no_surat_undangan;
        }
        return view('general.data-fpku.index', compact('getDataPegawai','getTahunAkademik','latestFpkuNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat_undangan' => 'required',
            'undangan_dari'     => 'required',
            'nama_kegiatan'     => 'required',
            'tgl_kegiatan'      => 'required',
            'berkas.*'          => 'file|mimes:pdf,doc,docx|max:2048',
        ],[
            'no_surat_undangan.required'    => 'Anda belum menginputkan no surat undangan',
            'undangan_dari.required'        => 'Anda belum menginputkan undangan dari',
            'nama_kegiatan.required'        => 'Anda belum menginputkan nama kegiatan',
            'tgl_kegiatan.required'         => 'Anda belum menginputkan tanggal kegiatan',
            'berkas.*.max'                  => 'Ukuran berkas tidak boleh melebihi 2MB', 
            'berkas.*.mimes'                => 'File harus berjenis (pdf atau docx)',
        ]);

        $checkDate = $request->input('cek_tanggal');
        if($checkDate == null) { $checkDate = $request->input('cek_tanggal') ?? 0; } 
        else { $checkDate = $request->input('cek_tanggal') ?? 1; }

        $getIdTahunAkademik = TahunAkademik::where('is_active',1)->select('id')->first();

        $post = DataFpku::updateOrCreate(['id' => $request->id],
        [
            'cek_tanggal'       => $checkDate,
            'id_tahun_akademik' => $getIdTahunAkademik->id,
            'no_surat_undangan' => $request->no_surat_undangan,
            'undangan_dari'     => $request->undangan_dari,
            'nama_kegiatan'     => $request->nama_kegiatan,
            'tgl_kegiatan'      => $request->tgl_kegiatan,
            'ketua'             => $request->ketua,
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

        $dataSet = [];
        foreach($request->kolom as $key => $keperluan){
            if(!empty($keperluan['isi_keperluan'])) {
                $dataSet[] = [
                    'id_fpku'           => $latest,
                    'isi_keperluan'     => $keperluan['isi_keperluan'],
                    'created_at'        => now(),
                    'updated_at'        => now()
                ];
            }
        }  
        if (!empty($dataSet)){
            $post = DB::table('fpku_keperluans')->insert($dataSet);
        }            

        # Insert data into lampiran_fpkus
        if($request->berkas != ''){
            $fileNames = [];
            foreach($request->berkas as $file){
                $fileName = md5(time().'_'.Auth::user()->user_id).$file->getClientOriginalName();
                $file->move(public_path('uploads-lampiran/lampiran-fpku'),$fileName);
                $fileNames[] = 'uploads-lampiran/lampiran-fpku/'.$fileName;
            }

            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){

                if(!empty($fileNames[$x])) {
                    $default_nama_berkas = !empty($request->nama_berkas[$x]) ? $request->nama_berkas[$x] : time().'-default';
                    
                    $insertData[] = [
                        'id_fpku'       => $latest,
                        'nama_berkas'   => $default_nama_berkas,
                        'berkas'        => $fileNames[$x],
                        'link_gdrive'   => '',
                        'keterangan'    => '-',
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }

            }
            if (!empty($insertData)){
                $post = DB::table('lampiran_fpkus')->insert($insertData);
            }
        }

        $post = DB::table('status_fpkus')->insert([
            'id_fpku'           => $latest,
            'status_approval'   => 1,
            'broadcast_email'   => 0,
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

    public function viewlampiranfpku(Request $request)
    {
        $datas = DB::table('lampiran_fpkus')->where('id_fpku',$request->fpku_id)->select('id','nama_berkas','berkas','keterangan')->get();
        $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Berkas</th>
                            <th>Lihat</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($datas as $no => $data){
                        $html .= 
                            '<tr>
                                <td>'.++$no.'</td>
                                <td>'.$data->nama_berkas.'</td>
                                <td>';
                                if($data->berkas != ''){
                                    $html .= '<button type="button" name="view" id="'.$data->id.'" class="view btn btn-outline-primary btn-sm"><a href="'.asset('/'.$data->berkas).'" target="_blank"><i class="bx bx-show"></i></a></button>';
                                } else {
                                    $html .= '<a href="'.$data->link_gdrive.'" target="_blank">'.$data->link_gdrive.'</a>';
                                }
                                
                                $html .= '</td>
                            </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }

}
