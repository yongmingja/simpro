<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\General\LaporanProposal;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\HandleProposal;
use Auth;
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
                'totalLaporanProposalDitolakDekanBiro'
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
