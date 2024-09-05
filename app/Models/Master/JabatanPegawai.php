<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class JabatanPegawai extends Model
{
    protected $guarded = [];

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Master\Pegawai','id_pegawai','id')->withDefault([
            'nip'                   => 'Data Tidak Ada',
            'nama_pegawai'          => 'Data Tidak Ada',
            'email'                 => 'Data Tidak Ada',
            'jenis_kelamin'         => 'Data Tidak Ada',
            'agama'                 => 'Data Tidak Ada',
            'id_status_pegawai'     => 'Data Tidak Ada',
        ]);
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Master\Jabatan','id_jabatan','id')->withDefault([
            'id'                 => 'Data Tidak Ada',
            'kode_jabatan'       => 'Data Tidak Ada',
            'nama_jabatan'       => 'Data Tidak Ada',
            'golongan_jabatan'   => 'Data Tidak Ada',
        ]);
    }
}
