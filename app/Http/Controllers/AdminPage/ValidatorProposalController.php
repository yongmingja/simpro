<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Jabatan;
use App\Models\Master\ValidatorProposal;

class ValidatorProposalController extends Controller
{
    public function index(Request $request)
    {
        $datas = ValidatorProposal::leftJoin('jabatans AS a','a.id','=','validator_proposals.diusulkan_oleh')
            ->leftJoin('jabatans AS b','b.id','=','validator_proposals.diketahui_oleh')
            ->select('validator_proposals.id AS id','a.nama_jabatan AS pengusul','b.nama_jabatan AS diketahui')
            ->get();

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
        $getJabatan = Jabatan::all();
        return view('admin-page.validator.index', compact('getJabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'diusulkan_oleh'    => 'required',
            'diketahui_oleh'    => 'required',
        ],[
            'diusulkan_oleh.required'   => 'Anda belum memilih pengusul',
            'diketahui_oleh.required'   => 'Anda belum memilih yang mengetahui',
        ]);

        $post = ValidatorProposal::updateOrCreate(['id' => $request->id],
                [
                    'diusulkan_oleh'    => $request->diusulkan_oleh,
                    'diketahui_oleh'    => $request->diketahui_oleh,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = ValidatorProposal::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = ValidatorProposal::where('id',$id)->delete();     
        return response()->json($post);
    }
}
