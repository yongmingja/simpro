<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\HandleProposal;
use App\Models\Master\Pegawai;
use App\Models\Master\Jabatan;
use App\Models\General\JenisKegiatan;

class HandleProposalController extends Controller
{
    public function index(Request $request)
    {
        $datas = HandleProposal::leftJoin('jabatans','jabatans.id','=','handle_proposals.id_jabatan')
            ->leftJoin('jabatan_pegawais','jabatan_pegawais.id_jabatan','=','jabatans.id')
            ->leftJoin('pegawais','pegawais.id','=','jabatan_pegawais.id_pegawai')
            ->leftJoin('jenis_kegiatans','jenis_kegiatans.id','=','handle_proposals.id_jenis_kegiatan')
            ->select('handle_proposals.id AS id','pegawais.nama_pegawai','jenis_kegiatans.nama_jenis_kegiatan','handle_proposals.id_jenis_kegiatan')
            ->orderBy('pegawais.nama_pegawai','ASC')
            ->get();

        if($request->ajax()){
            return datatables()->of($datas)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Edit" data-original-title="Edit" class="edit btn btn-success btn-xs edit-post"><i class="bx bx-xs bx-edit"></i></a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete btn btn-danger btn-xs"><i class="bx bx-xs bx-trash"></i></button>';

                return $button;
            })->addColumn('kategori', function($data){
                $namaKategori = JenisKegiatan::whereIn('id',$data->id_jenis_kegiatan)->select('nama_jenis_kegiatan')->get();
                $kat = [];
                foreach($namaKategori as $item){
                    $kat[] = $item->nama_jenis_kegiatan;
                }
                return implode(", ", $kat);
            })
            ->rawColumns(['action','kategori'])
            ->addIndexColumn(true)
            ->make(true);
        }
        $getJabatans = Jabatan::select('id','nama_jabatan')->orderBy('nama_jabatan','ASC')->get();
        $getCategories = JenisKegiatan::select('id','nama_jenis_kegiatan')->get();
        return view('admin-page.handle-proposal.index', compact('getJabatans','getCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jabatan'            => 'required',
            'id_jenis_kegiatan'     => 'required',
        ],[
            'id_jabatan.required'           => 'Anda belum memilih pegawai',
            'id_jenis_kegiatan.required'    => 'Anda belum memilih kategori',
        ]);

        $post = HandleProposal::updateOrCreate(['id' => $request->id],
                [
                    'id_jabatan'            => $request->id_jabatan,
                    'id_jenis_kegiatan'     => $request->input('id_jenis_kegiatan'),
                ]); 

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = HandleProposal::where($where)->first();
     
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = HandleProposal::where('id',$id)->delete();     
        return response()->json($post);
    }
}
