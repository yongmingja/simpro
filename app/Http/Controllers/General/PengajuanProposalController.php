<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\JenisKegiatan;
use App\Models\General\DataFakultas;
use App\Models\General\DataPengajuanSarpras;
use App\Models\General\DataRencanaAnggaran;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Setting\Dekan;
use App\Setting\Rektorat;
use Illuminate\Support\Facades\Storage;
use Redirect;
use File;
use Auth;
use DB; use URL;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanProposalController extends Controller
{
    public function index(Request $request)
    {
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai AS nama_user_dosen','mahasiswas.name AS nama_user_mahasiswa')
            ->where('proposals.user_id',Auth::user()->user_id)
            ->orderBy('proposals.id','DESC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $query = DB::table('status_proposals')->select('status_approval','id_proposal')->where('id_proposal','=',$data->id)->get();
                if($query){
                    foreach($query as $get){                
                        if($get->status_approval == 1){
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-primary"></i>Lihat Sarpras</a>
                                    <a class="dropdown-item delete" name="delete" id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-trash me-2 text-danger"></i>Hapus Proposal</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 2){
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2"></i>Lihat Sarpras</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 3) {
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-proposal" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-layer me-2 text-primary"></i>Lihat Sarpras</a>
                                    </div>
                                </div>';
                        } elseif($get->status_approval == 4) {
                            return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item lihat-anggaran" data-id="'.$get->id_proposal.'" href="javascript:void(0);"><i class="bx bx-money me-2"></i>Lihat Anggaran</a>
                                    </div>
                                </div>';
                        } else {
                            return '';
                        }
                    }
                } else {
                    return 'x';
                }
                
            })->addColumn('status', function($data){
                $btn = $this->statusProposal($data->id);                
                return $btn;
            })->addColumn('laporan', function($data){
                # check any attachment
                $q = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($q > 0){
                    return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="btn btn-outline-info btn-sm v-lampiran"><i class="bx bx-xs bx-file"></i></a>';
                } else {
                    return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>';
                }
            })
            ->rawColumns(['action','laporan','status'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $checkLap = DB::table('laporan_proposals')->rightJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
            ->leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->select('proposals.id','laporan_proposals.status_laporan','status_proposals.status_approval')
            ->where('proposals.user_id',Auth::user()->user_id)
            ->whereIn('status_proposals.status_approval',[1,3,5]) # Check status approval to activate new proposal button
            ->get();
        return view('general.pengajuan-proposal.index', compact('datas','checkLap'));
    }

    protected function statusProposal($id)
    {
        $query = DB::table('status_proposals')->select('status_approval')->where('id_proposal','=',$id)->get();
        if($query){
            foreach($query as $data){                
                if($data->status_approval == 2){
                    return '<span class="badge bg-label-danger">Ditolak Dekan</span>';
                } elseif($data->status_approval == 3) {
                    return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> ACC Dekan</span>';
                } elseif($data->status_approval == 4) {
                    return '<span class="badge bg-label-warning">Pending WR&nbsp;<div class="spinner-grow spinner-grow-sm text-warning me-1" role="status"><span class="visually-hidden"></span></div></span>';
                } elseif($data->status_approval == 5) {
                    return '<span class="badge bg-label-success"><i class="bx bx-check-double bx-xs"></i> ACC WR</span>';
                } else {
                    return '<span class="badge bg-label-secondary">Pending</span>';
                }
            }
        } else {
            return 'x';
        }
    }

    public function tampilkanWizard(Request $request)
    {
        $getJenisKegiatan = JenisKegiatan::all();
        $getFakultas = DataFakultas::select('nama_fakultas','id')->get();
        return view('general.pengajuan-proposal.wizard-proposal', compact('getJenisKegiatan','getFakultas'));
    }

    public function faculties($id)
    {
        $datas = DataFakultas::leftJoin('data_prodis','data_prodis.id_fakultas','=','data_fakultas.id')
            ->where('data_prodis.id_fakultas',$id)
            ->pluck('data_prodis.nama_prodi','data_prodis.id');
        return json_encode($datas);
    }

    public function insertProposal(Request $request)
    {

        $request->validate([
            'id_jenis_kegiatan' => 'required',
            'id_fakultas'       => 'required',
            'id_prodi'          => 'required',
            'nama_kegiatan'     => 'required',
            'pendahuluan'       => 'required',
            'tujuan_manfaat'    => 'required',
            'tgl_event'         => 'required',
            'peserta'           => 'required',
            'detil_kegiatan'    => 'required',
            'penutup'           => 'required',
            'lokasi_tempat'     => 'required',
            'berkas'            => 'max:2048'
        ],[
            'id_jenis_kegiatan.required'    => 'Anda belum memilih kategori proposal', 
            'id_fakultas.required'          => 'Anda belum memilih fakultas', 
            'id_prodi.required'             => 'Anda belum memilih prodi', 
            'nama_kegiatan.required'        => 'Anda belum menginput nama kegiatan', 
            'pendahuluan.required'          => 'Anda belum menginput pendahuluan', 
            'tujuan_manfaat.required'       => 'Anda belum menginput tujuan manfaat', 
            'tgl_event.required'            => 'Anda belum menginput tgl event', 
            'peserta.required'              => 'Anda belum menginput peserta', 
            'detil_kegiatan.required'       => 'Anda belum menginput detil kegiatan', 
            'penutup.required'              => 'Anda belum menginput penutup',
            'lokasi_tempat.required'        => 'Anda belum menginput lokasi kegiatan',
            'berkas.max'                    => 'Ukuran berkas tidak boleh melebihi 2MB', 
        ]);

        $post = Proposal::updateOrCreate(['id' => $request->id],
                [
                    'id_jenis_kegiatan'     => $request->id_jenis_kegiatan,
                    'user_id'               => Auth::user()->user_id,
                    'id_fakultas'           => $request->id_fakultas,
                    'id_prodi'              => $request->id_prodi,
                    'nama_kegiatan'         => $request->nama_kegiatan,
                    'pendahuluan'           => $request->pendahuluan,
                    'tujuan_manfaat'        => $request->tujuan_manfaat,
                    'tgl_event'             => $request->tgl_event,
                    'lokasi_tempat'         => $request->lokasi_tempat,
                    'peserta'               => $request->peserta,
                    'detil_kegiatan'        => $request->detil_kegiatan,
                    'penutup'               => $request->penutup,
                    'validasi'              => 1,
                ]);

        $latest_id = Proposal::latest()->first();

        if($latest_id == ''){
            $latest = '1';
        } else {
            $latest = $latest_id->id;
        }

        

        # Insert data into data_pengajuan_sarpras
        foreach($request->kolom as $key => $sarpras){
            $dataSet[] = [
                'id_proposal'       => $latest,
                'tgl_kegiatan'      => $sarpras['tgl_kegiatan'],
                'sarpras_item'      => $sarpras['sarpras_item'],
                'jumlah'            => $sarpras['jumlah'],
                'sumber_dana'       => $sarpras['sumber'],
                'status'            => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ];
        }                
        $post = DataPengajuanSarpras::insert($dataSet);

        # Insert data into data_rencana_anggaran
        foreach($request->rows as $k => $renang){
            $dataRenang[] = [
                'id_proposal'   => $latest,
                'item'          => $renang['item'],
                'biaya_satuan'  => $renang['biaya_satuan'],
                'quantity'      => $renang['quantity'],
                'frequency'     => $renang['frequency'],
                'sumber_dana'   => $renang['sumber'],
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now()
            ];
        }
        $post = DataRencanaAnggaran::insert($dataRenang);

        $post = DB::table('status_proposals')->insert(
            [
                'id_proposal'       => $latest,
                'status_approval'   => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

        # Insert data into lampiran_proposals
        if($request->berkas != ''){
            $fileNames = [];
            foreach($request->berkas as $file){
                $fileName = time().'_'.Auth::user()->user_id.'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads-lampiran/lampiran-proposal'),$fileName);
                $fileNames[] = 'uploads-lampiran/lampiran-proposal/'.$fileName;
            }

            $insertData = [];
            for($x = 0; $x < count($request->nama_berkas);$x++){
                $insertData[] = [
                    'id_proposal'   => $latest,
                    'nama_berkas'   => $request->nama_berkas[$x],
                    'berkas'        => $fileNames[$x],
                    'keterangan'    => $request->keterangan[$x],
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }
            $post = DB::table('lampiran_proposals')->insert($insertData);
        } else {
            return redirect()->route('submission-of-proposal.index');
        }
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Proposal::where('id',$id)->delete(); 
        DB::table('lampiran_proposals')->where('id_proposal',$id)->delete();    
        return response()->json($post);
    }

    public function checkstatus(Request $request)
    {
        $datas = DataPengajuanSarpras::where('id_proposal',$request->proposal_id)->get();
        foreach($datas as $data){
            $html = '<div class="container-fluid py-5">
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="horizontal-timeline">
                            <ul class="list-inline items">';

                            if($data->status == '1'){
                                $html .= '<li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Pengajuan Proposal</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-warning">Proses</div>
                                    <h5 class="pt-2 text-warning">Verifikasi Sarpras</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Verifikasi Dekan</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Proposal diterima</h5>
                                    </div>
                                </li>';
                            } else if($data->status == '2') {
                                $html .= '<li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Pengajuan Proposal</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Verifikasi Sarpras</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-warning">Proses</div>
                                    <h5 class="pt-2 text-warning">Verifikasi Dekan</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Proposal diterima</h5>
                                    </div>
                                </li>';
                            } else if($data->status == '3'){
                                $html .= '<li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Pengajuan Proposal</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-warning">Pending</div>
                                    <h5 class="pt-2 text-warning">Verifikasi Sarpras</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Verifikasi Dekan</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Proposal diterima</h5>
                                    </div>
                                </li>';
                            } else if($data->status == '4') {
                                $html .= '<li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Pengajuan Proposal</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Verifikasi Sarpras</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Verifikasi Dekan</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Proposal diterima</h5>
                                    </div>
                                </li>';
                            } else {
                                $html .= '<li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Pengajuan Proposal</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-success">Berhasil</div>
                                    <h5 class="pt-2 text-success">Verifikasi Sarpras</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-danger">Ditolak</div>
                                    <h5 class="pt-2 text-danger">Verifikasi Dekan</h5>
                                    </div>
                                </li>
                                <li class="list-inline-item items-list">
                                    <div class="px-4">
                                    <div class="event-date badge bg-secondary">Menunggu</div>
                                    <h5 class="pt-2 text-secondary">Proposal diterima</h5>
                                    </div>
                                </li>';
                            } 

                            $html .= '
                            </ul>

                        </div>
                        </div>
                    </div>
                </div>';
        }
        $html .= '<p>NB: <i>Untuk Verifikasi Sarpras silahkan perhatikan tabel di bawah!</i></p>';

        # Do another looping
        $html .= '<hr>';
        $html .= '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tgl Kegiatan</th>
                            <th>Sarpras Item</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Ket</th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach($datas as $no => $item){
                $html .= '<tr>
                            <td>'.++$no.'</td>
                            <td>'.tanggal_indonesia($item->tgl_kegiatan).'</td>
                            <td>'.$item->sarpras_item.'</td>
                            <td>'.$item->jumlah.'</td>';
                                if($item->status == '1'){
                                    $x = '<span class="badge bg-label-warning">Pending</span>';
                                }else if($item->status == '2'){
                                    $x = '<span class="badge bg-label-success">Disetujui</span>';
                                }elseif($item->status == '3'){
                                    $x = '<span class="badge bg-label-danger">Ditolak</span>';
                                }else{
                                    $x = '<span class="badge bg-label-success">Disetujui</span>';
                                }
                $html .=    '<td>'.$x.'</td>';
                        if($item->status == '3'){
                            $html .= '<td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-alasan="'.$item->alasan.'" data-placement="bottom" title="Detil keterangan ditolak" data-original-title="Detil keterangan ditolak" class="alasan"><i class="bx bx-show-alt bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-tgl="'.$data->tgl_kegiatan.'" data-item="'.$item->sarpras_item.'" data-jumlah="'.$item->jumlah.'" data-sumber="'.$item->sumber_dana.'" data-placement="bottom" title="Edit data sarpras" data-original-title="Edit data sarpras" class="edit-post"><i class="bx bx-edit bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-post"><i class="bx bx-trash bx-xs"></i></a></td>';
                        } else {
                            $html .= '<td></td></tr>';
                        }
                }

                $html .= '</tbody>
                    </table>';
        return response()->json(['card' => $html]);
    }

    public function updatepengajuan(Request $request){
        $post = DataPengajuanSarpras::where('id',$request->e_sarpras_id)->update([
            'tgl_kegiatan'  => $request->e_tgl_kegiatan,
            'sarpras_item'  => $request->e_sarpras_item,
            'jumlah'        => $request->e_jumlah,
            'sumber_dana'   => $request->e_sumber,
            'status'        => 1,
            'alasan'        => '',
        ]);
        return response()->json($post);
    }

    public function hapusItem(Request $request)
    {
        $post = DataPengajuanSarpras::where('id',$request->id)->delete(); 
        return response()->json($post);
    }

    public function previewproposal($id)
    {
        $ID = decrypt($id);
        $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id','=','proposals.id_prodi')
            ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi','pegawais.nama_pegawai AS nama_user_dosen','mahasiswas.name AS nama_user_mahasiswa')
            ->where('proposals.id',$ID)
            ->orderBy('proposals.id','DESC')
            ->get();

        $sarpras = DataPengajuanSarpras::where('id_proposal',$ID)->get();
        $anggarans = DataRencanaAnggaran::where('id_proposal',$ID)->get();
        $grandTotal = DataRencanaAnggaran::select(DB::raw('sum(biaya_satuan * quantity * frequency) as grandTotal'))->where('id_proposal',$ID)->first();

        # Get and show the QRCode
        $getQR = DB::table('status_proposals')
            ->leftJoin('proposals','proposals.id','=','status_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('dekans','dekans.id_fakultas','=','proposals.id_fakultas')
            ->select('status_proposals.status_approval','status_proposals.generate_qrcode','pegawais.nama_pegawai AS nama_dosen','mahasiswas.name AS nama_mahasiswa')
            ->where([['status_proposals.id_proposal',$ID],['status_proposals.status_approval',5]])
            ->get();
        $qrcode = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate('Proposal belum disetujui!'));

        # get lampiran
        $data_lampiran = DB::table('lampiran_proposals')->where('id_proposal',$ID)->get();

        # Get Dekan
        foreach($datas as $r){
            $getDekan = Dekan::where('id_fakultas',$r->id_fakultas)->select('name')->get();
        }
        
        $fileName = 'proposal_'.date(now()).'.pdf';
        $pdf = PDF::loadview('general.pengajuan-proposal.preview-proposal', compact('datas','sarpras','anggarans','grandTotal','getQR','qrcode','getDekan','data_lampiran'));
        $pdf->setPaper('F4','P');
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }

    public function showQR($slug)
    {
        $initial = ''.URL::to('/').'/in/'.$slug;
        $datas = DB::table('status_proposals')
            ->leftJoin('proposals','proposals.id','=','status_proposals.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
            ->leftJoin('mahasiswas','mahasiswas.user_id','=','proposals.user_id')
            ->leftJoin('dekans','dekans.id_fakultas','=','proposals.id_fakultas')
            ->select('proposals.id AS id_proposal','proposals.id_jenis_kegiatan','proposals.nama_kegiatan','proposals.tgl_event','proposals.id_fakultas','status_proposals.status_approval','status_proposals.generate_qrcode','pegawais.nama_pegawai AS nama_dosen','mahasiswas.name AS nama_mahasiswa','status_proposals.updated_at','jenis_kegiatans.nama_jenis_kegiatan')
            ->where('status_proposals.generate_qrcode',$initial)
            ->get();

        foreach($datas as $r){
            $getDekan = Dekan::where('id_fakultas',$r->id_fakultas)->select('name')->get();
        }
        return view('general.pengajuan-proposal.show_qrcode', compact('datas','getDekan'));
    }

    public function viewlampiran(Request $request)
    {
        $datas = DB::table('lampiran_proposals')->where('id_proposal',$request->proposal_id)->select('id','nama_berkas','berkas','keterangan')->get();
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

    public function checkanggaran(Request $request)
    {
        $checkKeteranganDiTolak = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->select('keterangan_ditolak')->get();
        $datas = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->get();
        if($checkKeteranganDiTolak->count() > 0){
            foreach($checkKeteranganDiTolak as $dataKet){
                if($dataKet->keterangan_ditolak == ''){
                    $html = '';
                } else {
                    $html = '<i class="bx bx-spa mb-1"></i> Keterangan pending:  <p style="color: #f3920b; font-size: 13px; font-style:italic;">'.$dataKet->keterangan_ditolak.'</p>
                    <hr>';
                }
            }
        } else {
            $html .= '';
        }
        $html .= '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Biaya Satuan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
        if($datas->count() > 0){
            foreach($datas as $no => $item){
                $html .= '<tr>
                        <td>'.++$no.'</td>
                        <td>'.$item->item.'</td>
                        <td>'.currency_IDR($item->biaya_satuan).'</td>
                        <td>'.$item->quantity.'</td>';
                    if($item->status == '1'){
                        $html .= '<td><a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id-proposal="'.$request->proposal_id.'" data-id="'.$item->id.'" data-item="'.$item->item.'" data-biaya-satuan="'.$item->biaya_satuan.'" data-quantity="'.$item->quantity.'" data-frequency="'.$item->frequency.'" data-sumber-dana="'.$item->sumber_dana.'" data-placement="bottom" title="Edit data ini" data-original-title="Edit data ini" class="edit-anggaran"><i class="bx bx-edit bx-xs"></i></a>&nbsp;|&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$item->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-anggaran-post"><i class="bx bx-trash bx-xs"></i></a></td>';
                    } else {
                        $html .= '<td></td></tr>';
                    }
            }
            $html .= '</tbody>
                </table>';
        } else {
            $html .= '<table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Biaya Satuan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Ket</th>
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

    public function updateAnggaranItem(Request $request){
        $post = DataRencanaAnggaran::where('id',$request->e_anggaran_id)->update([
            'item'          => $request->e_anggaran_item,
            'biaya_satuan'  => $request->e_anggaran_biaya_satuan,
            'quantity'      => $request->e_anggaran_quantity,
            'frequency'     => $request->e_anggaran_frequency,
            'sumber_dana'   => $request->e_anggaran_sumber_dana
        ]);
        DB::table('status_proposals')->where('id_proposal',$request->props_id)->update([
            'status_approval'       => 3,
            'keterangan_ditolak'    => '',
        ]);
        return response()->json($post);
    }

    public function hapusItemAnggaran(Request $request)
    {
        $post = DataRencanaAnggaran::where('id',$request->id)->delete(); 
        return response()->json($post);
    }
}
