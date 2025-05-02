<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\LaporanProposal;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\HandleProposal;
use App\Models\Master\FormRkat;
use Auth; use DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:pegawai');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::guard('pegawai')->user()){
            $countProposals = Proposal::where('user_id',Auth::user()->user_id)->count();

            $countProposalAcc = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['proposals.user_id',Auth::user()->user_id],['status_proposals.status_approval',5]])
                ->count();

            $countProposalOnGoing = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['proposals.user_id',Auth::user()->user_id],['status_proposals.status_approval','!=',5],['proposals.is_archived',0]])
                ->count();

            $countProposalDeclined = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['proposals.user_id',Auth::user()->user_id],['proposals.is_archived',1]])
                ->count();

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

            # Dashboard WRSDP and WRAK Proposals
            $totalProposals = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
            ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['proposals.is_archived',0]])
            ->whereIn('status_proposals.status_approval',[1,2,3,4,5])->count();
            $totalProposalPending = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['proposals.is_archived',0]])
                ->whereIn('status_proposals.status_approval',[1,2,3])->count();
            $totalProposalDiterima = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['status_proposals.status_approval','=',5],['proposals.is_archived',0]])->count();
            $totalProposalDitolak = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['status_proposals.status_approval','=',4],['proposals.is_archived',0]])->count();

            # Dashboard WRSDP and WRAK Laporan Proposals
            $totalLaporanProposals = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where('proposals.id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->whereIn('status_laporan_proposals.status_approval',[1,2,3,4,5])->count();
            $totalLaporanProposalPending = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where('id_jenis_kegiatan',$this->arrJenisKegiatan())
                ->whereIn('status_laporan_proposals.status_approval',[1,2,3])->count();
            $totalLaporanProposalDiterima = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['status_laporan_proposals.status_approval','=',5]])->count();
            $totalLaporanProposalDitolak = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['id_jenis_kegiatan',$this->arrJenisKegiatan()],['status_laporan_proposals.status_approval','=',4]])->count();

            # Dashboard WRSDP and WRAK Proposal Saya


            # Dashboard Admin Umum Proposals
            $totalProposalsSadm = Proposal::count();
            $totalProposalPendingSadm = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',1],['proposals.is_archived',0]])->count();
            $totalProposalDiterimaSadm = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',5],['proposals.is_archived',0]])->count();
            $totalProposalDitolakSadm = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',2],['proposals.is_archived',0]])->orWhere('status_proposals.status_approval','=',4)->count();
            $totalLaporanProposalsSadm = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')->count();
            $totalLaporanProposalPendingSadm = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',1]])->count();
            $totalLaporanProposalDiterimaSadm = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',5]])->count();
            $totalLaporanProposalDitolakSadm = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',2]])
                ->orWhere('status_laporan_proposals.status_approval','=',4)->count();

            # Dashboard PEG Proposals

            $getJabatanIs = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->where([['jabatan_pegawais.id_pegawai',Auth::guard('pegawai')->user()->id],['jabatans.kode_jabatan','=',$recentRole]])
                ->select('jabatan_pegawais.id_fakultas_biro')
                ->first();

            $totalProposalsDekanBiro = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['proposals.is_archived',0],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();
                
            $totalProposalPendingDekanBiro = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',1],['proposals.is_archived',0],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();
            $totalProposalDiterimaDekanBiro = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',5],['proposals.is_archived',0],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();
            $totalProposalDitolakDekanBiro = Proposal::leftJoin('status_proposals','status_proposals.id_proposal','=','proposals.id')
                ->where([['status_proposals.status_approval','=',4],['proposals.is_archived',0],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();

            # Dashboard PEG Laporan Proposals
            $totalLaporanProposalsDekanBiro = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro],['status_laporan_proposals.status_approval','>',0]])->count();
            $totalLaporanProposalPendingDekanBiro = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',1],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();
            $totalLaporanProposalDiterimaDekanBiro = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',5],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();
            $totalLaporanProposalDitolakDekanBiro = LaporanProposal::leftJoin('proposals','proposals.id','=','laporan_proposals.id_proposal')
                ->leftJoin('status_laporan_proposals','status_laporan_proposals.id_laporan_proposal','=','proposals.id')
                ->where([['status_laporan_proposals.status_approval','=',4],['proposals.id_fakultas_biro',$getJabatanIs->id_fakultas_biro]])->count();

            # Chart RKAT
            $actualData = Proposal::leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->select(
                    'tahun_akademiks.year',
                    DB::raw('COALESCE(data_fakultas_biros.kode_fakultas_biro, "Rektorat") as kode_fakultas_biro'),
                    DB::raw('SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) as actual')
                )
                ->where([['data_realisasi_anggarans.sumber_dana', '=', 1],['tahun_akademiks.is_active',1],['proposals.id_jenis_kegiatan',1]])
                ->groupBy('data_fakultas_biros.kode_fakultas_biro', 'tahun_akademiks.year')
                ->get();


            $expectedData = FormRkat::join('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'form_rkats.id_fakultas_biro')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
                ->select(
                    DB::raw('COALESCE(data_fakultas_biros.kode_fakultas_biro, "Rektorat") as kode_fakultas_biro'),
                    DB::raw('SUM(form_rkats.total) as expected')
                )
                ->where('tahun_akademiks.is_active',1)
                ->groupBy('data_fakultas_biros.kode_fakultas_biro')
                ->get();
            

            $mergedData = $actualData->map(function ($actual) use ($expectedData) {
                $expected = $expectedData->firstWhere('kode_fakultas_biro', $actual->kode_fakultas_biro);
            
                return [
                    'x' => (string) $actual->kode_fakultas_biro,
                    'y' => $actual->actual,
                    'goals' => [
                        [
                            'name' => 'Total RKAT',
                            'value' => $expected->expected ?? 0,
                            'strokeHeight' => 5,
                            'strokeColor' => '#ed2da7' # fce626
                        ]
                    ]
                ];
            })->keyBy('x')->values();


            # Chart Non-RKAT
            $actualData2 = Proposal::leftJoin('tahun_akademiks', 'tahun_akademiks.id', '=', 'proposals.id_tahun_akademik')
                ->leftJoin('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'proposals.id_fakultas_biro')
                ->leftJoin('data_realisasi_anggarans', 'data_realisasi_anggarans.id_proposal', '=', 'proposals.id')
                ->select(
                    'tahun_akademiks.year',
                    DB::raw('COALESCE(data_fakultas_biros.kode_fakultas_biro, "Rektorat") as kode_fakultas_biro'),
                    DB::raw('SUM(data_realisasi_anggarans.biaya_satuan * data_realisasi_anggarans.quantity * data_realisasi_anggarans.frequency) as actual')
                )
                ->where([['data_realisasi_anggarans.sumber_dana', '=', 1],['tahun_akademiks.is_active',1],['proposals.id_jenis_kegiatan',2]])
                ->groupBy('data_fakultas_biros.kode_fakultas_biro', 'tahun_akademiks.year')
                ->get();


            $expectedData2 = FormRkat::join('data_fakultas_biros', 'data_fakultas_biros.id', '=', 'form_rkats.id_fakultas_biro')
                ->leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
                ->select(
                    DB::raw('COALESCE(data_fakultas_biros.kode_fakultas_biro, "Rektorat") as kode_fakultas_biro'),
                    DB::raw('SUM(form_rkats.total) as expected')
                )
                ->where('tahun_akademiks.is_active',1)
                ->groupBy('data_fakultas_biros.kode_fakultas_biro')
                ->get();
            

            $mergedData2 = $actualData2->map(function ($actual) use ($expectedData2) {
                $expected = $expectedData2->firstWhere('kode_fakultas_biro', $actual->kode_fakultas_biro);
            
                return [
                    'x' => (string) $actual->kode_fakultas_biro,
                    'y' => $actual->actual,
                ];
            })->keyBy('x')->values();

            return view('dashboard.admin-dashboard', compact([
                'countProposals',
                'countProposalAcc',
                'countProposalOnGoing',
                'countProposalDeclined',
                'recentRole',
                'totalProposals',
                'totalProposalPending',
                'totalProposalDiterima',
                'totalProposalDitolak',
                'totalLaporanProposals',
                'totalLaporanProposalPending',
                'totalLaporanProposalDiterima',
                'totalLaporanProposalDitolak',
                'totalProposalsSadm',
                'totalProposalPendingSadm',
                'totalProposalDiterimaSadm',
                'totalProposalDitolakSadm',
                'totalLaporanProposalsSadm',
                'totalLaporanProposalPendingSadm',
                'totalLaporanProposalDiterimaSadm',
                'totalLaporanProposalDitolakSadm',
                'totalProposalsDekanBiro',
                'totalProposalPendingDekanBiro',
                'totalProposalDiterimaDekanBiro',
                'totalProposalDitolakDekanBiro',
                'totalLaporanProposalsDekanBiro',
                'totalLaporanProposalPendingDekanBiro',
                'totalLaporanProposalDiterimaDekanBiro',
                'totalLaporanProposalDitolakDekanBiro',
                'mergedData',
                'mergedData2'
            ]));
        } else {
            return redirect()->intended('/');
        }
    }

    public function uiModul()
    {
        return view('ui-modul');
    }

    protected function arrJenisKegiatan()
    {
        $datas = HandleProposal::select('id_jenis_kegiatan')->where('id_pegawai',Auth::user()->id)->get();
        $getID = [];
        if($datas->count() > 0){
            foreach($datas as $data){
                $getID = $data->id_jenis_kegiatan;
            }
        }else{
            $getID = '';
        }
        return $getID;
    }
}
