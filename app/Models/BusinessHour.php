<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $fillable = ['business_loc_id','weekday','open_time','close_time','overnight'];
    protected $casts = ['weekday'=>'integer','overnight'=>'boolean'];

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class,'business_loc_id');
    }
}
