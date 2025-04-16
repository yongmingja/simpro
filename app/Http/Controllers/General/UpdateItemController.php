<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\DataPengajuanSarpras;
use App\Models\General\DataRencanaAnggaran;
use App\Models\General\DataRealisasiAnggaran;
use DB;

class UpdateItemController extends Controller
{
    public function indexUpdateSarpras(Request $request, $id)
    {
        $ID = decrypt($id);
        return view('general.pengajuan-proposal.update-items.update-sarpras', ['id' => $id]);
    }

    public function pageUpdateSarpras(Request $request, $id)
    {
        $getID = decrypt($id);
        $datas = DataPengajuanSarpras::where('id_proposal',$getID)->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-tgl="'.$data->tgl_kegiatan.'" data-item="'.$data->sarpras_item.'" data-jumlah="'.$data->jumlah.'" data-sumber="'.$data->sumber_dana.'" data-keterangan="'.$data->keterangan.'" data-placement="bottom" title="Edit data sarpras" data-original-title="Edit data sarpras" class="edit-post"><i class="bx bx-edit bx-xs text-success"></i></a>&nbsp;&nbsp;
                <a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-post"><i class="bx bx-trash bx-xs text-danger"></i></a>';
            })->addColumn('status', function($data){
                if($data->status == 1){
                    return '<small class="text-warning"><i class="bx bx-minus-circle bx-xs"></i> Belum divalidasi</small>';
                } elseif($data->status = 2){
                    return '<small class="text-danger"><i class="bx bx-x-circle bx-xs"></i> Ditolak</small>';
                } else {
                    return '<small class="text-success"><i class="bx bx-check-circle bx-xs"></i> Diterima</small>';
                }
            })->addColumn('sumber_dana', function($data){
                if($data->sumber_dana == 1){
                    return 'Kampus';
                } else {
                    return 'Mandiri';
                }
            })
            ->rawColumns(['action','status','sumber_dana'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.pengajuan-proposal.update-items.update-sarpras',['proposal_id' => $getID]);
    }

    public function simpanSarpras(Request $request)
    {
        $getId = decrypt($request->id_proposal);
        $post = DataPengajuanSarpras::updateOrCreate([
            'id_proposal'   => $getId['id'],
            'tgl_kegiatan'  => $request->tgl_kegiatan,
            'sarpras_item'  => $request->sarpras_item,
            'jumlah'        => $request->jumlah,
            'sumber_dana'   => $request->sumber_dana,
            'keterangan'    => $request->keterangan,
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function editItemSarpras(Request $request){
        $post = DataPengajuanSarpras::where('id',$request->e_sarpras_id)->update([
            'tgl_kegiatan'  => $request->e_tgl_kegiatan,
            'sarpras_item'  => $request->e_sarpras_item,
            'jumlah'        => $request->e_jumlah,
            'sumber_dana'   => $request->e_sumber,
            'keterangan'    => $request->e_keterangan,
            'status'        => 1,
            'alasan'        => '',
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function hapusItemSarpras(Request $request)
    {
        $post = DataPengajuanSarpras::where('id',$request->id)->delete(); 
        return response()->json($post);
    }

    ####### Update Item Anggaran #######
    public function indexUpdateAnggaran(Request $request, $id)
    {
        $ID = decrypt($id);
        return view('general.pengajuan-proposal.update-items.update-anggaran', ['id' => $id]);
    }

    public function pageUpdateAnggaran(Request $request, $id)
    {
        $getID = decrypt($id);
        $datas = DataRencanaAnggaran::where('id_proposal',$getID)->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-item="'.$data->item.'" data-biaya-satuan="'.$data->biaya_satuan.'" data-quantity="'.$data->quantity.'" data-frequency="'.$data->frequency.'" data-sumber-dana="'.$data->sumber_dana.'" data-placement="bottom" title="Edit data sarpras" data-original-title="Edit data sarpras" class="edit-post"><i class="bx bx-edit bx-xs text-success"></i></a>&nbsp;&nbsp;
                <a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-post"><i class="bx bx-trash bx-xs text-danger"></i></a>';
            })->addColumn('status', function($data){
                if($data->status == 1){
                    return '<small class="text-warning"><i class="bx bx-minus-circle bx-xs"></i> Belum divalidasi</small>';
                } elseif($data->status = 2){
                    return '<small class="text-danger"><i class="bx bx-x-circle bx-xs"></i> Ditolak</small>';
                } else {
                    return '<small class="text-success"><i class="bx bx-check-circle bx-xs"></i> Diterima</small>';
                }
            })->addColumn('sumber_dana', function($data){
                if($data->sumber_dana == 1){
                    return 'Kampus';
                } else {
                    return 'Mandiri';
                }
            })
            ->rawColumns(['action','status','sumber_dana'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.pengajuan-proposal.update-items.update-anggaran',['proposal_id' => $getID]);
    }

    public function simpanAnggaran(Request $request)
    {
        $getId = decrypt($request->id_proposal);
        $post = DataRencanaAnggaran::updateOrCreate([
            'id_proposal'   => $getId['id'],
            'item'          => $request->item,
            'biaya_satuan'  => $request->biaya_satuan,
            'quantity'      => $request->quantity,
            'frequency'     => $request->frequency,
            'sumber_dana'   => $request->sumber_dana,
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function editItemAnggaran(Request $request){
        $post = DataRencanaAnggaran::where('id',$request->e_anggaran_id)->update([
            'item'          => $request->e_item,
            'biaya_satuan'  => $request->e_biaya_satuan,
            'quantity'      => $request->e_quantity,
            'frequency'     => $request->e_frequency,
            'sumber_dana'   => $request->e_sumber_dana,
            'status'        => 1,
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function hapusItemAnggaran(Request $request)
    {
        $post = DataRencanaAnggaran::where('id',$request->id)->delete(); 
        return response()->json($post);
    }

    # Page Untuk Revisi Anggaran Laporan Proposal
    public function indexRevisiAnggaranLaporanProposal(Request $request, $id)
    {
        $ID = decrypt($id);
        return view('general.laporan-proposal.update-items.update-anggaran', ['id' => $id]);
    }

    public function pageRevisiAnggaranLaporanProposal(Request $request, $id)
    {
        $getID = decrypt($id);
        $datas = DataRealisasiAnggaran::where('id_proposal',$getID)->get();
        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                return '<a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-item="'.$data->item.'" data-biaya-satuan="'.$data->biaya_satuan.'" data-quantity="'.$data->quantity.'" data-frequency="'.$data->frequency.'" data-sumber-dana="'.$data->sumber_dana.'" data-placement="bottom" title="Edit data sarpras" data-original-title="Edit data sarpras" class="edit-post"><i class="bx bx-edit bx-xs text-success"></i></a>&nbsp;&nbsp;
                <a href="javascript:void(0)" data-toggle="tooltip" data-toggle="tooltip" data-id="'.$data->id.'" data-placement="bottom" title="Hapus item ini?" data-original-title="Hapus item ini?" class="delete-post"><i class="bx bx-trash bx-xs text-danger"></i></a>';
            })->addColumn('status', function($data){
                if($data->status == 1){
                    return '<small class="text-warning"><i class="bx bx-minus-circle bx-xs"></i> Belum divalidasi</small>';
                } elseif($data->status = 2){
                    return '<small class="text-danger"><i class="bx bx-x-circle bx-xs"></i> Ditolak</small>';
                } else {
                    return '<small class="text-success"><i class="bx bx-check-circle bx-xs"></i> Diterima</small>';
                }
            })->addColumn('sumber_dana', function($data){
                if($data->sumber_dana == 1){
                    return 'Kampus';
                } else {
                    return 'Mandiri';
                }
            })
            ->rawColumns(['action','status','sumber_dana'])
            ->addIndexColumn(true)
            ->make(true);
        }
        return view('general.laporan-proposal.update-items.update-anggaran',['proposal_id' => $getID]);
    }

    public function simpanAnggaranLaporanProposal(Request $request)
    {
        $getId = decrypt($request->id_proposal);
        $post = DataRealisasiAnggaran::updateOrCreate([
            'id_proposal'   => $getId['id'],
            'item'          => $request->item,
            'biaya_satuan'  => $request->biaya_satuan,
            'quantity'      => $request->quantity,
            'frequency'     => $request->frequency,
            'sumber_dana'   => $request->sumber_dana,
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function editItemAnggaranLaporanProposal(Request $request){
        $post = DataRealisasiAnggaran::where('id',$request->e_anggaran_id)->update([
            'item'          => $request->e_item,
            'biaya_satuan'  => $request->e_biaya_satuan,
            'quantity'      => $request->e_quantity,
            'frequency'     => $request->e_frequency,
            'sumber_dana'   => $request->e_sumber_dana,
            'status'        => 1,
            'updated_at'    => now()
        ]);
        return response()->json($post);
    }

    public function hapusItemAnggaranLaporanProposal(Request $request)
    {
        $post = DataRealisasiAnggaran::where('id',$request->id)->delete(); 
        return response()->json($post);
    }
}
