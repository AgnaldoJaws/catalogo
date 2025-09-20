<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['business_id','kind','url','sort_order'];
    protected $casts = ['sort_order'=>'integer'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
