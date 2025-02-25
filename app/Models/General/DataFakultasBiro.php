<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class DataFakultasBiro extends Model
{
    protected $guarded = [];

    public function dekan()
    {
    	return $this->hasOne('App\Setting\Dekan');
    }
}
