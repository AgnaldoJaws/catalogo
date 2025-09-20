<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BusinessLocation extends Model
{
    protected $fillable = [
        'business_id','city_id','name','address',
        'lat','lng','whatsapp','phone','status',
    ];

    protected $casts = ['lat'=>'float','lng'=>'float','status'=>'integer'];

    /* Relationships */
    public function business() { return $this->belongsTo(Business::class); }
    public function city()     { return $this->belongsTo(City::class); }
    public function hours()    { return $this->hasMany(BusinessHour::class,'business_loc_id'); }

    /* Scopes */
    public function scopeActive(Builder $q){ return $q->where('status',1); }

    public function scopeInCitySlug(Builder $q, ?string $citySlug)
    {
        return $citySlug
            ? $q->whereHas('city', fn($qq)=>$qq->where('slug',$citySlug))
            : $q;
    }

    public function scopeWithBusinessCategory(Builder $q, ?string $categorySlug)
    {
        return $categorySlug
            ? $q->whereHas('business.categories', fn($qq)=>$qq->where('slug',$categorySlug))
            : $q;
    }

    /** Busca por raio (km) via Haversine — compatível com MySQL < 8 */
    public function scopeNear(Builder $q, ?float $lat, ?float $lng, float $km = 5.0)
    {
        if ($lat === null || $lng === null) return $q;

        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(lat)) *
            cos(radians(lng) - radians(?)) +
            sin(radians(?)) * sin(radians(lat))
        ))";

        return $q->whereNotNull('lat')->whereNotNull('lng')
            ->select('*')
            ->selectRaw("$haversine AS distance_km", [$lat, $lng, $lat])
            ->having('distance_km','<=',$km)
            ->orderBy('distance_km');
    }

    /** Aberto agora (considera overnight) */
    public function scopeOpenNow(Builder $q)
    {
        $weekday = now()->weekday(); // 0..6
        $time    = now()->format('H:i:s');

        return $q->whereExists(function($sub) use ($weekday,$time){
            $sub->select(DB::raw(1))
                ->from('business_hours as h')
                ->whereColumn('h.business_loc_id','business_locations.id')
                ->where(function($w) use ($weekday,$time){
                    $w->where(function($w1) use ($weekday,$time){
                        $w1->where('h.overnight',0)
                            ->where('h.weekday',$weekday)
                            ->where('h.open_time','<=',$time)
                            ->where('h.close_time','>=',$time);
                    })->orWhere(function($w2) use ($weekday,$time){
                        $w2->where('h.overnight',1)
                            ->where(function($ww) use ($weekday,$time){
                                $ww->where(function($w3) use ($weekday,$time){
                                    $w3->where('h.weekday',$weekday)
                                        ->where('h.open_time','<=',$time);
                                })->orWhere(function($w4) use ($weekday,$time){
                                    $w4->where('h.weekday', ($weekday+6)%7)
                                        ->where('h.close_time','>=',$time);
                                });
                            });
                    });
                });
        });
    }
}
