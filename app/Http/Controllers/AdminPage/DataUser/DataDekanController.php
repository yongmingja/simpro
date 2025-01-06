<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Setting\Dekan;
use App\Models\General\DataFakultas;
use App\Models\General\DataRencanaAnggaran;
use Auth;

class DataDekanController extends Controller
{
    public function index(Request $request)
    {
        $datas = Dekan::leftJoin('data_fakultas','data_fakultas.id','=','dekans.id_fakultas')->select('dekans.id AS id','dekans.name','data_fakultas.nama_fakultas')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';

                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getDataFakultas = DataFakultas::all();
        return view('admin-page.data-user.data-dekan', compact('getDataFakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_fakultas' => 'required',
        ],[
            'id_fakultas.required' => 'Anda belum memilih fakultas',
        ]);

        $post = Dekan::updateOrCreate(['id' => $request->id],
                [
                    'name' => $request->name,
                    'id_fakultas' => $request->id_fakultas
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Dekan::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Dekan::where('id',$id)->delete();     
        return response()->json($post);
    }

    public function dashDekan(Request $request)
    {
        $datas = DataRencanaAnggaran::leftJoin('proposals','proposals.id','=','data_rencana_anggarans.id_proposal')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','proposals.id_jenis_kegiatan')
            ->leftJoin('data_fakultas','data_fakultas.id','=','proposals.id_fakultas')
            ->leftJoin('data_prodis','data_prodis.id_fakultas','=','data_fakultas.id')
            ->select('data_rencana_anggarans.id AS id','data_rencana_anggarans.*','proposals.id AS idpro','jenis_kegiatans.nama_jenis_kegiatan','data_fakultas.nama_fakultas','data_prodis.nama_prodi')
            ->where('proposals.id_fakultas','=',Auth::user()->id_fakultas)
            ->orderBy('status','ASC')->get();
        if($request->ajax()){
            return datatables()->of($datas)
                ->addIndexColumn(true)
                ->make(true);
        }
        return view('dashboard.dekan-dashboard');
    }
}
