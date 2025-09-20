<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MenuItem extends Model
{
    protected $fillable = [
        'business_id','section_id','business_loc_id',
        'name','description','price_cents','prep_time_minutes',
        'tags','is_available','image_url','sort_order'
    ];

    protected $casts = [
        'price_cents'=>'integer',
        'prep_time_minutes'=>'integer',
        'tags'=>'array',
        'is_available'=>'boolean',
        'sort_order'=>'integer',
    ];

    public function business(){ return $this->belongsTo(Business::class); }
    public function section() { return $this->belongsTo(MenuSection::class,'section_id'); }
    public function location(){ return $this->belongsTo(BusinessLocation::class,'business_loc_id'); }
    public function options() { return $this->hasMany(MenuItemOption::class,'item_id'); }

    /* full-text simples (MySQL 8) — para MySQL 5.x use LIKE como fallback */
    public function scopeSearch(Builder $q, ?string $term)
    {
        if (!$term) return $q;
        return $q->whereFullText(['name','description'], $term);
    }

    public function scopeAvailable(Builder $q){ return $q->where('is_available', true); }
}
