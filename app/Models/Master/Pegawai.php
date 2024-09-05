<?php

namespace App\Models\Master;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'nip','email', 'nama_pegawai','agama','jenis_kelamin', 'password','id_status_pegawai',
    ];

    protected $hidden = [
        'password',
    ];

    public function jabatanAkademik(){
        return $this->hasMany('App\Models\Master\JabatanAkademik', 'id_pegawai','id');
    }

    public function jabatanPegawai(){
        return $this->hasMany('App\Models\Master\JabatanPegawai', 'id_pegawai','id');
    }
}
