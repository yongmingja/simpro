<?php

namespace App\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dekan extends Authenticatable
{
    use Notifiable;
    protected $guard = 'dekan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','id_fakultas', 'email',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function fakultas()
    {
    	return $this->belongsTo('App\Models\General\DataFakultas');
    }
}
