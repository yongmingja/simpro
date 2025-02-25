<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class HandleProposal extends Model
{
    protected $table = 'handle_proposals';
    protected $guarded = [];
    protected $casts = [ 'id_jenis_kegiatan' => 'array',];
}
