<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:pegawai,mahasiswa');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::guard('pegawai')->user()){
            return view('dashboard.admin-dashboard');
        } elseif(Auth::guard('mahasiswa')->user()){
            return view('dashboard.mahasiswa-dashboard');
        } else {
            return redirect()->intended('/');
        }
    }
}
