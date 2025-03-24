<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFpku;
use App\Models\General\LaporanFpku;
use App\Models\General\DataFakultasBiro;
use App\Models\General\TahunAkademik;
use App\Setting\Dekan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Master\ValidatorProposal;
use App\Models\Master\Pegawai;
use Auth; use DB; use URL;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanFpkuExport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LaporanFpkuController extends Controller
{
    public function buatLaporan(Request $request,$id)
    {
        $ID = decrypt($id);
        $getDataFpku = DataFpku::where('id',$ID)->select('nama_kegiatan','tgl_kegiatan')->first();
        $getFakultasBiro = DataFakultasBiro::select('nama_fakultas_biro','id')->get();
        return view('general.laporan-fpku.buat-laporan',['id' => $ID, 'getDataFpku' => $getDataFpku, 'getFakultasBiro' => $getFakultasBiro]);
    }

    public function faculties($id)
    {
        $datas = DataFakultasBiro::leftJoin('data_prodi_biros','data_prodi_biros.id_fakultas_biro','=','data_fakultas_biros.id')
            ->where('data_prodi_biros.id_fakultas_biro',$id)
            ->pluck('data_prodi_biros.nama_prodi_biro','data_prodi_biros.id');
        return json_encode($datas);
    }

    public function indexFpku(Request $request)
    {
        $userID = Auth::user()->id;
        // $datas = DataFpku::whereRaw("JSON_CONTAINS(peserta_kegiatan,'\"$userID\"')")->get();
        $datas = DataFpku::leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
            ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
            ->select('data_fpkus.id AS id','laporan_fpkus.id AS id_laporan','data_fpkus.peserta_kegiatan','data_fpkus.undangan_dari','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','status_laporan_fpkus.status_approval','status_laporan_fpkus.keterangan_ditolak','data_fpkus.ketua')
            ->whereRaw("JSON_CONTAINS(data_fpkus.peserta_kegiatan,'\"$userID\"')")
            ->orderBy('status_laporan_fpkus.status_approval','ASC')
            ->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkStatus = LaporanFpku::where('id_fpku',$data->id)->get();
                if($checkStatus->count() > 0){                    
                    return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-bs-toggle="tooltip" data-id="'.$data->id.'" data-bs-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku"><i class="bx bx-book-open bx-sm text-primary"></i></a>';                    
                } else {
                    if($data->ketua == Auth::user()->id){
                        return '<a href="'.Route('buat-laporan-fpku',encrypt(['id' => $data->id])).'" class="getIdFpku" data-toggle="tooltip" data-placement="bottom" title="Buat Laporan Pertanggungjawaban" data-original-title="Buat Laporan Pertanggungjawaban"><i class="bx bx-plus-circle bx-tada-hover bx-sm text-primary"></i></a>&nbsp;<div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span>';
                    } else {
                        return '<small><i class="text-danger">belum submit</i></small>';
                    }
                }
            })->addColumn('status', function($data){
                if($data->status_approval == 1){
                    if($data->ketua == Auth::user()->id){
                        return '<a href="javascript:void(0)" name="delete" id="'.$data->id_laporan.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete text-danger"><i class="bx bx-xs bx-trash"></i></a>';
                    } else {
                        return '<small><i class="text-danger">belum submit</i></small>';
                    }
                } elseif($data->status_approval == 2){
                    $button = '<a href="javascript:void()" class="delete" id="'.$data->id_laporan.'"><i class="bx bx-refresh"></i> Recreate</a>';
                    $button .= '&nbsp;|&nbsp;';
                    $button .= '<a href="javascript:void(0)" class="text-danger info-ditolak" data-keteranganditolak="'.$data->keterangan_ditolak.'"><i class="bx bx-shield-x"></i> denied <span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';                    
                    return $button;
                }elseif($data->status_approval == 3){
                    return '<a href="javascript:void(0)" class="text-success"><i class="bx bx-check-shield"></i> verified</a>';
                } else {
                    return '<small><i class="text-danger">belum submit</i></small>';
                }
            })->addColumn('lampirans', function($data){
                $checkLampiran = DB::table('lampiran_laporan_fpkus')->where('id_laporan_fpku',$data->id)->get();
                if($checkLampiran->count() > 0){                   
                    return '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-id="'.$data->id.'" data-bs-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="v-lampiran" style="font-size: 10px;">lihat lampiran</a>';
                } else {
                    return '<i class="bx bx-minus-circle text-secondary"></i>';
                }
            })
            ->rawColumns(['action','status','lampirans'])
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
                    'id_fakultas_biro'              => $request->id_fakultas_biro,
                    'id_prodi_biro'                 => $request->id_prodi_biro,
                    'lokasi_tempat'                 => $request->lokasi_tempat,
                    'pendahuluan'                   => $request->pendahuluan,
                    'tujuan_manfaat'                => $request->tujuan_manfaat,
                    'peserta'                       => $request->peserta,
                    'detil_kegiatan'                => $request->detil_kegiatan,
                    'hasil_kegiatan'                => $request->hasil_kegiatan,
                    'evaluasi_catatan_kegiatan'     => $request->evaluasi_catatan_kegiatan,
                    'penutup'                       => $request->penutup,
                    'dibuat_oleh'                   => Auth::user()->id,
                ]);

        $latest_id = LaporanFpku::latest()->first();

        if($latest_id == ''){
            $latest = '1';
        } else {
            $latest = $latest_id->id;
        } 

            # Insert data into data_rencana_anggaran
            $dataRenang = [];
            foreach($request->rows as $k => $renang){
                if(!empty($renang['item']) && !empty($renang['biaya_satuan']) && !empty($renang['quantity']) && !empty($renang['frequency']) && !empty($renang['sumber'])) {
                    $dataRenang[] = [
                        'id_laporan_fpku'  => $latest,
                        'item'             => $renang['item'],
                        'biaya_satuan'     => $renang['biaya_satuan'],
                        'quantity'         => $renang['quantity'],
                        'frequency'        => $renang['frequency'],
                        'sumber_dana'      => $renang['sumber'],
                        'created_at'       => now(),
                        'updated_at'       => now()
                    ];
                }
            }
            if (!empty($dataRenang)) {
                $post = DB::table('data_rencana_anggaran_fpkus')->insert($dataRenang);
            }

            $dataRealisasi = [];
            foreach($request->baris as $l => $realisasi){
                if(!empty($realisasi['r_item']) && !empty($realisasi['r_biaya_satuan']) && !empty($realisasi['r_quantity']) && !empty($realisasi['r_frequency']) && !empty($realisasi['r_sumber'])) {
                    $dataRealisasi[] = [
                        'id_laporan_fpku'   => $latest,
                        'item'              => $realisasi['r_item'],
                        'biaya_satuan'      => $realisasi['r_biaya_satuan'],
                        'quantity'          => $realisasi['r_quantity'],
                        'frequency'         => $realisasi['r_frequency'],
                        'sumber_dana'       => $realisasi['r_sumber'],
                        'created_at'        => now(),
                        'updated_at'        => now()
                    ];
                }
            }
            if (!empty($dataRealisasi)){
                $post = DB::table('data_realisasi_anggaran_fpkus')->insert($dataRealisasi);
            }
            DB::table('status_laporan_fpkus')->insert(
                [
                    'id_laporan_fpku'     => $latest,
                    'status_approval'     => 1,
                    'created_at'          => now(),
                    'updated_at'          => now()
                ]);

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
                    if(!empty($request->nama_berkas[$x]) && !empty($fileNames[$x]) && !empty($request->keterangan[$x])){
                        $insertData[] = [
                            'id_laporan_fpku' => $latest,
                            'nama_berkas'     => $request->nama_berkas[$x],
                            'berkas'          => $fileNames[$x],
                            'keterangan'      => $request->keterangan[$x],
                            'created_at'      => now(),
                            'updated_at'      => now()
                        ];
                    }
                }
                if (!empty($insertData)){
                    $post = DB::table('lampiran_laporan_fpkus')->insert($insertData);
                }
            } else {
                $insertData = [];
                for($x = 0; $x < count($request->nama_berkas);$x++){
                    if(!empty($request->nama_berkas[$x]) && !empty($request->link_gdrive[$x]) && !empty($request->keterangan[$x])){
                        $insertData[] = [
                            'id_laporan_fpku' => $latest,
                            'nama_berkas'     => $request->nama_berkas[$x],
                            'berkas'          => '',
                            'link_gdrive'     => $request->link_gdrive[$x],
                            'keterangan'      => $request->keterangan[$x],
                            'created_at'      => now(),
                            'updated_at'      => now()
                        ];
                    }
                }
                if (!empty($insertData)){
                    $post = DB::table('lampiran_laporan_fpkus')->insert($insertData);
                }
                return redirect()->route('index-laporan-fpku');
            }

            
            
            return response()->json($post);
        }

    }

    public function viewlampiran(Request $request)
    {
        $datas = DB::table('lampiran_laporan_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','lampiran_laporan_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$request->fpku_id)->select('lampiran_laporan_fpkus.id','lampiran_laporan_fpkus.nama_berkas','lampiran_laporan_fpkus.berkas','lampiran_laporan_fpkus.keterangan','lampiran_laporan_fpkus.link_gdrive')->get();
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
                    if($datas->count() > 0){
                        foreach($datas as $no => $data){
                        $html .= '<tr>
                                    <td>'.++$no.'</td>
                                    <td>'.$data->nama_berkas.'</td>
                                    <td>'.$data->keterangan.'</td>
                                    <td>';
                                    if($data->berkas != ''){
                                        $html .= '<button type="button" name="view" id="'.$data->id.'" class="view btn btn-outline-primary btn-sm"><a href="'.asset('/'.$data->berkas).'" target="_blank"><i class="bx bx-show"></i></a></button>';
                                    } else {
                                        $html .= '<a href="'.$data->link_gdrive.'" target="_blank">'.$data->link_gdrive.'</a>';
                                    }
                                    
                                $html .= '</td>
                            </tr>';
                        }
                    } else {
                        $html .= '<tr>
                            <td colspan="3"> No data available in table </td>
                        </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }

    public function previewlaporanfpku($id)
    {
        $ID = decrypt($id);
        $datas = LaporanFpku::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','laporan_fpkus.id_fakultas_biro')
            ->leftJoin('pegawais','pegawais.id','=','laporan_fpkus.dibuat_oleh')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','laporan_fpkus.id_prodi_biro')
            ->select('laporan_fpkus.id AS id','laporan_fpkus.*','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','pegawais.user_id')
            ->where('laporan_fpkus.id_fpku',$ID)
            ->get();

        $anggarans = DB::table('data_rencana_anggaran_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','data_rencana_anggaran_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$ID)->get();

        $realisasianggarans = DB::table('data_realisasi_anggaran_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','data_realisasi_anggaran_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$ID)->get();

        $grandTotalAnggarans = DB::table('data_rencana_anggaran_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','data_rencana_anggaran_fpkus.id_laporan_fpku')->select(DB::raw('sum(data_rencana_anggaran_fpkus.biaya_satuan * data_rencana_anggaran_fpkus.quantity * data_rencana_anggaran_fpkus.frequency) as grandTotal'))->where('laporan_fpkus.id_fpku',$ID)->first();

        $grandTotalRealisasiAnggarans = DB::table('data_realisasi_anggaran_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','data_realisasi_anggaran_fpkus.id_laporan_fpku')->select(DB::raw('sum(data_realisasi_anggaran_fpkus.biaya_satuan * data_realisasi_anggaran_fpkus.quantity * data_realisasi_anggaran_fpkus.frequency) as grandTotalRealisasi'))->where('laporan_fpkus.id_fpku',$ID)->first();

        # Get and show the QRCode
        $getQR = DB::table('status_laporan_fpkus')
            ->leftJoin('laporan_fpkus','laporan_fpkus.id','=','status_laporan_fpkus.id_laporan_fpku')
            ->leftJoin('pegawais','pegawais.user_id','=','laporan_fpkus.dibuat_oleh')
            ->select('status_laporan_fpkus.id AS id_status','status_laporan_fpkus.status_approval','pegawais.nama_pegawai','status_laporan_fpkus.generate_qrcode')
            ->where([['laporan_fpkus.id_fpku',$ID],['status_laporan_fpkus.status_approval',3]])
            ->get();

        $qrcode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate('Unverified!'));

        # Get Pengusul according to proposal.user_id
        $getPengusul = DB::table('laporan_fpkus')->leftJoin('pegawais','pegawais.id','=','laporan_fpkus.dibuat_oleh')
            ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_pegawai','=','pegawais.id')
            ->where('laporan_fpkus.id_fpku',$ID)
            ->select('pegawais.nama_pegawai','jabatan_pegawais.ket_jabatan')
            ->first();
        
        
        $fileName = 'laporan_fpku_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.laporan-fpku.preview-laporan-fpku', compact('datas','anggarans','realisasianggarans','grandTotalAnggarans','grandTotalRealisasiAnggarans','getQR','qrcode','getPengusul'));
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    public function qrlaporan($slug)
    {
        $initial = ''.URL::to('/').'/fpku-rep/'.$slug;
        $datas = DataFpku::leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
            ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
            ->select('data_fpkus.id AS id','data_fpkus.*','laporan_fpkus.created_at AS tgl_verif')
            ->where('status_laporan_fpkus.generate_qrcode',$initial)
            ->get();

        return view('general.laporan-fpku.qrcode-laporan', compact('datas'));
    }

    public function hapusLaporanFpku(Request $request)
    {
        $post = LaporanFpku::where('id',$request->id)->delete();  
        return response()->json($post);
    }

    public function indexExportFpku(Request $request)
    {
        $checkYear = TahunAkademik::select('year','id')->get();

        if($request->tahun_fpku == null || $request->tahun_fpku == '[semua]'){
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','data_fpkus.id_tahun_akademik')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','laporan_fpkus.id')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai','status_laporan_fpkus.status_approval','tahun_akademiks.year')
                ->orderBy('data_fpkus.tgl_kegiatan','DESC')
                ->get();
        } else {
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','data_fpkus.id_tahun_akademik')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','laporan_fpkus.id')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai','status_laporan_fpkus.status_approval','tahun_akademiks.year')
                ->where('data_fpkus.id_tahun_akademik',$request->tahun_fpku)
                ->orderBy('data_fpkus.tgl_kegiatan','DESC')
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('status', function($data){
                if(!empty($data->status_approval)) {
                    if($data->status_approval == 3){
                        return '<i class="text-success">ACC Rektorat</i>';
                    } else {
                        return '<i class="text-secondary">Belum ada laporan</i>';
                    }
                } else {
                    return '<i class="text-secondary">Belum ada laporan</i>';
                }
            })->addColumn('anggota_pelaksana', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })
            ->rawColumns(['status','anggota_pelaksana'])
            ->addIndexColumn(true)
            ->make(true);
        }

        return view('general.laporan-fpku.export-data', compact('checkYear'));
    }

    public function showDataFpkuHtml($year)
    {
        if($year == null || $year == '[semua]'){
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('lampiran_fpkus','lampiran_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','data_fpkus.id_tahun_akademik')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai as ketua','lampiran_fpkus.link_gdrive','status_laporan_fpkus.status_approval','laporan_fpkus.id AS id_laporan','tahun_akademiks.year')
                ->orderBy('data_fpkus.tgl_kegiatan','DESC')
                ->get();
        } else {
            $datas = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
                ->leftJoin('lampiran_fpkus','lampiran_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('laporan_fpkus','laporan_fpkus.id_fpku','=','data_fpkus.id')
                ->leftJoin('status_laporan_fpkus','status_laporan_fpkus.id_laporan_fpku','=','laporan_fpkus.id')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','data_fpkus.id_tahun_akademik')
                ->select('data_fpkus.id AS id','data_fpkus.no_surat_undangan','data_fpkus.nama_kegiatan','data_fpkus.tgl_kegiatan','data_fpkus.peserta_kegiatan','pegawais.nama_pegawai as ketua','lampiran_fpkus.link_gdrive','status_laporan_fpkus.status_approval','laporan_fpkus.id AS id_laporan','tahun_akademiks.year')
                ->where('data_fpkus.id_tahun_akademik',$year)
                ->orderBy('data_fpkus.tgl_kegiatan','DESC')
                ->get();
        }
        $getYear = TahunAkademik::where('id',$year)->select('year')->first();
        if($getYear){
            $getYear = $getYear->year;
        } else {
            $getYear = '[Semua]';
        }
        return view('general.laporan-fpku.show-in-html', ['datas' => $datas, 'getYear' => $getYear]);
    }

    public function downloadFpkuExcel($year)
    {
        $getYear = $year;
        $fileName = 'data_dan_laporan_fpku_'.$getYear.'.xlsx';

        // Simpan file Excel ke penyimpanan sementara
        Excel::store(new LaporanFpkuExport($getYear), $fileName, 'local');

        return Response::stream(function() use ($fileName) {
            // Buka file dari penyimpanan sementara dan kirim sebagai stream
            $file = Storage::disk('local')->get($fileName);
            echo $file;
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
