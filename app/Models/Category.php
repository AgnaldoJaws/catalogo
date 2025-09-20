<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','icon_emoji'];

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_category');
    }
}
