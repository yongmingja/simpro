<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class DelegasiFpku extends Model
{
    protected $guarded = [];
    protected $casts = [ 'delegasi' => 'array',];
}
