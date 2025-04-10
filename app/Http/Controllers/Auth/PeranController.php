<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Master\JabatanPegawai;

class PeranController extends Controller
{
    public function getRecentPeran()
    {
        $getPeran = JabatanPegawai::leftJoin('jabatans', 'jabatans.id', '=', 'jabatan_pegawais.id_jabatan')
            ->where('jabatan_pegawais.id_pegawai', Auth::user()->id)
            ->whereNotNull('jabatan_pegawais.id_fakultas_biro') // Pastikan id_fakultas_biro tidak null
            ->where('jabatan_pegawais.id_fakultas_biro', '!=', '') // Pastikan id_fakultas_biro tidak kosong
            ->select('jabatans.kode_jabatan', 'jabatan_pegawais.ket_jabatan', 'jabatan_pegawais.id_fakultas_biro')
            ->first();
    
        $recentPeranIs = session()->get('selected_peran') ?? $getPeran->kode_jabatan;
        $unitIs = $getPeran->id_fakultas_biro; # sekalian ambil data id fakultas biro untuk menyeleksi pilihan rkat pada saat ajukan proposal on wizard

        return response()->json([
            'recentPeranIs' => $recentPeranIs,
            'unitIs' => $unitIs, # sekalian ambil data id fakultas biro untuk menyeleksi pilihan rkat pada saat ajukan proposal on wizard
        ]);
    }
}
