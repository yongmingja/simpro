<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class DataFpku extends Model
{
    protected $guarded = [];

    protected $casts = [ 'peserta_kegiatan' => 'array',];
}
