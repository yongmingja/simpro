<?php

namespace App\Http\Controllers\AdminPage\DataUser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Setting\Admin;
use App\Models\General\DataPengajuanSarpras;

class DataAdminController extends Controller
{
    public function index(Request $request)
    {
        $datas = Admin::all();

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
        return view('admin-page.data-user.data-admin');
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

        $post = Admin::updateOrCreate(['id' => $request->id],
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
        $post  = Admin::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Admin::where('id',$id)->delete();     
        return response()->json($post);
    }

    public function dashAdmin(Request $request)
    {
        $datas = DataPengajuanSarpras::orderBy('status','ASC')->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                if($data->status == '1'){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Ditolak" data-original-title="Ditolak" class="btn btn-danger btn-sm tombol-no"><i class="bx bx-xs bx-x"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" name="see-file" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Setuju atau di ACC" data-placement="bottom" data-original-title="Setuju atau di ACC" class="btn btn-success btn-sm tombol-yes"><i class="bx bx-xs bx-check-double"></i></a>';
                } elseif ($data->status == '2'){
                    return '<span class="badge bg-label-success">Diterima</span>';
                } elseif ($data->status == '3'){
                    return '<span class="badge bg-label-danger">Ditolak</span>';
                } else {
                    return '<span class="badge bg-label-success">Diterima</span>';
                }
                
            })->addColumn('detil',function($data){
                if($data->status == '3'){
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-ket="'.$data->alasan.'" data-placement="bottom" title="Keterangan" data-original-title="Keterangan" class="btn btn-outline-secondary btn-sm alasan">Lihat detil</a>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['action','detil'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('dashboard.admin-dashboard');
    }
}
