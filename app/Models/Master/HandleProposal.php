<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Pegawai;

class HandleProposal extends Model
{
    protected $table = 'handle_proposals';
    protected $guarded = [];
    protected $casts = [ 'id_jenis_kegiatan' => 'array',];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }

}
