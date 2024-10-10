<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Pegawai;
use App\Imports\PegawaisImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class DataPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $datas = Pegawai::all();
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
        return view('admin-page.data-user.data-pegawai');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'nama_pegawai' => 'required',
            'email' => 'required',
            'password' => 'required',
        ],[
            'user_id.required' => 'Anda belum menginputkan nama',
            'nama_pegawai.required' => 'Anda belum menginputkan NIP',
            'email.required' => 'Anda belum menginputkan email',
            'password.required' => 'Anda belum menginputkan password',
        ]);

        $post = Pegawai::updateOrCreate(['id' => $request->id],
                [
                    'user_id' => $request->user_id,
                    'nama_pegawai' => $request->nama_pegawai,
                    'email' => $request->email,
                    'password'  => Hash::make($request['password']),
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'id_status_pegawai' => 1,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Pegawai::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Pegawai::where('id',$id)->delete();     
        return response()->json($post);
    }

    public function importDataPegawai(Request $request) 
    {
    	$request->validate([
           'file_csv' => 'required|file',
        ],[
            'file_csv.required' => 'Anda belum memilih berkas CSV'
        ]);
		$file = $request->file('file_csv'); 
		$nama_file = rand().$file->getClientOriginalName();
		$file->move('file_pegawai',$nama_file);
		$post = Excel::import(new PegawaisImport, public_path('/file_pegawai/'.$nama_file));
        return response()->json($post);
    }
}
