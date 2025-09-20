<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name','state_code','slug','lat','lng'];
    protected $casts = ['lat'=>'float','lng'=>'float'];

    public function locations()
    {
        return $this->hasMany(BusinessLocation::class);
    }
}
