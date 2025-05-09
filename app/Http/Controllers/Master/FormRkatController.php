<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\FormRkat;
use App\Models\Master\JabatanPegawai;
use App\Models\Master\Jabatan;
use App\Models\General\TahunAkademik;
use App\Models\General\DataFakultasBiro;
use Illuminate\Support\Facades\Session;
use App\Imports\RkatsImport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class FormRkatController extends Controller
{
    public function index(Request $request)
    {
        # $recentRole = Session::get('selected_peran');
        # No more using this, cuz only WRSDP and Superadmin who be able to insert the RKAT since April 24 2025
        // if(session()->get('selected_peran') == ''){
        //     $getPeran = JabatanPegawai::leftJoin('jabatans','jabatans.id','=','jabatan_pegawais.id_jabatan')
        //         ->where('jabatan_pegawais.id_pegawai',Auth::user()->id)
        //         ->select('jabatan_pegawais.id AS jab_id','jabatans.kode_jabatan','jabatans.id AS id_jabatan')
        //         ->first();
        //     $recentRole = $getPeran->kode_jabatan;
        //     $recentRoleId = $getPeran->jab_id;
        // } else {
        //     $getPeran = Jabatan::leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
        //         ->where('jabatans.kode_jabatan',session()->get('selected_peran'))->select('jabatans.id AS id_jabatan','jabatan_pegawais.id AS jab_id')->first();
        //     $recentRoleId = $getPeran->jab_id;
        // }

        $datas = FormRkat::leftJoin('tahun_akademiks','tahun_akademiks.id','=','form_rkats.id_tahun_akademik')
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','form_rkats.id_fakultas_biro')
            ->select('form_rkats.id AS id','form_rkats.*','tahun_akademiks.year','data_fakultas_biros.nama_fakultas_biro')
            ->where('tahun_akademiks.is_active',1)
            // ->where('form_rkats.penanggung_jawab',$recentRoleId) # no longer used April 24 2025
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
                    return '<i class="text-success">ACC Rektorat</i> ';
                } elseif($data->status_validasi == 2) {
                    return '<i class="text-danger">Ditolak</i>';
                } else {
                    return '<i class="bx bx-loader-circle bx-spin text-warning"></i> Pending';
                }
            })
            ->rawColumns(['action','status'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getTahunAkademik = TahunAkademik::select('id','year','is_active')->orderBy('year','DESC')->get();
        $getFakultasBiro = DataFakultasBiro::select('id','nama_fakultas_biro')->orderBy('nama_fakultas_biro','ASC')->get();
        return view('master.form-rkat.index', compact('getTahunAkademik','getFakultasBiro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_fakultas_biro'   => 'required',
            'sasaran_strategi'   => 'required',
            'program_strategis'  => 'required',
            'program_kerja'      => 'required',
            'kode_renstra'       => 'required',
            'nama_kegiatan'      => 'required',
            'kode_pagu'          => 'required',
        ],[
            'id_fakultas_biro.required'  => 'Anda belum memilih data ini',
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

        $getTahunAkademik = TahunAkademik::where('is_active',1)->select('id')->first();

        $post = FormRkat::updateOrCreate(['id' => $request->id],
                [
                    'id_tahun_akademik'  => $getTahunAkademik->id,
                    'id_fakultas_biro'   => $request->id_fakultas_biro,
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
            ->leftJoin('data_fakultas_biros','data_fakultas_biros.id','=','form_rkats.id_fakultas_biro')
            ->where([['tahun_akademiks.is_active',1],['form_rkats.status_validasi',1]])
            ->select('form_rkats.id AS id','form_rkats.*','data_fakultas_biros.kode_fakultas_biro','tahun_akademiks.is_active','tahun_akademiks.year','tahun_akademiks.id AS id_year')
            ->orderBy('form_rkats.id_fakultas_biro','DESC')
            ->get();
        return response()->json($datas);

    }

    public function importDataRkat(Request $request) 
    {
    	$request->validate([
           'file_csv' => 'required|file',
        ],[
            'file_csv.required' => 'Anda belum memilih berkas CSV'
        ]);

        $idFakultasBiro = $request->pilih_fakultas_biro;

		$file = $request->file('file_csv'); 
		$nama_file = rand().$file->getClientOriginalName();
        $file->move('file_import_rkat',$nama_file);
        
		$post = Excel::import(new RkatsImport($idFakultasBiro), public_path('/file_import_rkat/'.$nama_file));
        return response()->json($post);

    }
}
