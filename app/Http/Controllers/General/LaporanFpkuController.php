<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFpku;
use App\Models\General\LaporanFpku;
use App\Models\General\DataFakultas;
use App\Setting\Dekan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Auth; use DB; use URL;

class LaporanFpkuController extends Controller
{
    public function buatLaporan(Request $request,$id)
    {
        $ID = decrypt($id);
        $getFakultas = DataFakultas::select('nama_fakultas','id')->get();
        return view('general.laporan-fpku.buat-laporan',['id' => $ID, 'getFakultas' => $getFakultas]);
    }

    public function faculties($id)
    {
        $datas = DataFakultas::leftJoin('data_prodis','data_prodis.id_fakultas','=','data_fakultas.id')
            ->where('data_prodis.id_fakultas',$id)
            ->pluck('data_prodis.nama_prodi','data_prodis.id');
        return json_encode($datas);
    }

    public function indexFpku(Request $request)
    {
        $userID = Auth::user()->id;
        $datas = DataFpku::whereRaw("JSON_CONTAINS(peserta_kegiatan,'\"$userID\"')")->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkStatus = LaporanFpku::where('id_fpku',$data->id)->get();
                if($checkStatus->count() > 0){
                    return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-bs-toggle="tooltip" data-id="'.$data->id.'" data-bs-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku"><i class="bx bx-food-menu bx-sm text-primary"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" data-bs-toggle="tooltip" data-id="'.$data->id.'" data-bs-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="v-lampiran"><i class="bx bx-show bx-sm text-info"></i></a>';
                    return $button;
                } else {
                    return '<a href="'.Route('buat-laporan-fpku',encrypt(['id' => $data->id])).'" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Buat Laporan Pertanggungjawaban" data-original-title="Buat Laporan Pertanggungjawaban"><i class="bx bx-plus-circle bx-tada-hover bx-sm text-primary"></i></a>';
                }
            })->addColumn('status', function($data){
                $state = LaporanFpku::where('id_fpku',$data->id)->select('status_laporan')->get();
                if($state->count() > 0){
                    foreach($state as $r){
                        if($r->status_laporan == 1){
                            return '<a href="javascript:void(0)" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Submitted" data-original-title="Submitted"><i class="bx bx-check-circle text-primary"></i></a>';
                        } elseif($r->status_laporan == 2){
                            return '<a href="javascript:void(0)" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Verified" data-original-title="Verified"><i class="bx bx-check-shield text-success"></i></a>';
                        } else {
                            return '<a href="javascript:void(0)" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Not submitted yet" data-original-title="Not submitted yet"><i class="bx bx-x-circle text-danger"></i></a>';
                        }
                    }
                } else {
                    return '<a href="javascript:void(0)" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Not submitted yet" data-original-title="Not submitted yet"><i class="bx bx-x-circle text-danger"></i></a>';
                }
            })
            ->rawColumns(['action','status'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.laporan-fpku.index');
    }

    public function insertLaporanFpku(Request $request)
    {
        $request->validate([
            'nama_kegiatan'                => 'required',
            'tgl_kegiatan'                 => 'required',
            'id_fakultas'                  => 'required',
            'id_prodi'                     => 'required',
            'lokasi_tempat'                => 'required',
            'pendahuluan'                  => 'required',
            'tujuan_manfaat'               => 'required',
            'peserta'                      => 'required',
            'detil_kegiatan'               => 'required',
            'hasil_kegiatan'               => 'required',
            'evaluasi_catatan_kegiatan'    => 'required',
            'penutup'                      => 'required',
            'berkas.*'                     => 'file|mimes:pdf,doc,docx|max:2048'
        ],[
            'nama_kegiatan.required'                => 'Anda belum menginput nama kegiatan', 
            'tgl_kegiatan.required'                 => 'Anda belum menginput tgl kegiatan', 
            'id_fakultas.required'                  => 'Anda belum memilih fakultas', 
            'id_prodi.required'                     => 'Anda belum memilih prodi', 
            'lokasi_tempat.required'                => 'Anda belum menginput lokasi kegiatan',
            'pendahuluan.required'                  => 'Anda belum menginput pendahuluan', 
            'tujuan_manfaat.required'               => 'Anda belum menginput tujuan manfaat', 
            'peserta.required'                      => 'Anda belum menginput peserta', 
            'detil_kegiatan.required'               => 'Anda belum menginput detil kegiatan', 
            'hasil_kegiatan.required'               => 'Anda belum menginput hasil kegiatan', 
            'evaluasi_catatan_kegiatan.required'    => 'Anda belum menginput evaluasi catatan kegiatan', 
            'penutup.required'                      => 'Anda belum menginput penutup',
            'berkas.*.mimes'                        => 'File harus berjenis (pdf atau docx)', 
            'berkas.*.max'                          => 'Ukuran berkas tidak boleh melebihi 2MB', 
        ]);

        $checkStatus = LaporanFpku::where('id_fpku',$request->id_fpku)->count();
        if($checkStatus > 0){
            return redirect()->route('index-laporan-fpku');
        } else {
            $post = LaporanFpku::updateOrCreate(['id' => $request->id],
                [
                    'id_fpku'                       => $request->id_fpku,
                    'nama_kegiatan'                 => $request->nama_kegiatan,
                    'tgl_kegiatan'                  => $request->tgl_kegiatan,
                    'id_fakultas'                   => $request->id_fakultas,
                    'id_prodi'                      => $request->id_prodi,
                    'lokasi_tempat'                 => $request->lokasi_tempat,
                    'pendahuluan'                   => $request->pendahuluan,
                    'tujuan_manfaat'                => $request->tujuan_manfaat,
                    'peserta'                       => $request->peserta,
                    'detil_kegiatan'                => $request->detil_kegiatan,
                    'hasil_kegiatan'                => $request->hasil_kegiatan,
                    'evaluasi_catatan_kegiatan'     => $request->evaluasi_catatan_kegiatan,
                    'penutup'                       => $request->penutup,
                    'status_laporan'                => 1,
                    'dibuat_oleh'                   => Auth::user()->id,
                ]);

            # Insert data into data_rencana_anggaran
            foreach($request->rows as $k => $renang){
                $dataRenang[] = [
                    'id_fpku'       => $request->id_fpku,
                    'item'          => $renang['item'],
                    'biaya_satuan'  => $renang['biaya_satuan'],
                    'quantity'      => $renang['quantity'],
                    'frequency'     => $renang['frequency'],
                    'sumber_dana'   => $renang['sumber'],
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }
            $post = DB::table('data_rencana_anggaran_fpkus')->insert($dataRenang);

            foreach($request->baris as $l => $realisasi){
                $dataRealisasi[] = [
                    'id_fpku'       => $request->id_fpku,
                    'item'          => $realisasi['r_item'],
                    'biaya_satuan'  => $realisasi['r_biaya_satuan'],
                    'quantity'      => $realisasi['r_quantity'],
                    'frequency'     => $realisasi['r_frequency'],
                    'sumber_dana'   => $realisasi['r_sumber'],
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }
            $post = DB::table('data_realisasi_anggaran_fpkus')->insert($dataRealisasi);

            # Insert data into lampiran_laporan_proposals
            if($request->berkas != ''){
                $fileNames = [];
                foreach($request->berkas as $file){
                    $fileName = md5(time().'_'.Auth::user()->user_id).$file->getClientOriginalName();
                    $file->move(public_path('uploads-lampiran/lampiran-laporan-fpku'),$fileName);
                    $fileNames[] = 'uploads-lampiran/lampiran-laporan-fpku/'.$fileName;
                }

                $insertData = [];
                for($x = 0; $x < count($request->nama_berkas);$x++){
                    $insertData[] = [
                        'id_fpku'       => $request->id_fpku,
                        'nama_berkas'   => $request->nama_berkas[$x],
                        'berkas'        => $fileNames[$x],
                        'keterangan'    => $request->keterangan[$x],
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ];
                }
                $post = DB::table('lampiran_laporan_fpkus')->insert($insertData);
            } else {
                return redirect()->route('index-laporan-fpku');
            }
            
            return response()->json($post);
        }

    }

    public function viewlampiran(Request $request)
    {
        $datas = DB::table('lampiran_laporan_fpkus')->where('id_fpku',$request->fpku_id)->select('id','nama_berkas','berkas','keterangan')->get();
        $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Berkas</th>
                            <th>Ket</th>
                            <th>Lihat</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($datas as $no => $data){
                        $html .= 
                            '<tr>
                                <td>'.++$no.'</td>
                                <td>'.$data->nama_berkas.'</td>
                                <td>'.$data->keterangan.'</td>
                                <td><button type="button" name="view" id="'.$data->id.'" class="view btn btn-outline-primary btn-sm"><a href="'.asset('/'.$data->berkas).'" target="_blank"><i class="bx bx-show"></i></a></button></td>
                            </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }

    public function previewlaporanfpku($id)
    {
        $ID = decrypt($id);
        $datas = LaporanFpku::leftJoin('data_fakultas','data_fakultas.id','=','laporan_fpkus.id_fakultas')
            ->leftJoin('pegawais','pegawais.id','=','laporan_fpkus.dibuat_oleh')
            ->leftJoin('data_prodis','data_prodis.id','=','laporan_fpkus.id_prodi')
            ->select('laporan_fpkus.id AS id','laporan_fpkus.*','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai','pegawais.user_id')
            ->where('laporan_fpkus.id_fpku',$ID)
            ->get();

        $anggarans = DB::table('data_rencana_anggaran_fpkus')->where('id_fpku',$ID)->get();
        $realisasianggarans = DB::table('data_realisasi_anggaran_fpkus')->where('id_fpku',$ID)->get();
        $grandTotalAnggarans = DB::table('data_rencana_anggaran_fpkus')->select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_fpku',$ID)->first();
        $grandTotalRealisasiAnggarans = DB::table('data_realisasi_anggaran_fpkus')->select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotalRealisasi'))->where('id_fpku',$ID)->first();
        $qrcode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate('Unverified!'));

        # Get Dekan
        foreach($datas as $r){
            $getDekan = Dekan::leftJoin('data_fakultas','data_fakultas.id','=','dekans.id_fakultas')->where('dekans.id_fakultas',$r->id_fakultas)->select('dekans.name','data_fakultas.nama_fakultas')->get();
        }
        
        
        $fileName = 'laporan_fpku_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.laporan-fpku.preview-laporan-fpku', compact('datas','anggarans','realisasianggarans','grandTotalAnggarans','grandTotalRealisasiAnggarans','qrcode','getDekan'));
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    public function qrlaporan($slug)
    {
        $initial = ''.URL::to('/').'/fpku-rep/'.$slug;
        $datas = DataFpku::leftJoin('status_fpkus','status_fpkus.id_fpku','=','data_fpkus.id')
            ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
            ->select('data_fpkus.id AS id','data_fpkus.*','laporan_fpkus.created_at AS tgl_verif')
            ->where('laporan_fpkus.qrcode',$initial)
            ->get();

        return view('general.laporan-fpku.qrcode-laporan', compact('datas'));
    }
}
