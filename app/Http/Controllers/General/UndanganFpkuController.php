<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataFpku;
use App\Models\Master\Pegawai;
use Auth; use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UndanganFpkuController extends Controller
{
    public function index(Request $request)
    {
        $userID = Auth::user()->id;
        $datas = DataFpku::whereRaw("JSON_CONTAINS(peserta_kegiatan,'\"$userID\"')")->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="'.Route('preview-undangan',encrypt(['id' => $data->id])).'" target="_blank" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Preview Undangan" data-original-title="Preview Undangan" class="preview-undangan btn btn-outline-info btn-sm"><i class="bx bx-show bx-xs"></i></a>';
            })
            ->rawColumns(['action'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.undangan-fpku.index');
    }

    public function previewUndangan($id)
    {
        $ID = decrypt($id);
        $dataUndangan = DataFpku::leftJoin('pegawais','pegawais.id','=','data_fpkus.dibuat_oleh')->where('data_fpkus.id',$ID)->select('data_fpkus.id AS id','data_fpkus.*','pegawais.nama_pegawai')->get();
        $dataKeperluan = DB::table('fpku_keperluans')->where('id_fpku',$ID)->get();
        $verifiedQrCode = DB::table('status_fpkus')->where([['id_fpku',$ID],['status_approval',3]])->get();

        $fileName = date(now()).'_undangan_FPKU'.'.pdf';
        $pdf = PDF::loadview('general.undangan-fpku.preview-undangan', compact('dataUndangan','dataKeperluan','verifiedQrCode'));
        $pdf->setPaper('F4','P');
        $pdf->getDomPDF()->set_option("isPhpEnabled", true);
        $pdf->getFontMetrics()->get_font("helvetica", "bold");
        $pdf->output();
        $canvas = $pdf->getDomPDF()->getCanvas();
        return $pdf->stream($fileName);
    }
}
