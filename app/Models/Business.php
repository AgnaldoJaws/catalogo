<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Business extends Model
{
    protected $fillable = [
        'user_id','name','slug','logo_url','about',
        'avg_rating','items_count','status',
    ];

    protected $casts = [
        'avg_rating'=>'decimal:1',
        'items_count'=>'integer',
        'status'=>'integer',
    ];

    /* Relationships */
    public function owner()      { return $this->belongsTo(User::class,'user_id'); }
    public function locations()  { return $this->hasMany(BusinessLocation::class); }
    public function categories() { return $this->belongsToMany(Category::class,'business_category'); }
    public function sections()   { return $this->hasMany(MenuSection::class)->orderBy('sort_order'); }
    public function items()      { return $this->hasMany(MenuItem::class)->orderBy('sort_order'); }
    public function media()      { return $this->hasMany(Media::class); }

    /* Scopes */
    public function scopeActive(Builder $q){ return $q->where('status',1); }
    public function scopeWithCategorySlug(Builder $q, ?string $slug){
        return $slug ? $q->whereHas('categories', fn($qq)=>$qq->where('slug',$slug)) : $q;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
