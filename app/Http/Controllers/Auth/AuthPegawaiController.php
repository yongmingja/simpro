<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Auth;
use App\Models\Master\Jabatan;

class AuthPegawaiController extends Controller
{
    public function postLogin(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ],[
            'user_id.required' => 'Anda belum menginputkan id pengguna'
        ]);

        if(Auth::guard('pegawai')->attempt(['user_id' => $request->user_id, 'password' => $request->password])){
            return redirect()->route('ui-modul');
        } elseif(Auth::guard('mahasiswa')->attempt(['user_id' => $request->user_id, 'password' => $request->password])){
            return redirect()->route('ui-modul');
        } else {
            return back()->withErrors([
                'user_id' => 'ID Pengguna atau Password anda salah!',
            ]);
        }
    }

    public function logout()
    {
        Session::forget('selected_peran');

        if(Auth::guard('pegawai')->check()){
            Auth::guard('pegawai')->logout();
        } elseif(Auth::guard('mahasiswa')->check()){
            Auth::guard('mahasiswa')->logout();
        }

        return redirect()->intended('/');
    }
}
