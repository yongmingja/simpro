<?php

namespace App\Http\Controllers\DekanPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\DataRencanaAnggaran;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\FormRkat;
use App\Models\Master\Pegawai;
use App\Models\Master\HandleProposal;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDitolakDekan;
use App\Mail\EmailDiterimaDekan;
use Auth;
use DB;

class DataProposalController extends Controller
{
    public function index(Request $request)
    {
        // $recentRole = Session::get('selected_peran');
        if(session()->get('selected_peran') == ''){
            $getPeran = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
                ->select('jabatans.kode_jabatan','jabatan_pegawais.id_fakultas_biro')
                ->first();
            $recentRole = $getPeran->kode_jabatan;
        } else {
            $recentRole = session()->get('selected_peran');
        }

        $getJabatanIs = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
            ->where([['jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id],['jabatans.kode_jabatan','=',$recentRole]]) # Remember this is not only for DKN but for BRO as well, so this is not the best query
            ->select('jabatan_pegawais.id_fakultas_biro')
            ->first();

            $status = $request->status ?? 'all'; // Default ke 'all' jika status kosong

            // Inisialisasi query dasar
            $query = Proposal::leftJoin('jenis_kegiatans', 'jenis_kegiatans.id', '=', 'proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais', 'pegawais.user_id', '=', 'proposals.user_id')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros', 'data_prodi_biros.id', '=', 'proposals.id_prodi_biro')
                ->leftJoin('status_proposals', 'status_proposals.id_proposal', '=', 'proposals.id')
                ->leftJoin('form_rkats', 'form_rkats.id', '=', 'proposals.id_form_rkat')
                ->leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
                ->select('proposals.id AS id', 'proposals.*', 'jenis_kegiatans.nama_jenis_kegiatan', 'data_fakultas_biros.nama_fakultas_biro', 'data_prodi_biros.nama_prodi_biro', 'pegawais.nama_pegawai AS nama_user', 'form_rkats.total')
                ->where([['proposals.id_fakultas_biro', $getJabatanIs->id_fakultas_biro], ['tahun_akademiks.is_active', 1]]);
            
            // Tambahkan filter berdasarkan status
            $statusApproval = null;
            switch ($status) {
                case 'pending':
                    $statusApproval = 1;
                    break;
                case 'accepted':
                    $statusApproval = 5;
                    break;
                case 'denied':
                    $statusApproval = 4;
                    break;
                default:
                    $statusApproval = null;
                    break;
            }
            
            if ($statusApproval !== null) {
                $query->where('status_proposals.status_approval', $statusApproval);
            }
            
            $datas = $query->orderBy('status_proposals.status_approval', 'ASC')->get();
            
            

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $btn = $this->statusProposal($data->id);                
                return $btn;
            })->addColumn('preview', function($data){
                # check any attachment
                $q = DB::table('lampiran_proposals')->where('id_proposal',$data->id)->count();
                if($q > 0){
                    $button = '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Lihat Lampiran" data-original-title="Lihat Lampiran" class="btn btn-outline-info btn-sm v-lampiran"><i class="bx bx-xs bx-file"></i></a>';
                    return $button;
                } else {
                    return '<a href="'.Route('preview-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Proposal" data-original-title="Preview Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-food-menu bx-xs"></i></a>';
                }
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><i class="bx bx-detail bx-tada-hover"></i> Detail</a>';
            })
            ->rawColumns(['action','preview','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dekan-page.data-proposal.index');
    }

    public function rencana(Request $request)
    {
        $datas = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->get();
        
            $html = '<div class="card">
            <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Biaya Satuan</th>
                    <th>Qty</th>
                    <th>Freq</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>';
        foreach($datas as $no => $data){
            $total = 0;
            $grandTotal = 0;
            $html .= '<tr>
                    <td>'.++$no.'</td>
                    <td>'.$data->item.'</td>
                    <td>'.currency_IDR($data->biaya_satuan).'</td>
                    <td>'.$data->quantity.'</td>
                    <td>'.$data->frequency.'</td>';
            $total = $data->biaya_satuan * $data->quantity * $data->frequency; 
            $html .= '<td>'.currency_IDR($total).'</td>
                    </tr>';
                    
            }
            $grandTotal = DataRencanaAnggaran::where('id_proposal',$request->proposal_id)->select(DB::raw('sum(biaya_satuan * quantity * frequency) as total'))->get()->sum('total');
            $html .= '<tr><td colspan="5" style="text-align: right;"><b>Grand Total</b></td>
                    <td><b>'.currency_IDR($grandTotal).'</b></td>
                </tr>';

                $html .= '</tbody>
                </table>            
                </div>            
            </div>';
            $html .= '<div class="modal-footer mt-3">';
            if($data->status != '1'){
                $html .= '<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>';
            } else {
                $html .= '<button type="submit" class="btn btn-primary btn-block tombol-validasi" id="'.$request->proposal_id.'">Validasi</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>';
            }
                $html .= '</div>';
        return response()->json(['card' => $html]);
    }

    public function approvalDeanY(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->proposal_id)->update([
            'status_approval' => 3, 
            'keterangan_ditolak' => ''
        ]);   
        
        $content = [
            'name' => 'Update Status Proposal',
            'body' => 'Anda memiliki proposal yang perlu divalidasi. Silahkan buka SIMPRO.',
        ];

        # seleksi alamat email berdasarkan RKAT atau Non-RKAT
        $getJenisKegiatan = Proposal::select('id_jenis_kegiatan')->where('id',$request->proposal_id)->first();
        $getEmailRektorat = HandleProposal::leftJoin('pegawais','pegawais.id','=','handle_proposals.id_pegawai')
            ->select('pegawais.email')
            ->whereJsonContains('handle_proposals.id_jenis_kegiatan',(string) $getJenisKegiatan->id_jenis_kegiatan)
            ->first();        

        Mail::to(strtolower($getEmailRektorat->email))->send(new EmailDiterimaDekan($content));

        return response()->json($post);
    }

    public function approvalDeanN(Request $request)
    {
        $post = DB::table('status_proposals')->where('id_proposal',$request->propsl_id)->update([
            'status_approval'       => 2,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);

        # Get the specific email address according to proposal id
        $getEmail = Pegawai::leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
            ->select('pegawais.email')
            ->where('proposals.id',$request->propsl_id)
            ->first();
        
        if ($getEmail->email != null){
            $content = [
                'name' => 'Proposal Ditolak!',
                'body' => 'Mohon maaf, proposal anda tidak dapat dilanjutkan. Silahkan periksa catatan atau alasan ditolak',
            ];
            Mail::to(strtolower($getEmail->email))->send(new EmailDitolakDekan($content));        
        } else {
            return 'No valid email addresses found';
        }

        return response()->json($post);
    }

    protected function statusProposal($id)
    {
        $query = DB::table('status_proposals')
            ->select('status_approval', 'keterangan_ditolak')
            ->where('id_proposal', '=', $id)
            ->get();

        if ($query->isNotEmpty()) { // Memastikan data tidak kosong
            $data = $query->first(); // Mengambil data pertama

            switch ($data->status_approval) {
                case 1:
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $id . '" data-placement="bottom" title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;'
                        . '<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="' . $id . '" data-placement="bottom" title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                case 2:
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="' . $data->keterangan_ditolak . '" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak">'
                        . '<small class="text-danger">Ditolak Atasan</small><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                case 3:
                    return '<small><i class="text-warning">Menunggu Validasi Rektorat</i></small>';
                case 4:
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="' . $data->keterangan_ditolak . '" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak">'
                        . '<small class="text-danger">Ditolak Rektorat</small><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                case 5:
                    return '<small><i class="text-success">ACC Rektorat</i></small>';
                default:
                    return '<small><i class="text-secondary">Pending</i></small>';
            }
        } 

        // Jika query kosong
        return 'x';

    }

    public function lihatDetailAnggaran(Request $request)
    {
        $datas = DB::table('data_rencana_anggarans')->where('id_proposal',$request->proposal_id)->get();
        
        # Check if RKAT or Non-RKAT
        $checkType = Proposal::where('id',$request->proposal_id)->select('id_form_rkat')->first();
        if($checkType['id_form_rkat'] != null){
            $getTotal = FormRkat::where('id',$checkType['id_form_rkat'])->first();
            $html = '<h4 class="mb-3">Total Budget: '.currency_IDR($getTotal->total).'</h4>';
        } else {
            $html = '<p class="mb-3 text-muted">Non RKAT</p>';
        }

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
                    'Mandiri' => 0
                );
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
                            } else {
                                $text = 'Mandiri';
                                $total_biaya['Mandiri'] += $data->biaya_satuan * $data->quantity * $data->frequency;
                            } 
                        $html .= '<td style="text-align: center">'.$text.'</td>
                    </tr>';
                }
                $grand_total = $total_biaya['Kampus'] + $total_biaya['Mandiri'];
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Kampus</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Kampus']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Mandiri</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Mandiri']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right; color: orange;"><b>Grand Total</b></td><td style="text-align: right; color: orange;"><b>' . currency_IDR($grand_total) . '</b></td></tr>';
            } else {
                $html .= '<tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data rencana anggaran</td>
                </tr>';
            }
            $html .= '</body>
            </table>';

        return response()->json(['card' => $html]);
                
    }
}
