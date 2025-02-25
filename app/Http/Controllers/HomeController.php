<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\General\Proposal;
use App\Models\Master\JabatanPegawai;
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
        $this->middleware('auth:pegawai,mahasiswa');
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

            return view('dashboard.admin-dashboard', compact('countProposals','countProposalAcc','countProposalOnGoing','countProposalDeclined','recentRole'));
        } elseif(Auth::guard('mahasiswa')->user()){
            return view('dashboard.mahasiswa-dashboard');
        } else {
            return redirect()->intended('/');
        }
    }

    public function uiModul()
    {
        return view('ui-modul');
    }
}
