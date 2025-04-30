<?php

namespace App\Http\Controllers\RektoratPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use App\Models\Master\HandleProposal;
use App\Models\Master\MasterFormRkat;
use App\Models\General\LaporanFpku;
use App\Models\General\DelegasiFpku;
use App\Models\General\DelegasiProposal;
use App\Models\General\TahunAkademik;
use App\Models\General\DataPengajuanSarpras;
use Illuminate\Support\Facades\Mail;
use App\Mail\UndanganFpku;
use App\Mail\EmailDelegasiFpku;
use App\Mail\EmailDelegasiProposal;
use App\Mail\EmailDiterimaRektorat;
use App\Mail\EmailDitolakRektorat;
use Auth;
use DB;
use URL;

class DashboardController extends Controller
{
    # Method untuk Proposal dan Laporan Proposal
    public function index(Request $request)
    {
        $query = Proposal::leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
            ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros', 'data_prodi_biros.id', '=', 'proposals.id_prodi_biro')
            ->leftJoin('status_proposals', 'status_proposals.id_proposal', '=', 'proposals.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
            ->select(
                'proposals.id AS id',
                'proposals.*',
                'jenis_kegiatans.nama_jenis_kegiatan',
                'data_fakultas_biros.nama_fakultas_biro',
                'data_prodi_biros.nama_prodi_biro',
                'pegawais.nama_pegawai AS nama_user'
            )
            ->where([
                ['tahun_akademiks.is_active', 1]
            ])
            ->whereIn('proposals.id_jenis_kegiatan', $this->arrJenisKegiatan());

        // Mapping status untuk kondisi dinamis
        $statusMapping = [
            '' => null,
            'all' => null,
            'batal' => ['proposals.is_archived', '=', 1],
            'pending' => ['status_proposals.status_approval', '<=', 3],
            'accepted' => ['status_proposals.status_approval', '=', 5],
            'denied' => ['status_proposals.status_approval', '=', [2,4]],
        ];

        // Tambahkan kondisi berdasarkan status jika diperlukan
        if (array_key_exists($request->status, $statusMapping) && $statusMapping[$request->status] !== null) {
            $query->where($statusMapping[$request->status][0], $statusMapping[$request->status][1], $statusMapping[$request->status][2] ?? null);
        }

        // Eksekusi query
        $datas = $query->orderBy('proposals.tgl_event', 'DESC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal">'.$data->nama_kegiatan.'</a>';               
                return $button;
            })->addColumn('validasi', function($data){
                // Query utama untuk mengambil status_proposals
                $checkState = DB::table('status_proposals')
                    ->where('id_proposal', $data->id)
                    ->select('status_approval')
                    ->get();

                if ($checkState->isEmpty()) {
                    return ''; // Jika tidak ada data
                }

                $statusLabels = [
                    2 => ['text' => '<small><i class="text-danger">Ditolak Atasan</i></small>', 'tooltip' => 'Status terakhir: ditolak atasan'],
                    3 => ['text' => '<small><i class="text-warning">Menunggu validasi rektorat</i></small>', 'tooltip' => 'Status terakhir: menunggu validasi rektorat'],
                    4 => ['text' => '<small><i class="text-danger">Ditolak Rektorat</i></small>', 'tooltip' => 'Status terakhir: ditolak rektorat'],
                    5 => ['text' => '<small><i class="text-success">ACC Rektorat</i></small>', 'tooltip' => 'Status terakhir: diterima rektorat'],
                ];
                
                foreach ($checkState as $state) {
                    if ($data->is_archived != 1) { // Proposal tidak diarsipkan
                        if ($state->status_approval == 3) {
                            return '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="' . $data->id . '" title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>'
                                . '&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>';
                        }
                        return $statusLabels[$state->status_approval]['text'] ?? '<small><i class="text-warning">Menunggu validasi atasan</i></small>';
                    } else { // Proposal diarsipkan
                        $statusText = $statusLabels[$state->status_approval]['text'] ?? '<small><i class="text-warning">Menunggu validasi atasan</i></small>';
                        $tooltipText = $statusLabels[$state->status_approval]['tooltip'] ?? 'Status terakhir: menunggu validasi atasan';
                        return '<small class="text-warning">Dibatalkan oleh user</small>&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" title="' . $tooltipText . '"><span class="badge bg-danger badge-notifications">?</span></a>';
                    }
                }                

            })->addColumn('vlampiran', function($data){
                # check any attachment
                $q = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($q > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="v-lampiran"><small><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                }else{
                    return '<small><i class="bx bx-minus-circle bx-xs"></i></small>';
                }
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><small><i class="bx bx-detail bx-xs bx-tada-hover"></i> Detail</small></a>';
            })->addColumn('lihatDelegasi', function($data){
                $isExist = DelegasiProposal::where('id_proposal',$data->id)->select('catatan_delegator','delegasi')->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" class="lihat-delegasi" data-id="'.$data->id.'"><small><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    $checkState = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->first();
                    if($checkState != null){
                        if($checkState->status_approval == 5){
                            return '<a href="javascript:void(0)" class="tambah-delegasi text-warning" data-id="'.$data->id.'"><small><i class="bx bx-plus-circle bx-xs"></i> Input</small></a>';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                }
            })->addColumn('detail_sarpras', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="status-mon-sarpras"><small class="text-info"><i class="bx bx-detail bx-tada-hover bx-xs"></i> Detail</small></a></a>';
            })
            ->rawColumns(['action','validasi','vlampiran','detail','lihatDelegasi','detail_sarpras'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataPegawai = Pegawai::select('id','nama_pegawai')->get();
        return view('dashboard.rektorat-dashboard', compact('getDataPegawai'));
    }

    protected function arrJenisKegiatan()
    {
        $datas = HandleProposal::select('id_jenis_kegiatan')->where('id_pegawai',Auth::user()->id)->get();
        if($datas){
            foreach($datas as $data){
                $getID = $data->id_jenis_kegiatan;
            }
        }else{
            $getID = '';
        }
        return $getID;
    }

    # Method validasi proposal diterima oleh Rektorat
    public function approvalY(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update([
            'status_approval' => 5,
            'keterangan_ditolak' => '',
            'generate_qrcode' => ''.URL::to('/').'/in/'.time().'.png'
        ]);

        # Get the specific email address according to proposal id
        $getEmail = Pegawai::leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
            ->select('pegawais.email')
            ->where('proposals.id',$request->proposal_id)
            ->first();
        
        if ($getEmail && filter_var($getEmail->email, FILTER_VALIDATE_EMAIL)) {
            $emailAddress = strtolower($getEmail->email);
            $content = [
                'name' => 'Diterima!',
                'body' => 'Proposal telah di ACC oleh Rektorat. Anda bisa melihat status pada halaman proposal di SIMPRO',
            ];
            $post = Mail::to([$emailAddress,'bennyalfian@uvers.ac.id'])->send(new EmailDiterimaRektorat($content));        
        } 

        return response()->json($post);
    }

    public function approvalN(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->propsl_id)->update([
            'status_approval' => 4,
            'keterangan_ditolak' => $request->keterangan_ditolak
        ]);

        // Query untuk mendapatkan email pegawai berdasarkan proposal ID
        $getEmail = Pegawai::leftJoin('proposals', 'proposals.user_id', '=', 'pegawais.user_id')
            ->select('pegawais.email')
            ->where('proposals.id', $request->propsl_id)
            ->first();

        // Validasi jika $getEmail tidak null dan email valid
        if ($getEmail && filter_var($getEmail->email, FILTER_VALIDATE_EMAIL)) {
            $emailAddress = strtolower($getEmail->email);
            $content = [
                'name' => 'Ditolak!',
                'body' => 'Proposal ditolak oleh Rektorat. Anda bisa melihat status pada halaman proposal di SIMPRO',
            ];
            $post = Mail::to([$emailAddress, 'bennyalfian@uvers.ac.id'])->send(new EmailDitolakRektorat($content));
        }

        return response()->json($post);
    }

    public function tambahDelegasiProposal(Request $request)
    {
        $post = DelegasiProposal::updateOrCreate([
            'id_proposal'       => $request->proposal_id,
            'catatan_delegator' => $request->catatan_delegator,
            'delegasi'          => $request->input('delegasis')
        ]);

        # From delegator modal
        # Get delegation's email
        $getDelEmails = Pegawai::whereIn('id',$request->input('delegasis'))->select('email')->get();
        foreach($getDelEmails as $delmail){
            if (filter_var($delmail->email, FILTER_VALIDATE_EMAIL)){
                $delemails[] = strtolower($delmail->email);
            }
        }
        if (isset($delemails) && count($delemails) > 0){
            $content = [
                'name' => 'Delegasi dari WRSDP / WRAK*',
                'body' => $request->catatan_delegator,
                'link' => ''.URL::to('preview-proposal').'/'.encrypt($request->proposal_id).'',
            ];
            Mail::to($delemails)->send(new EmailDelegasiProposal($content));        
        } 

        return response()->json($post);
    }

    public function indexlaporan(Request $request)
    {
        $query = Proposal::leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
            ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros', 'data_prodi_biros.id', '=', 'proposals.id_prodi_biro')
            ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
            ->select(
                'proposals.id AS id',
                'proposals.*',
                'jenis_kegiatans.nama_jenis_kegiatan',
                'data_fakultas_biros.nama_fakultas_biro',
                'data_prodi_biros.nama_prodi_biro',
                'pegawais.nama_pegawai',
                'status_laporan_proposals.keterangan_ditolak',
                'status_laporan_proposals.created_at AS tgl_proposal'
            )
            ->where('tahun_akademiks.is_active', 1)
            ->whereIn('proposals.id_jenis_kegiatan', $this->arrJenisKegiatan());

        $statusMapping = [
            '' => null,
            'all' => null,
            'pending' => ['status_laporan_proposals.status_approval', '<=', 3],
            'accepted' => ['status_laporan_proposals.status_approval', '=', 5],
            'denied' => ['status_laporan_proposals.status_approval', '=', 4],
            'emp' => ['status_laporan_proposals.status_approval', '=', null],
        ];

        // Tambahkan kondisi berdasarkan status jika diperlukan
        if (isset($statusMapping[$request->status]) && $statusMapping[$request->status] !== null) {
            $query->where(
                $statusMapping[$request->status][0],
                $statusMapping[$request->status][1],
                $statusMapping[$request->status][2] ?? null
            );
        }

        // Eksekusi query
        $datas = $query->orderBy('status_laporan_proposals.status_approval', 'ASC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal"><small class="text-info"><i class="bx bx-file bx-xs"></i> Lihat laporan</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada laporan</small>';
                }
            })->addColumn('action', function($data){
                $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    foreach($query as $q){
                        if($q->status_approval == 5){
                            return '<small><i class="text-success">ACC Rektorat</i></small>';
                        } elseif($q->status_approval == 4){
                            return '<a href="javascript:void(0)" class="info-ditolak" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak Rektorat</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                        } elseif($q->status_approval == 3) {
                            return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="ACC Selesai" data-placement="bottom" data-original-title="ACC Selesai" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                        } elseif($q->status_approval == 2){
                            return '<a href="javascript:void(0)" class="info-ditolak" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak Atasan</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                        } else {
                            return '<small><i class="text-warning">Menunggu validasi atasan</i></small>';
                        }
                    }
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada laporan</small>';
                }
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><small><i class="bx bx-detail bx-xs bx-tada-hover"></i> Detail</small></a>';
            })
            ->rawColumns(['laporan','action','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-proposal.index-laporan');
    }

    # Method validasi laporan proposal diterima (telah selesai) oleh Rektorat
    public function selesailaporan(Request $request)
    {
        // Update status laporan proposal
        $post = DB::table('status_laporan_proposals')
            ->where('id_laporan_proposal', $request->proposal_id)
            ->update([
                'status_approval' => 5,
                'generate_qrcode' => URL::to('/').'/report/'.time().'.png',
            ]);

        // Ambil email pegawai berdasarkan proposal_id
        $getEmail = Pegawai::join('proposals', 'proposals.user_id', '=', 'pegawais.user_id')
            ->where('proposals.id', $request->proposal_id)
            ->value('pegawais.email'); // Langsung ambil nilai email tanpa objek

        // Validasi email dan kirim notifikasi
        if ($getEmail && filter_var($getEmail, FILTER_VALIDATE_EMAIL)) {
            $emailAddress = strtolower($getEmail);
            $content = [
                'name' => 'Diterima!',
                'body' => 'Laporan Proposal telah di ACC oleh Rektorat. Anda bisa melihat status pada halaman proposal di SIMPRO',
            ];
            Mail::to([$emailAddress, 'bennyalfian@uvers.ac.id'])->send(new EmailDiterimaRektorat($content));
        }

        // Kirim respon sebagai JSON
        return response()->json([
            'success' => (bool)$post,
            'message' => $post ? 'Status updated successfully!' : 'Failed to update status!',
        ]);
    }

    public function approvalRektorN(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->propsl_id)->update([
            'status_approval'       => 4,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);

        // Ambil email pegawai berdasarkan proposal ID
        $getEmail = Pegawai::leftJoin('proposals', 'proposals.user_id', '=', 'pegawais.user_id')
            ->select('pegawais.email')
            ->where('proposals.id', $request->propsl_id)
            ->first();

        // Pastikan $getEmail tidak null dan email valid
        if ($getEmail && filter_var($getEmail->email, FILTER_VALIDATE_EMAIL)) {
            $emailAddress = strtolower($getEmail->email);
            $content = [
                'name' => 'Ditolak!',
                'body' => 'Laporan Proposal ditolak oleh Rektorat. Anda bisa melihat status pada halaman proposal di SIMPRO',
            ];
            Mail::to($emailAddress)->send(new EmailDitolakRektorat($content));
        }

        return response()->json($post);
    }

    public function lihatHistoryDelegasiProposal(Request $request)
    {
        $datas = DelegasiProposal::where('id_proposal',$request->proposal_id)->get();
        if($datas->count() > 0){
            $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Catatan Delegator</th>
                            <th>Delegasi</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($datas as $item){
                        $html .= '<tr>
                            <td><li></li></td>
                            <td>'.$item->catatan_delegator.'</td>';
                            $getPegawai = Pegawai::whereIn('id',$item->delegasi)->select('nama_pegawai')->get();
                            foreach($getPegawai as $result){
                                $pegawai[] = $result->nama_pegawai;
                                
                            }
                        $html .= '<td>'.implode(", <br>", $pegawai).'</td>
                        </tr>';
                    }   
                    $html .= '</tbody>
                    </table>';                 
        } else {
            $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Catatan Delegator</th>
                            <th>Delegasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">No data available in table</td>
                        </tr>
                    </tbody>
                </table>';
        }

        return response()->json(['card' => $html]);

    }

    public function indexMonitoringProposal(Request $request)
    {
        $tahun_akademik = $request->tahun_akademik;
        $lembaga = $request->lembaga;
        $status = $request->status;

        $query = Proposal::leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros', 'data_prodi_biros.id', '=', 'proposals.id_prodi_biro')
            ->leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'proposals.id_jenis_kegiatan')
            ->leftJoin('status_proposals', 'status_proposals.id_proposal', '=', 'proposals.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
            ->leftJoin('form_rkats','form_rkats.id','=','proposals.id_form_rkat')
            ->select('proposals.id AS id', 'proposals.*', 'data_fakultas_biros.nama_fakultas_biro', 'data_prodi_biros.nama_prodi_biro', 'jenis_kegiatans.nama_jenis_kegiatan', 'status_proposals.status_approval','form_rkats.kode_renstra','form_rkats.kode_pagu');

        // Filter berdasarkan tahun akademik
        if ($tahun_akademik && $tahun_akademik != 'all') {
            $query->where('tahun_akademiks.id', $tahun_akademik);
        }

        // Filter berdasarkan lembaga
        if ($lembaga && $lembaga != 'all') {
            if ($lembaga == 'others') {
                $query->whereNull('proposals.id_fakultas_biro');
            } else {
                $query->where('proposals.id_fakultas_biro', $lembaga);
            }
        }

        // Filter berdasarkan status
        if ($status && $status != 'all') {
            if ($status == 'batal') {
                $query->where('proposals.is_archived', 1);
            } elseif ($status == 'ditolakatasan') {
                $query->where('status_proposals.status_approval', 2);
            } elseif ($status == 'diterimaatasan') {
                $query->where('status_proposals.status_approval', 3);
            } elseif ($status == 'ditolakrektorat') {
                $query->where('status_proposals.status_approval', 4);
            } elseif ($status == 'diterimarektorat') {
                $query->where('status_proposals.status_approval', 5);
            }
        }


        $datas = $query->orderBy('proposals.tgl_event', 'DESC')->get();


        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('preview', function($data){
                # check any attachment
                $query = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($query > 0){
                    $button = '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal"><small class="text-info"><i class="bx bx-food-menu bx-xs"></i></small></a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="v-lampiran"><small class="text-success"><i class="bx bx-xs bx-file"></i></small></a>';
                    return $button;
                } else {
                    return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal"><small class="text-info"><i class="bx bx-food-menu bx-xs"></i></small></a>';
                }
            })->addColumn('status', function($data){
                
                $statusLabels = [
                    1 => '<small><i class="text-warning">Menunggu validasi atasan</i></small>',
                    2 => '<small><i class="text-danger">Ditolak Atasan</i></small>',
                    3 => '<small><i class="text-warning">Menunggu validasi rektorat</i></small>',
                    4 => '<small><i class="text-danger">Ditolak Rektorat</i></small>',
                    5 => '<small><i class="text-success">ACC Rektorat</i></small>',
                ];
                
                if ($data->is_archived == 1) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Status terakhir: ' 
                        . strip_tags($statusLabels[$data->status_approval] ?? 'Menunggu validasi atasan') . '">
                        <small class="text-warning">Dibatalkan oleh user</small>&nbsp;&nbsp;
                        <span class="badge bg-danger badge-notifications">?</span></a>';
                } 
                
                return $statusLabels[$data->status_approval] ?? '';
                   
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><small><i class="bx bx-detail bx-tada-hover bx-xs"></i> Detail</small></a>';
            })->addColumn('history', function($data){
                $isExist = DelegasiProposal::where('id_proposal',$data->id)->select('catatan_delegator','delegasi')->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" class="lihat-delegasi" data-id="'.$data->id.'"><small class="text-info"><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Tidak ada</small>';
                }
            })->addColumn('detail_sarpras', function($data){
                return '<a href="javascript:void()" class="status-mon-sarpras text-info" data-id="'.$data->id.'"><small><i class="bx bx-detail bx-tada-hover bx-xs"></i> Detail</small></a>';
            })
            ->rawColumns(['preview','status','detail','history','detail_sarpras'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getLembaga = Proposal::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')->distinct()->get(['data_fakultas_biros.nama_fakultas_biro','data_fakultas_biros.id']);
        $getYear = TahunAkademik::select('year','id')->get();
        return view('rektorat-page.data-proposal.index-monitoring-proposals', compact('getLembaga','getYear'));
    }

    public function indexMonitoringLaporanProposal(Request $request)
    {
        $tahun_akademik = $request->tahun_akademik;
        $lembaga = $request->lembaga;

        $query = Proposal::leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'proposals.id_jenis_kegiatan')
            ->leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
            ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros', 'data_prodi_biros.id', '=', 'proposals.id_prodi_biro')
            ->leftJoin('status_laporan_proposals', 'status_laporan_proposals.id_laporan_proposal', '=', 'proposals.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
            ->leftJoin('form_rkats','form_rkats.id','=','proposals.id_form_rkat')
            ->select('proposals.id AS id', 'proposals.*', 'jenis_kegiatans.nama_jenis_kegiatan', 'data_fakultas_biros.nama_fakultas_biro', 'data_prodi_biros.nama_prodi_biro', 'pegawais.nama_pegawai','status_laporan_proposals.status_approval AS status_approval_laporan', 'status_laporan_proposals.keterangan_ditolak', 'status_laporan_proposals.created_at AS tgl_proposal','form_rkats.kode_renstra','form_rkats.kode_pagu');

        // Filter Tahun Akademik
        if ($tahun_akademik && $tahun_akademik != 'all') {
            $query->where('tahun_akademiks.is_active', $tahun_akademik);
        }

        // Filter Lembaga
        if ($lembaga && $lembaga != 'all') {
            if ($lembaga == 'others') {
                $query->whereNull('proposals.id_fakultas_biro');
            } elseif ($lembaga == 'emp') {
                $query->whereNull('status_laporan_proposals.status_approval');
            } else {
                $query->where('proposals.id_fakultas_biro', $lembaga);
            }
        }

        $datas = $query->orderBy('status_laporan_proposals.status_approval', 'ASC')->get();

        
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                if ($data->is_archived != 1) {
                    $query = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$data->id)->select('status_approval')->get();
                    if($query->count() > 0){
                        return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal"><small class="text-info"><i class="bx bx-search bx-xs"></i> Lihat</small></a>';
                    } else {
                        return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada laporan</small>';
                    }
                } else {
                    return '<small class="text-warning"><i class="bx bx-minus-circle bx-xs"></i> Proposal dibatalkan</small>';
                }
            })->addColumn('action', function($data){

                $statusLabels = [
                    1 => '<small><i class="text-warning">Menunggu validasi atasan</i></small>',
                    2 => '<small><i class="text-danger">Ditolak Atasan</i></small>',
                    3 => '<small><i class="text-warning">Menunggu validasi rektorat</i></small>',
                    4 => '<small><i class="text-danger">Ditolak Rektorat</i></small>',
                    5 => '<small><i class="text-success">ACC Rektorat</i></small>',
                ];
                
                if ($data->is_archived == 1) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Status terakhir: ' 
                        . strip_tags($statusLabels[$data->status_approval_laporan] ?? 'Menunggu validasi atasan') . '">
                        <small class="text-warning">Dibatalkan oleh user</small>&nbsp;&nbsp;
                        <span class="badge bg-danger badge-notifications">?</span></a>';
                } 
                
                return $statusLabels[$data->status_approval_laporan] ?? '<small class="text-secondary"><i class="bx bx-minus-circle bx-xs"></i> Belum ada</small>';

            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><small><i class="bx bx-detail bx-tada-hover bx-xs"></i> Detail</small></a>';
            })
            ->rawColumns(['laporan','action','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getLembaga = Proposal::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')->distinct()->get(['data_fakultas_biros.nama_fakultas_biro','data_fakultas_biros.id']);
        $getYear = TahunAkademik::select('year','id')->get();
        return view('rektorat-page.data-proposal.index-monitoring-laporan-proposals', compact('getLembaga','getYear'));
    }

    # Method untuk FPKU
    public function indexUndanganFpku(Request $request)
    {
        $status = $request->status;
        $query = DataFpku::leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'data_fpkus.id_tahun_akademik')
                        ->leftJoin('status_fpkus', 'status_fpkus.id_fpku', '=', 'data_fpkus.id')
                        ->select('data_fpkus.id AS id', 'data_fpkus.*', 'status_fpkus.status_approval')
                        ->where('tahun_akademiks.is_active', 1);

        if ($status == 'pending') {
            $query->where('status_fpkus.status_approval', 1);
        } elseif ($status == 'accepted') {
            $query->where('status_fpkus.status_approval', 2);
        }

        $datas = $query->orderBy('data_fpkus.tgl_kegiatan', 'DESC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $checkState = DB::table('status_fpkus')->where('id_fpku',$data->id)->select('status_approval')->first();
                if($checkState->status_approval == 1){
                    return '<a href="javascript:void(0)" name="validasi" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Validasi FPKU" data-placement="bottom" data-original-title="Validasi FPKU" class="text-warning tombol-yes"><small class="text-warning">Validasi FPKU</small></a>&nbsp;<div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span></div>';
                } else {
                    return '<small class="text-muted">Sudah divalidasi</small>';
                }
                // return '';
            })->addColumn('ketua_pelaksana', function($data){
                $peg = Pegawai::where('id',$data->ketua)->select('nama_pegawai')->first();
                return $peg->nama_pegawai;
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-undangan',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview FPKU" data-original-title="Preview FPKU" class="preview-undangan">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_fpkus')->where('id_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran"><small class="text-info"><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Tidak ada</small>';
                }
            })->addColumn('lihatDelegasi', function($data){
                $isExist = DelegasiFpku::where('id_fpku',$data->id)->select('catatan_delegator','delegasi')->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" class="lihat-delegasi" data-id="'.$data->id.'"><small class="text-info"><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Tidak ada</small>';
                }
            })
            ->rawColumns(['action','ketua_pelaksana','nama_pegawai','undangan','lampirans','lihatDelegasi'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataPegawai = Pegawai::select('id','nama_pegawai')->get();
        return view('rektorat-page.data-fpku.index-undangan-fpku', compact('getDataPegawai'));
    }

    public function confirmUndanganFpku(Request $request)
    {
        # setelah di confirm / validasi oleh WRSDP otomatis broadcast ke email peserta
        $datas = DataFpku::where('id',$request->fpku_id)->select('peserta_kegiatan')->get();
        if($datas->count() > 0){
            foreach($datas as $data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('email')->get();
                foreach($dataPegawai as $result){
                    // Validasi alamat email
                    if (filter_var($result->email, FILTER_VALIDATE_EMAIL)) {
                        $pegawai[] = strtolower($result->email);
                    }                
                }
            }
        } 

        if (isset($pegawai) && count($pegawai) > 0) {
            $isiData = [
                'name' => 'Form Partisipasi Kegiatan Undangan',
                'body' => 'Anda memiliki undangan kegiatan, untuk info lebih detail, silakan login di akun SIMPRO anda. Pada menu Undangan FPKU - Undangan.',
            ];
            Mail::to($pegawai)->send(new UndanganFpku($isiData));
        } 

        $post = DB::table('status_fpkus')->where('id_fpku',$request->fpku_id)->update([
            'status_approval' => 2,
            'broadcast_email' => 1,
            'generate_qrcode' => ''.URL::to('/').'/fpku/'.time().'.png'
        ]);        

        $post = DelegasiFpku::updateOrCreate([
            'id_fpku'           => $request->fpku_id,
            'catatan_delegator' => $request->catatan_delegator,
            'delegasi'          => $request->input('delegasis')
        ]);

        # From delegator modal
        # Get delegation's email
        $getDelEmails = Pegawai::whereIn('id',$request->input('delegasis'))->select('email')->get();
        foreach($getDelEmails as $delmail){
            if (filter_var($delmail->email, FILTER_VALIDATE_EMAIL)){
                $delemails[] = strtolower($delmail->email);
            }
        }
        if (isset($delemails) && count($delemails) > 0){
            $content = [
                'name' => 'Delegasi dari WRSDP',
                'body' => $request->catatan_delegator,
                'link' => ''.URL::to('preview-undangan-fpku').'/'.encrypt($request->fpku_id).'',
            ];
            Mail::to($delemails)->send(new EmailDelegasiFpku($content));        
        }

        return response()->json($post);
    }

    public function indexLaporanFpku(Request $request)
    {
        // Query
        $query = DataFpku::leftJoin('laporan_fpkus', 'laporan_fpkus.id_fpku', '=', 'data_fpkus.id')
            ->leftJoin('pegawais','pegawais.id','=','data_fpkus.ketua')
            ->leftJoin('status_laporan_fpkus', 'status_laporan_fpkus.id_laporan_fpku', '=', 'laporan_fpkus.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'data_fpkus.id_tahun_akademik')
            ->select(
                'laporan_fpkus.id_fpku AS id',
                'laporan_fpkus.id AS id_laporan',
                'pegawais.nama_pegawai AS ketua_pelaksana',
                'data_fpkus.peserta_kegiatan',
                'data_fpkus.ketua',
                'data_fpkus.undangan_dari',
                'data_fpkus.nama_kegiatan',
                'data_fpkus.tgl_kegiatan',
                'status_laporan_fpkus.status_approval'
            )
            ->where('tahun_akademiks.is_active', 1)
            ->orderBy('status_laporan_fpkus.status_approval', 'ASC');

        // Mapping status untuk kondisi dinamis
        $statusMapping = [
            '' => null,
            'all' => null,
            'emp' => ['status_laporan_fpkus.status_approval', '=', null],
            'pending' => ['status_laporan_fpkus.status_approval', '=', 1],
            'accepted' => ['status_laporan_fpkus.status_approval', '=', 3],
            'denied' => ['status_laporan_fpkus.status_approval', '=', 2],
        ];

        // Tambahkan kondisi berdasarkan status jika diperlukan
        if (array_key_exists($request->status, $statusMapping) && $statusMapping[$request->status] !== null) {
            $query->where(
                $statusMapping[$request->status][0],
                $statusMapping[$request->status][1],
                $statusMapping[$request->status][2] ?? null
            );
        }

        // Eksekusi query
        $datas = $query->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->id_laporan != null ){
                    if($data->status_approval == 3){
                        return '<a href="javascript:void(0)" class="text-success"><i class="bx bx-xs bx-check-shield"></i> ACC Rektorat</a>';
                    } elseif($data->status_approval == 2){
                        return '<a href="javascript:void(0)" class="text-danger"><i class="bx bx-xs bx-shield-x"></i> Ditolak Rektorat</a>';
                    } else {
                        $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id_laporan.'" data-placement="bottom" title="Tolak" data-original-title="Tolak" class="tombol-no-laporan btn btn-xs btn-danger"><small><i class="bx bx-xs bx-x"></i></small></a>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id_laporan.'" data-placement="bottom" title="Setuju" data-placement="bottom" data-original-title="Setuju" class="tombol-yes-laporan btn btn-xs btn-success"><small><i class="bx bx-xs bx-check-double"></i></small></a>';
                        $button .= '&nbsp;';
                        $button .= '<div class="spinner-grow spinner-grow-sm text-warning" role="status"><span class="visually-hidden"></span>';
                        return $button;                  
                    }
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada laporan</small>';
                }
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_laporan_fpkus')->where('id_laporan_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran-laporan-fpku"><small class="text-info"><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i></small>';
                }
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id_laporan.'"><small><i class="bx bx-detail bx-tada-hover"></i> Detail</small></a>';
            })
            ->rawColumns(['action','undangan','lampirans','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-fpku.index-laporan-fpku');
    }

    public function confirmLaporanFpku(Request $request)
    {
        $post = DB::table('status_laporan_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','status_laporan_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$request->id)->update([
            'status_approval' => 3,
            'generate_qrcode' => ''.URL::to('/').'/fpku-rep/'.time().'.png'
        ]);
        return response()->json($post);
    }
    public function ignoreLaporanFpku(Request $request)
    {
        $post = DB::table('status_laporan_fpkus')->where('id_laporan_fpku',$request->id_laporan)->update([
            'status_approval' => 2,
            'keterangan_ditolak' => $request->keterangan_ditolak
        ]);
        return response()->json($post);
    }

    public function viewlampiranLaporanFpku(Request $request)
    {
        $datas = DB::table('lampiran_laporan_fpkus')->leftJoin('laporan_fpkus','laporan_fpkus.id','=','lampiran_laporan_fpkus.id_laporan_fpku')->where('laporan_fpkus.id_fpku',$request->fpku_id)->select('lampiran_laporan_fpkus.id','lampiran_laporan_fpkus.nama_berkas','lampiran_laporan_fpkus.berkas','lampiran_laporan_fpkus.keterangan','lampiran_laporan_fpkus.link_gdrive')->get();
        $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Berkas</th>
                            <th>Lihat</th>
                        </tr>
                    </thead>
                    <tbody>';
                    if($datas->count() > 0){
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
                    } else {
                        $html .= 
                        '<tr>
                            <td colspan="3"> No data available in table </td>
                        </tr>';
                    }
            $html .= '</tbody>
                </table>';
        return response()->json(['card' => $html]);
    }

    # Detail Anggaran FPKU
    public function lihatDetailAnggaran(Request $request)
    {
        # check data rencana anggaran first
        $rencana = DB::table('data_rencana_anggaran_fpkus')->where('id_laporan_fpku',$request->laporan_fpku_id)->get();

        $datas = DB::table('data_realisasi_anggaran_fpkus')->where('id_laporan_fpku',$request->laporan_fpku_id)->get();

        $html = '<div class="divider divider-dashed text-start"><div class="divider-text"><a class="me-1 mb-1 text-info" data-bs-toggle="collapse"  href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> Klik untuk melihat data rencana anggaran </a></div></div>';
        $html .= '<div class="collapse" id="collapseExample">
            <table class="table table-bordered table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th style="text-align: center">#</th>
                    <th style="text-align: center">Item</th>
                    <th style="text-align: center">Biaya Satuan</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Freq</th>
                    <th style="text-align: center">Sumber Dana</th>
                </tr>
            </thead>
            <tbody>';
            if($rencana->count() > 0){
                $total_biaya = array(
                    'Kampus' => 0,
                    'Mandiri' => 0,
                    'Hibah' => 0
                );
                $grand_total_rencana = 0;
                foreach($rencana as $no => $dataRencana){
                    $html .= '<tr>
                        <td style="text-align: center">'.++$no.'</td>
                        <td>'.$dataRencana->item.'</td>
                        <td style="text-align: right;">'.currency_IDR($dataRencana->biaya_satuan).'</td>
                        <td style="text-align: center">'.$dataRencana->quantity.'</td>
                        <td style="text-align: center">'.$dataRencana->frequency.'</td>';
                            if ($dataRencana->sumber_dana == '1') {
                                $text = 'Kampus';
                                $total_biaya['Kampus'] += $dataRencana->biaya_satuan * $dataRencana->quantity * $dataRencana->frequency;
                            } else if ($dataRencana->sumber_dana == '2') {
                                $text = 'Mandiri';
                                $total_biaya['Mandiri'] += $dataRencana->biaya_satuan * $dataRencana->quantity * $dataRencana->frequency;
                            } else {
                                $text = 'Hibah';
                                $total_biaya['Hibah'] += $dataRencana->biaya_satuan * $dataRencana->quantity * $dataRencana->frequency;
                            }
                        $html .= '<td style="text-align: center">'.$text.'</td>
                    </tr>';
                }
                $grand_total_rencana = $total_biaya['Kampus'] + $total_biaya['Mandiri'] + $total_biaya['Hibah'];
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Kampus</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Kampus']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Mandiri</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Mandiri']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Hibah</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Hibah']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right; color: orange;"><b>Grand Total</b></td><td style="text-align: right; color: orange;"><b>' . currency_IDR($grand_total_rencana) . '</b></td></tr>';
            } else {
                $html .= '<tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data rencana anggaran</td>
                </tr>';
            }
            $html .= '</body>
            </table>
        </div>';

        $html .= '<div class="divider divider-dashed text-start"><div class="divider-text mt-2">Tabel Realisasi Anggaran</div></div>';

        $html .= '<table class="table table-bordered table-hover table-sm mb-3">
            <thead class="table-dark">
                <tr>
                    <th style="text-align: center">#</th>
                    <th style="text-align: center">Item</th>
                    <th style="text-align: center">Biaya Satuan</th>
                    <th style="text-align: center">Qty</th>
                    <th style="text-align: center">Freq</th>
                    <th style="text-align: center">Sumber Dana</th>
                </tr>
            </thead>
            <tbody>';
            if($datas->count() > 0){
                $total_biaya = array(
                    'Kampus' => 0,
                    'Mandiri' => 0,
                    'Hibah' => 0
                );
                $grand_total_realisasi = 0;
                foreach($datas as $no => $data){
                    $html .= '<tr>
                        <td style="text-align: center">'.++$no.'</td>
                        <td>'.$data->item.'</td>
                        <td style="text-align: right;">'.currency_IDR($data->biaya_satuan).'</td>
                        <td style="text-align: center">'.$data->quantity.'</td>
                        <td style="text-align: center">'.$data->frequency.'</td>';
                            if ($data->sumber_dana == '1') {
                                $text = 'Kampus';
                                $total_biaya['Kampus'] += $data->biaya_satuan * $data->quantity * $data->frequency;
                            } else if ($data->sumber_dana == '2') {
                                $text = 'Mandiri';
                                $total_biaya['Mandiri'] += $data->biaya_satuan * $data->quantity * $data->frequency;
                            } else {
                                $text = 'Hibah';
                                $total_biaya['Hibah'] += $data->biaya_satuan * $data->quantity * $data->frequency;
                            }
                        $html .= '<td style="text-align: center">'.$text.'</td>
                    </tr>';
                }
                $grand_total_realisasi = $total_biaya['Kampus'] + $total_biaya['Mandiri'] + $total_biaya['Hibah'];
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Kampus</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Kampus']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Mandiri</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Mandiri']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Hibah</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Hibah']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right; color: orange;"><b>Grand Total</b></td><td style="text-align: right; color: orange;"><b>' . currency_IDR($grand_total_realisasi) . '</b></td></tr>';
            } else {
                $html .= '<tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data realisasi anggaran</td>
                </tr>';
            }
            $html .= '</body>
            </table>';

        return response()->json(['card' => $html]);
    }

    public function lihatHistoryDelegasi(Request $request)
    {
        $datas = DelegasiFpku::where('id_fpku',$request->fpku_id)->get();
        if($datas->count() > 0){
            $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Catatan Delegator</th>
                            <th>Delegasi</th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach($datas as $item){
                        $html .= '<tr>
                            <td><li></li></td>
                            <td>'.$item->catatan_delegator.'</td>';
                            $getPegawai = Pegawai::whereIn('id',$item->delegasi)->select('nama_pegawai')->get();
                            foreach($getPegawai as $result){
                                $pegawai[] = $result->nama_pegawai;
                                
                            }
                        $html .= '<td>'.implode(", <br>", $pegawai).'</td>
                        </tr>';
                    }   
                    $html .= '</tbody>
                    </table>';                 
        } else {
            $html = '<table class="table table-bordered table-hover table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Catatan Delegator</th>
                            <th>Delegasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">No data available in table</td>
                        </tr>
                    </tbody>
                </table>';
        }

        return response()->json(['card' => $html]);

    }

    public function indexMonitoringFpku(Request $request)
    {
        $query = DataFpku::leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'data_fpkus.id_tahun_akademik')
                        ->leftJoin('status_fpkus', 'status_fpkus.id_fpku', '=', 'data_fpkus.id')
                        ->select('data_fpkus.id AS id', 'data_fpkus.*', 'status_fpkus.status_approval')
                        ->where('tahun_akademiks.is_active', 1);

        $datas = $query->orderBy('data_fpkus.tgl_kegiatan', 'DESC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('ketua_pelaksana', function($data){
                $peg = Pegawai::where('id',$data->ketua)->select('nama_pegawai')->first();
                return $peg->nama_pegawai;
            })->addColumn('nama_pegawai', function($data){
                $dataPegawai = Pegawai::whereIn('id',$data->peserta_kegiatan)->select('nama_pegawai')->get();
                foreach($dataPegawai as $result){
                    $pegawai[] = $result->nama_pegawai;
                    
                }
                return implode(", <br>", $pegawai);
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-undangan',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview FPKU" data-original-title="Preview FPKU" class="preview-undangan">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_fpkus')->where('id_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran"><small class="text-info"><i class="bx bx-detail bx-tada-hover bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i></small>';
                }
            })->addColumn('lihatDelegasi', function($data){
                $isExist = DelegasiFpku::where('id_fpku',$data->id)->select('catatan_delegator','delegasi')->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" class="lihat-delegasi" data-id="'.$data->id.'"><small class="text-info"><i class="bx bx-paperclip bx-xs"></i> lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Tidak ada</small>';
                }
            })
            ->rawColumns(['ketua_pelaksana','nama_pegawai','undangan','lampirans','lihatDelegasi'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataPegawai = Pegawai::select('id','nama_pegawai')->get();
        return view('rektorat-page.data-fpku.index-monitoring-fpkus', compact('getDataPegawai'));
    }

    public function indexMonitoringLaporanFpku(Request $request)
    {
        // Query
        $query = DataFpku::leftJoin('laporan_fpkus', 'laporan_fpkus.id_fpku', '=', 'data_fpkus.id')
            ->leftJoin('status_laporan_fpkus', 'status_laporan_fpkus.id_laporan_fpku', '=', 'laporan_fpkus.id')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'data_fpkus.id_tahun_akademik')
            ->select(
                'laporan_fpkus.id_fpku AS id',
                'laporan_fpkus.id AS id_laporan',
                'data_fpkus.peserta_kegiatan',
                'data_fpkus.ketua',
                'data_fpkus.undangan_dari',
                'data_fpkus.no_surat_undangan',
                'data_fpkus.nama_kegiatan',
                'data_fpkus.tgl_kegiatan',
                'status_laporan_fpkus.status_approval'
            )
            ->where('tahun_akademiks.is_active', 1)
            ->orderBy('status_laporan_fpkus.status_approval', 'ASC');
            
        $datas = $query->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('status', function($data){
                if($data->id_laporan != null ){
                    if($data->status_approval == 3){
                        return '<small class="text-success"><i>ACC Rektorat</i></small>'; 
                    } elseif($data->status_approval == 2){
                        return '<small class="text-danger"><i>Ditolak Rektorat</i></small>'; 
                    } else {
                        return '<small class="text-warning"><i>Menunggu validasi rektorat</i></small>';                   
                    }
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i> Belum ada laporan</small>';
                }
            })->addColumn('undangan', function($data){
                return '<a href="'.Route('preview-laporan-fpku',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan FPKU" data-original-title="Preview Laporan FPKU" class="preview-laporan-fpku">'.$data->undangan_dari.'</a>';
            })->addColumn('lampirans', function($data){
                $isExist = DB::table('lampiran_laporan_fpkus')->where('id_laporan_fpku',$data->id)->get();
                if($isExist->count() > 0){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="lihat lampiran" data-placement="bottom" data-original-title="lihat lampiran" class="lihat-lampiran-laporan-fpku"><small><i class="bx bx-paperclip bx-xs"></i> Lihat</small></a>';
                } else {
                    return '<small><i class="bx bx-minus-circle bx-xs"></i></small>';
                }
            })->addColumn('ketua_pelaksana', function($data){
                $name = Pegawai::where('id','=',$data->ketua)->select('nama_pegawai')->first();
                return $name->nama_pegawai;
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id_laporan.'"><small><i class="bx bx-detail bx-xs bx-tada-hover"></i> Detail</small></a>';
            })
            ->rawColumns(['status','undangan','lampirans','ketua_pelaksana','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('rektorat-page.data-fpku.index-monitoring-laporan-fpkus');
    }

    public function indexMonitoringSarpras(Request $request)
    {
        $tahun_akademik = $request->tahun_akademik;
        $lembaga = $request->lembaga;

        $query = DataPengajuanSarpras::leftJoin('proposals','proposals.id','=','data_pengajuan_sarpras.id_proposal')
            ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
            ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
            ->select('proposals.id AS idproposal','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','jenis_kegiatans.nama_jenis_kegiatan','proposals.tgl_event','proposals.id_tahun_akademik','tahun_akademiks.id','proposals.id_jenis_kegiatan','proposals.id_form_rkat','proposals.nama_kegiatan')
            ->groupBy('proposals.id','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','jenis_kegiatans.nama_jenis_kegiatan','proposals.tgl_event','proposals.id_tahun_akademik','tahun_akademiks.id','proposals.id_jenis_kegiatan','proposals.id_form_rkat','proposals.nama_kegiatan');
        
         // Filter berdasarkan tahun akademik
         if ($tahun_akademik && $tahun_akademik != 'all') {
            $query->where('tahun_akademiks.id', $tahun_akademik);
        }

        // Filter berdasarkan lembaga
        if ($lembaga && $lembaga != 'all') {
            if ($lembaga == 'others') {
                $query->whereNull('proposals.id_fakultas_biro');
            } else {
                $query->where('proposals.id_fakultas_biro', $lembaga);
            }
        }

        $datas = $query->orderBy('proposals.tgl_event', 'DESC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->idproposal.'" class="status-mon-sarpras"><small class="text-info"><i class="bx bx-detail bx-tada-hover bx-xs"></i> Detail</small></a></a>';
            })
            ->rawColumns(['action'])
            ->addIndexColumn(true)
            ->make(true);
        }

        $getLembaga = Proposal::leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')->distinct()->get(['data_fakultas_biros.nama_fakultas_biro','data_fakultas_biros.id']);
        $getYear = TahunAkademik::select('year','id')->get();
        return view('rektorat-page.data-proposal.index-monitoring-sarpras', compact('getLembaga','getYear'));
    }
    
    public function statusSarpras(Request $request)
    {
        $datas = DataPengajuanSarpras::where('id_proposal',$request->proposal_id)->get();
        
            $html = '<div class="card">
            <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Tgl Kegiatan</th>
                    <th>Sarpras Item</th>
                    <th>Jumlah</th>
                    <th width="25%;">Status</th>
                </tr>
                </thead>
                <tbody>';
                if($datas->count() > 0){
                    foreach($datas as $no => $data){
                        $html .= '<tr>
                            <td>'.++$no.'</td>
                            <td>'.tanggal_indonesia($data->tgl_kegiatan).'</td>
                            <td>'.$data->sarpras_item.'</td>
                            <td>'.$data->jumlah.'</td>
                            <td style="text-align: center;">';
                            if($data->status == '1'){
                                $html .= '<small class="text-warning"><i>Belum divalidasi</i></small>';
                            } else if($data->status == '2'){
                                $html .= '<small class="text-success"><i>ACC Admin Umum</i></small>';
                            } else if($data->status == '3'){
                                $html .= '<small class="text-danger"><i>Ditolak</i></small>';
                            } else {
                                $html .= '<small class="text-success"><i>ACC Admin Umum</i></small>';
                            }
                            $html .= '</td>   
                        </tr>';
                    }
                } else {
                    $html .= '<tr>
                        <td colspan="5" style="text-align: center;">No data available in table</td>                    
                    </tr>';
                }
            $html .= '</tbody>
                </table> 
                </div>            
            </div>';
        return response()->json(['card' => $html]);
    }
}
