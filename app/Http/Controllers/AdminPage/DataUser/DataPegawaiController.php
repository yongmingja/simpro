<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Pegawai;
use App\Imports\PegawaisImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use App\Setting\Mahasiswa;
use App\Rules\MatchOldPassword;
use Auth;

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
            })->addColumn('reset-pass', function($data){
                return '<button type="button" id="'.$data->user_id.'" name="reset-pass" class="reset-pass btn btn-info btn-xs" data-toggle="tooltip" data-placement="bottom" title="Reset Password" data-original-title="Reset Password"><i class="bx bx-fingerprint bx-xs"></i> Reset</button>';
            })
            ->rawColumns(['action','reset-pass'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('admin-page.data-user.data-pegawai');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required',
            'nama_pegawai'  => 'required',
            'email'         => 'required',
            'password'      => 'required',
        ],[
            'user_id.required'      => 'Anda belum menginputkan NIP',
            'nama_pegawai.required' => 'Anda belum menginputkan nama pegawai',
            'email.required'        => 'Anda belum menginputkan email',
            'password.required'     => 'Anda belum menginputkan password',
        ]);

        $post = Pegawai::updateOrCreate(['id' => $request->id],
                [
                    'user_id'           => $request->user_id,
                    'nama_pegawai'      => $request->nama_pegawai,
                    'email'             => $request->email,
                    'password'          => Hash::make($request['password']),
                    'jenis_kelamin'     => $request->jenis_kelamin,
                    'agama'             => $request->agama,
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

    public function resetPass(Request $request)
    {
        $post = Pegawai::where('user_id',$request->user_id)->update([
            'password' => Hash::make($request->user_id)
        ]);
        return response()->json($post);
    }

    public function profile()
    {
        if(Auth::guard('pegawai')->check()){
            $datas = Pegawai::where('user_id',Auth::user()->user_id)->get();
        } else if(Auth::guard('mahasiswa')->check()){
            $datas = Mahasiswa::where('user_id',Auth::user()->user_id)->get();
        }
        return view('admin-page.data-user.profile', compact('datas'));
    }

    public function postPass(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', new MatchOldPassword],
            'new_password'          => ['required'],
            'new_confirm_password'  => ['same:new_password'],
        ]);
   
        if(Auth::guard('pegawai')->check()){
            Pegawai::where('user_id',Auth::user()->user_id)->update(['password'=> Hash::make($request->new_password)]);
        }elseif(Auth::guard('mahasiswa')->check()){
            Mahasiswa::where('user_id',Auth::user()->user_id)->update(['password'=> Hash::make($request->new_password)]);
        }
        return redirect()->route('profile')->with('success','Password has changed successfully!');
    }

    public function updateEmail(Request $request)
    {
        $id_user = Auth::user()->id;
        $post = Pegawai::where('id',$id_user)->update([
            'email' => $request->update_email
        ]);

        return redirect()->route('profile')->with('success','Email updated successfully!');
    }
}
