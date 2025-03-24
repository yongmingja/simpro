<?php

namespace App\Http\Controllers\DekanPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\LaporanProposal;
use App\Models\Master\HandleProposal;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\FormRkat;
use App\Models\Master\Pegawai;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDitolakDekan;
use Auth; use DB;

class LaporanProposalController extends Controller
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
            ->where([['jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id],['jabatans.kode_jabatan','=',$recentRole]])
            ->select('jabatan_pegawais.id_fakultas_biro')
            ->first();

        if($request->status == '' || $request->status == 'all'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where('proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro)
                ->get();
        }
        if($request->status == 'pending'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',1]])
                ->get();
        }
        if($request->status == 'accepted'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',5]])
                ->get();
        }
        if($request->status == 'denied'){
            $datas = Proposal::leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
                ->leftJoin('pegawais','pegawais.user_id','=','proposals.user_id')
                ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','proposals.id_fakultas_biro')
                ->leftJoin('data_prodi_biros','data_prodi_biros.id','=','proposals.id_prodi_biro')
                ->leftJoin('laporan_proposals','laporan_proposals.id_proposal','=','proposals.id')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->select('proposals.id AS id','proposals.*','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas_biros.nama_fakultas_biro','data_prodi_biros.nama_prodi_biro','pegawais.nama_pegawai','laporan_proposals.created_at AS tgl_proposal')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval',4]])
                ->get();
        }

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('laporan', function($data){
                $query = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return '<a href="'.Route('preview-laporan-proposal',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Laporan Proposal" data-original-title="Preview Laporan Proposal" class="preview-proposal btn btn-outline-success btn-sm"><i class="bx bx-file bx-xs"></i> lihat laporan</a>';
                } else {
                    return '<i class="text-secondary">Belum ada laporan</i>';
                }
            })->addColumn('action', function($data){
                $query = DB::table('status_proposals')->where('id_proposal',$data->id)->select('status_approval')->get();
                if($query->count() > 0){
                    return $this->statusLaporanProposal($data->id);
                } else {
                    return '<i class="text-secondary">Belum ada laporan</i>';
                }
            })->addColumn('detail', function($data){
                return '<a href="javascript:void()" class="lihat-detail text-info" data-id="'.$data->id.'"><i class="bx bx-detail bx-tada-hover"></i> Detail</a>';
            })
            ->rawColumns(['laporan','action','detail'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dekan-page.laporan-proposal.index');
    }

    protected function statusLaporanProposal($id)
    {
        $query = DB::table('status_laporan_proposals')->select('status_approval','keterangan_ditolak')->where('id_laporan_proposal','=',$id)->get();
        if($query){
            foreach($query as $data){                
                if($data->status_approval == 1){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                } elseif($data->status_approval == 2){
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Ditolak</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 3) {
                    return '<i class="text-warning">Menunggu Validasi Rektorat</i>';
                } elseif($data->status_approval == 4) {
                    return '<a href="javascript:void(0)" class="info-ditolakdekan" data-keteranganditolak="'.$data->keterangan_ditolak.'" data-toggle="tooltip" data-placement="bottom" title="Klik untuk melihat keterangan ditolak" data-original-title="Klik untuk melihat keterangan ditolak"><span class="badge bg-label-danger">Pending Rektorat</span><span class="badge bg-danger badge-notifications">Cek ket. ditolak</span></a>';
                } elseif($data->status_approval == 5) {
                    return '<i class="text-success">ACC Rektorat</i>';
                } else {
                    return '<i class="text-secondary">Pending</i>';
                }
            }
        } else {
            return 'x';
        }
    }

    public function approvalDeanY(Request $request)
    {
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->proposal_id)->update([
            'status_approval' => 3, 
            'keterangan_ditolak' => ''
        ]);

        $content = [
            'name' => 'Update Status Laporan Proposal',
            'body' => 'Anda memiliki laporan proposal yang perlu divalidasi. Silahkan buka SIMPRO.',
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
        $post = DB::table('status_laporan_proposals')->where('id_laporan_proposal',$request->propsl_id)->update([
            'status_approval'       => 2,
            'keterangan_ditolak'    => $request->keterangan_ditolak
        ]);

        # Get the specific email address according to proposal id
        $getEmail = Pegawai::leftJoin('proposals','proposals.user_id','=','pegawais.user_id')
            ->select('pegawais.email')
            ->where('proposals.id',$request->propsl_id)
            ->first();
        
        if (filter_var($getEmail->email, FILTER_VALIDATE_EMAIL)){
            $emailAddress = strtolower($getEmail->email);
        }
        
        if (isset($emailAddress) && count($emailAddress) > 0){
            $content = [
                'name' => 'Update Status Laporan Proposal Anda',
                'body' => 'Mohon maaf, laporan proposal anda tidak dapat dilanjutkan. Silahkan periksa catatan atau alasan ditolak',
            ];
            Mail::to($emailAddress)->send(new EmailDitolakDekan($content));        
        } else {
            return 'No valid email addresses found';
        }

        return response()->json($post);
    }

    public function lihatDetailRealisasiAnggaran(Request $request)
    {
        # check data rencana anggaran first
        $rencana = DB::table('data_rencana_anggarans')->where('id_proposal',$request->proposal_id)->get();

        $datas = DB::table('data_realisasi_anggarans')->where('id_proposal',$request->proposal_id)->get();
        
        # Check if RKAT or Non-RKAT
        $checkType = Proposal::where('id',$request->proposal_id)->select('id_form_rkat')->first();
        if($checkType['id_form_rkat'] != null){
            $getTotal = FormRkat::where('id',$checkType['id_form_rkat'])->first();
            $html = '<h4 class="mb-3">Total Budget: '.currency_IDR($getTotal->total).'</h4>';
        } else {
            $html = '<p class="mb-3 text-muted">Non RKAT</p>';
        }

        $html .= '<div class="divider divider-dashed text-start"><div class="divider-text"><a class="me-1 mb-1 text-info" data-bs-toggle="collapse"  href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> Klik untuk melihat data rencana anggaran </a></div></div>';
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
                    'Mandiri' => 0
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
                            } else {
                                $text = 'Mandiri';
                                $total_biaya['Mandiri'] += $dataRencana->biaya_satuan * $dataRencana->quantity * $dataRencana->frequency;
                            } 
                        $html .= '<td style="text-align: center">'.$text.'</td>
                    </tr>';
                }
                $grand_total_rencana = $total_biaya['Kampus'] + $total_biaya['Mandiri'];
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Kampus</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Kampus']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Mandiri</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Mandiri']) . '</td></tr>';
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
                    'Mandiri' => 0
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
                            } else {
                                $text = 'Mandiri';
                                $total_biaya['Mandiri'] += $data->biaya_satuan * $data->quantity * $data->frequency;
                            } 
                        $html .= '<td style="text-align: center">'.$text.'</td>
                    </tr>';
                }
                $grand_total_realisasi = $total_biaya['Kampus'] + $total_biaya['Mandiri'];
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Kampus</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Kampus']) . '</td></tr>';
                $html .= '<tr><td colspan="5" style="text-align: right;"><i>Total Mandiri</i></td><td style="text-align: right;">' . currency_IDR($total_biaya['Mandiri']) . '</td></tr>';
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
}
