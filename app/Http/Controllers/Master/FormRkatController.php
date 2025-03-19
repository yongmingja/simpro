<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\FormRkat;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\Jabatan;
use App\Models\General\TahunAkademik;
use Illuminate\Support\Facades\Session;
use Auth;

class FormRkatController extends Controller
{
    public function index(Request $request)
    {
        // $recentRole = Session::get('selected_peran');
        if(session()->get('selected_peran') == ''){
            $getPeran = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
                ->select('jabatan_pegawais.id AS jab_id','jabatans.kode_jabatan','jabatans.id AS id_jabatan')
                ->first();
            $recentRole = $getPeran->kode_jabatan;
            $recentRoleId = $getPeran->jab_id;
        } else {
            $getPeran = Jabatan::leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->where('jabatans.kode_jabatan',session()->get('selected_peran'))->select('jabatans.id AS id_jabatan','jabatan_pegawais.id AS jab_id')->first();
            $recentRoleId = $getPeran->jab_id;
        }

        $datas = FormRkat::leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
            ->select('form_rkats.id AS id','form_rkats.*','tahun_akademiks.year')
            ->where('form_rkats.penanggung_jawab',$recentRoleId)
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->status_validasi == 0){
                    $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                    return $button;                    
                } else {
                    return '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';
                }
            })->addColumn('status', function($data){
                if($data->status_validasi == 1){
                    return '<i class="bx bx-check-shield text-success"></i> verified';
                } elseif($data->status_validasi == 2) {
                    return '<i class="bx bx-shield-x text-danger"></i> denied';
                } else {
                    return '<i class="bx bx-loader-circle bx-spin text-warning"></i> Pending';
                }
            })
            ->rawColumns(['action','status'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getTahunAkademik = TahunAkademik::select('id','year','is_active')->orderBy('year','DESC')->get();
        return view('master.form-rkat.index', compact('getTahunAkademik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tahun_akademik'  => 'required',
            'sasaran_strategi'   => 'required',
            'program_strategis'  => 'required',
            'program_kerja'      => 'required',
            'kode_renstra'       => 'required',
            'nama_kegiatan'      => 'required',
            'kode_pagu'          => 'required',
        ],[
            'id_tahun_akademik.required' => 'Anda belum memilih tahun akademik',
            'sasaran_strategi.required'  => 'Anda belum menginputkan sasaran strategi',
            'program_strategis.required' => 'Anda belum menginputkan program strategis',
            'program_kerja.required'     => 'Anda belum menginputkan program kerja',
            'kode_renstra.required'      => 'Anda belum menginputkan kode renstra',
            'nama_kegiatan.required'     => 'Anda belum menginputkan nama kegiatan',
            'kode_pagu.required'         => 'Anda belum menginputkan kode pagu',
        ]);

        // $recentRole = Session::get('selected_peran');
        if(session()->get('selected_peran') == ''){
            $getPeran = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
                ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
                ->select('jabatan_pegawais.id AS jab_id','jabatans.kode_jabatan','jabatans.id AS id_jabatan')
                ->first();
            $recentRole = $getPeran->kode_jabatan;
            $recentRoleId = $getPeran->jab_id;
        } else {
            $getPeran = Jabatan::leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
                ->where('jabatans.kode_jabatan',session()->get('selected_peran'))->select('jabatans.id AS id_jabatan','jabatan_pegawais.id AS jab_id')->first();
            $recentRoleId = $getPeran->jab_id;
        }

        $post = FormRkat::updateOrCreate(['id' => $request->id],
                [
                    'id_tahun_akademik'  => $request->id_tahun_akademik,
                    'sasaran_strategi'   => $request->sasaran_strategi,
                    'program_strategis'  => $request->program_strategis,
                    'program_kerja'      => $request->program_kerja,
                    'kode_renstra'       => $request->kode_renstra,
                    'nama_kegiatan'      => $request->nama_kegiatan,
                    'penanggung_jawab'   => $recentRoleId,
                    'kode_pagu'          => $request->kode_pagu,
                    'total'              => preg_replace('/\D/','', $request->total),
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = FormRkat::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = FormRkat::where('id',$id)->delete();     
        return response()->json($post);
    }

    public function dataForm(Request $request)
    {
        $datas = FormRkat::leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
            ->where('tahun_akademiks.is_active',1)
            ->select('form_rkats.id AS id','form_rkats.*','tahun_akademiks.is_active','tahun_akademiks.year','tahun_akademiks.id AS id_year')
            ->get();
        return response()->json($datas);

    }
}
