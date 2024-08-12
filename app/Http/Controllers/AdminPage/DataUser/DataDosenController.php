<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Setting\Dosen;

class DataDosenController extends Controller
{
    public function index(Request $request)
    {
        $datas = Dosen::all();

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
        return view('admin-page.data-user.data-dosen');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user_id' => 'required',
            'email' => 'required',
            'password' => 'required',
        ],[
            'name.required' => 'Anda belum menginputkan nama',
            'user_id.required' => 'Anda belum menginputkan NIP',
            'email.required' => 'Anda belum menginputkan email',
            'password.required' => 'Anda belum menginputkan password',
        ]);

        $post = Dosen::updateOrCreate(['id' => $request->id],
                [
                    'name' => $request->name,
                    'user_id' => $request->user_id,
                    'email' => $request->email,
                    'password'  => Hash::make($request['password']),
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Dosen::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Dosen::where('id',$id)->delete();     
        return response()->json($post);
    }
}
