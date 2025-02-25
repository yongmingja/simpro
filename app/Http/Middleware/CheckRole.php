<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Master\Pegawai;
use Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$levels)
    {
        $user = \Auth::guard('pegawai')->user();
        $selectedPeran = $request->session()->get('selected_peran');
        if($user){
            if($user instanceof Pegawai){
                $jabatanPegawai = $user->jabatanPegawai()->first();

                if(!empty($jabatanPegawai)){
                    $jabatan = $jabatanPegawai->jabatan()->first();
                    $jab = '';
                    if(!empty($selectedPeran)){
                        $jab = $selectedPeran;
                    } else {
                        $jab = $jabatan->kode_jabatan;
                    }

                    if($jabatan && in_array($jab, $levels)){
                        return $next($request);
                    }
                }
            }
        } elseif(\Auth::guard('mahasiswa')->user()) {
            if(in_array(\Auth::guard('mahasiswa')->user()->temp_role,$levels)){
                return $next($request);
            }
        }


        return route('base');
    }
}
