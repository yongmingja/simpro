<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Setting\Rektorat;

class DataRektoratController extends Controller
{
    public function index(Request $request)
    {
        $datas = Rektorat::all();

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
        return view('admin-page.data-user.data-rektorat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ],[
            'email.required' => 'Anda belum menginputkan email',
            'password.required' => 'Anda belum menginputkan password',
        ]);

        $post = Rektorat::updateOrCreate(['id' => $request->id],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password'  => Hash::make($request['password']),
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = Rektorat::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Rektorat::where('id',$id)->delete();     
        return response()->json($post);
    }
}
