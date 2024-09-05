<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $guarded = [];

    public function jabatanAkademik(){
        return $this->hasMany('App\Models\Master\JabatanAkademik', 'id_jabatan','id');
    }

    public function jabatanPegawai(){
        return $this->hasMany('App\Models\Master\JabatanPegawai', 'id_jabatan','id');
    }
}
