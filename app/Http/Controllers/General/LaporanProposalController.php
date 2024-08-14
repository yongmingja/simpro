<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\LaporanProposal;
use App\Models\General\DataRealisasiAnggaran;
use App\Models\General\Proposal;
use App\Setting\Dekan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;
use Auth;

class LaporanProposalController extends Controller
{
    public function indexlaporan(Request $request, $id)
    {
        $ID = decrypt($id);
        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $grandTotal = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();
        
        return view('general.laporan-proposal.index-laporan', ['anggarans' => $anggarans, 'grandTotal' => $grandTotal, 'id' => $ID]);
    }

    public function insertLaporanProposal(Request $request)
    {
        $request->validate([
            'berkas'            => 'mimes:jpeg,bmp,png,pdf|max:2000',
        ],[ 
            'berkas.mimes'      => 'Format berkas harus berformat .jpeg, .bmp, .png atau .pdf',
            'berkas.max'        => 'Ukuran file lebih dari 2MB'
        ]);

        $getId = $request->id_pro;
        $post = LaporanProposal::updateOrCreate([
            'id_proposal'                   => $getId,
            'hasil_kegiatan'                => $request->hasil_kegiatan,
            'evaluasi_catatan_kegiatan'     => $request->evaluasi_catatan_kegiatan,
            'penutup'                       => $request->penutup
        ]);

        foreach($request->rows as $k => $renang){
            $dataRenang[] = [
                'id_proposal'   => $getId,
                'item'          => $renang['item'],
                'biaya_satuan'  => $renang['biaya_satuan'],
                'quantity'      => $renang['quantity'],
                'frequency'     => $renang['frequency'],
                'sumber_dana'   => $renang['sumber'],
                'created_at'    => now(),
                'updated_at'    => now()
            ];
        }
        $post = DataRealisasiAnggaran::insert($dataRenang);

        # Insert data into lampiran_laporan_proposals
        if($request->berkas != ''){
            $fileNames = [];
            foreach($request->berkas as $file){
                $fileName = time().'.'.$file->getClientOriginalName();
                $file->move(public_path('uploads-lampiran/lampiran-laporan-proposal'),$fileName);
                $fileNames[] = 'uploads-lampiran/lampiran-laporan-proposal/'.$fileName;
            }

            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){
                $insertData[] = [
                    'id_proposal' => $getId,
                    'nama_berkas' => $request->nama_berkas[$x],
                    'berkas' => $fileNames[$x],
                    'keterangan' => $request->keterangan[$x],
                ];
            }
            $post = DB::table('lampiran_laporan_proposals')->insert($insertData);
        } else {
            return redirect()->route('my-report');
        }

        return response()->json($post);
    }

    public function laporansaya(Request $request)
    {
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','dosens.name AS nama_user','mahasiswas.name AS nama_user','laporan_proposals.created_at AS tgl_proposal')
            ->where('proposals.user_id',Auth::user()->user_id)
            ->orderBy('proposals.id','DESC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $checkStatusProposal = DB::table('status_proposals')->where([['status_approval',5],['id_proposal',$data->id]])->get();
                if($checkStatusProposal->count() > 0){
                    $query = LaporanProposal::where('id_proposal',$data->id)->select('status_laporan')->get();
                    if($query->count() > 0){
                        return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i></a>';
                    } else {
                        return '<a href="'.Route('index-laporan',encrypt(['id' => $data->id])).'" class="btn btn-outline-primary btn-sm"><i class="bx bx-plus-circle bx-tada-hover bx-xs"></i> Buat Laporan</a>';
                    }
                } else {
                    return '<span class="badge bg-label-secondary">Pending</span>';
                }
            })->addColumn('action', function($data){
                $query = LaporanProposal::where('id_proposal',$data->id)->select('status_laporan')->get();
                if($query->count() > 0){
                    foreach($query as $q){
                        if($q->status_laporan == 1){
                            return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> Sudah diverifikasi</span>';
                        } else {
                            return '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Hapus Laporan" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                        }
                    }
                } else {
                    return '<span class="badge bg-label-secondary">Belum ada laporan</span>';
                }
            })
            ->rawColumns(['laporan','action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.laporan-proposal.laporan-saya');
    }

    public function previewlaporan($id)
    {
        $ID = decrypt($id);
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','laporan_proposals.id AS id_laporan','laporan_proposals.*','laporan_proposals.penutup AS lap_penutup','laporan_proposals.created_at AS tgl_laporan')
            ->where('proposals.id',$ID)
            ->orderBy('proposals.id','DESC')
            ->get();
        
        # Get and show the QRCode
        $getQR = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('dekans','dekans.id_fakultas','=','proposals.id_fakultas')
            ->select('laporan_proposals.status_laporan','laporan_proposals.qrcode','dosens.name AS nama_dosen','mahasiswas.name AS nama_mahasiswa')
            ->where('laporan_proposals.id_proposal',$ID)
            ->get();
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate('Unverified!'));

        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $realisasianggarans = DataRealisasiAnggaran::where('id_proposal',$ID)->get();
        $grandTotalAnggarans = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();
        $grandTotalRealisasiAnggarans = DataRealisasiAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotalRealisasi'))->where('id_proposal',$ID)->first();

        # Get Dekan
        foreach($datas as $r){
            $getDekan = Dekan::where('id_fakultas',$r->id_fakultas)->select('name')->get();
        }

        $data_lampiran = DB::table('lampiran_laporan_proposals')->where('id_proposal',$ID)->get();
        
        $fileName = 'laporan_proposal_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.laporan-proposal.preview-laporan-proposal', compact('datas','getQR','qrcode','anggarans','realisasianggarans','grandTotalAnggarans','grandTotalRealisasiAnggarans','getDekan','data_lampiran'));
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    public function hapuslaporan(Request $request)
    {
        $post = LaporanProposal::where('id_proposal',$request->id)->delete();
        $post = DataRealisasiAnggaran::where('id_proposal',$request->id)->delete();
        return response()->json($post);
    }

    public function qrlaporan($slug)
    {
        $initial = 'http://simpro.test/report/'.$slug;
        $datas = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('dosens','dosens.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('dekans','dekans.id_fakultas','=','proposals.id_fakultas')
            ->select('proposals.id AS id_proposal','proposals.nama_kegiatan','proposals.tgl_event','proposals.id_fakultas','dosens.name AS nama_dosen','mahasiswas.name AS nama_mahasiswa','jenis_kegiatans.nama_jenis_kegiatan','laporan_proposals.updated_at')
            ->where('laporan_proposals.qrcode',$initial)
            ->get();

        return view('general.rektorat.show_qrcode', compact('datas'));
    }
}
