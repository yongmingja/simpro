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
            ->select('jabatans.kode_jabatan', 'jabatan_pegawais.ket_jabatan')
            ->first();

        $recentPeranIs = session()->get('selected_peran') ?? $getPeran->kode_jabatan;

        return response()->json([
            'recentPeranIs' => $recentPeranIs,
        ]);
    }
}
