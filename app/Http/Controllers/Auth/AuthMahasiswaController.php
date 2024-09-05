<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthMahasiswaController extends Controller
{
    public function index()
    {
        return view('auth.homepage');
    }

    public function doLogin(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('mahasiswa')->attempt([
            'user_id' => $request->input('user_id'),
            'password' => $request->input('password'),
        ])) {
            return dd("Login user mahasiswa");
        }
    }
}
