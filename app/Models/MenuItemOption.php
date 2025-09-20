<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemOption extends Model
{
    protected $fillable = ['item_id','group_name','name','price_delta_cents','max_select'];
    protected $casts = ['price_delta_cents'=>'integer','max_select'=>'integer'];

    public function item()
    {
        return $this->belongsTo(MenuItem::class,'item_id');
    }
}
