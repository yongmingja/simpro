<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\TahunAkademik;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        $datas = TahunAkademik::orderBy('year','DESC')->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';

                return $button;
            })->addColumn('state', function($data){
                return '<div class="custom-control">
                <label class="switch switch-primary" for="'.$data->id.'">
                <input type="checkbox" class="switch-input" onclick="PeriodeStatus('.$data->id.','.$data->is_active.')" name="period-status" id="'.$data->id.'" '.(($data->is_active=='1')?'checked':"").'>
                <span class="switch-toggle-slider"><span class="switch-on"><i class="bx bx-check"></i></span><span class="switch-off"><i class="bx bx-x"></i></span></span></label></div>';
            })
            ->rawColumns(['action','state'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.tahun-akademik.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year'          => 'required',
            'start_date'    => 'required',
            'end_date'      => 'required',
        ],[
            'year.required'         => 'Anda belum menginputkan tahun',
            'start_date.required'   => 'Anda belum memilih tanggal mulai',
            'end_date.required'     => 'Anda belum memilih tanggal akhir',
        ]);

        $checkState = TahunAkademik::where('is_active','=',1)->get();
        $isActive = $request->input('is_active');
        if($checkState->count() > 0){
            foreach($checkState as $data){
                if($isActive == null) {
                    $isActive = 0;
                } else {
                    $data->update(['is_active' => 0]);
                    $isActive = 1;
                }
            }
        } else {
            $isActive = 1;
        } 

        $post = TahunAkademik::updateOrCreate(['id' => $request->id],
                [
                    'year'          => $request->year,
                    'start_date'    => $request->start_date,
                    'end_date'      => $request->end_date,
                    'is_active'     => $isActive,
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = TahunAkademik::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = TahunAkademik::where('id',$id)->delete();     
        return response()->json($post);
    }

    public function switchPeriode(Request $request)
    {
        $checkState = TahunAkademik::where('is_active','=',1)->get();
        $req    = $request->is_active == '1' ? 0 : 1;
        foreach($checkState as $data){
            if($req == '1'){
                $data->update(['is_active' => 0]);
                $req = 1;
            } else {
                $data->update(['is_active' => 1]);
                $req = 0;
            }
        }

        $post = TahunAkademik::updateOrCreate(['id' => $request->id],['is_active' => $req]); 
        return response()->json($post);
    }
}
