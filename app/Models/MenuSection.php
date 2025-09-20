<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSection extends Model
{
    protected $fillable = ['business_id','business_loc_id','name','sort_order'];
    protected $casts = ['sort_order'=>'integer'];

    public function business() { return $this->belongsTo(Business::class); }
    public function location() { return $this->belongsTo(BusinessLocation::class,'business_loc_id'); }
    public function items()    { return $this->hasMany(MenuItem::class,'section_id')->orderBy('sort_order'); }
}
