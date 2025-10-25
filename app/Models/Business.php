<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Business extends Model
{
    protected $fillable = [
        'user_id','name','slug','about','status',
        'logo_url','logo_path','avg_rating','items_count',
        // novos campos
        'website','instagram','facebook','whatsapp',
    ];

    protected $appends = [
        'logo_src',
        'website_url',
        'instagram_url',
        'facebook_url',
        'whatsapp_link',
    ];

    protected $casts = [
        'avg_rating'  => 'decimal:1',
        'items_count' => 'integer',
        'status'      => 'integer',
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

    /* --------- Accessors --------- */
    public function getLogoSrcAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . ltrim($this->logo_path, '/'));
        }
        return $this->logo_url ?: null;
    }

    public function getWebsiteUrlAttribute(): ?string
    {
        if (!$this->website) return null;
        $u = trim($this->website);
        if (!str_starts_with($u, 'http://') && !str_starts_with($u, 'https://')) {
            $u = 'https://' . $u;
        }
        return $u;
    }

    public function getInstagramUrlAttribute(): ?string
    {
        if (!$this->instagram) return null;
        $h = trim($this->instagram);
        if (str_starts_with($h, 'http://') || str_starts_with($h, 'https://')) return $h;
        $h = ltrim($h, '@/');
        return "https://instagram.com/{$h}";
    }

    public function getFacebookUrlAttribute(): ?string
    {
        if (!$this->facebook) return null;
        $h = trim($this->facebook);
        if (str_starts_with($h, 'http://') || str_starts_with($h, 'https://')) return $h;
        $h = ltrim($h, '@/');
        return "https://facebook.com/{$h}";
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $digits = preg_replace('/\D+/', '', $this->whatsapp);
        if ($digits === '') return null;
        // se vier sem DDI, assume +55
        if (strlen($digits) >= 10 && !str_starts_with($digits, '55')) {
            $digits = '55' . $digits;
        }
        return "https://wa.me/{$digits}";
    }

    /* --------- Mutators (normalize storage) --------- */
    public function setInstagramAttribute($value): void
    {
        $this->attributes['instagram'] = $value ? ltrim(trim($value), '@/') : null;
    }

    public function setFacebookAttribute($value): void
    {
        $v = trim((string)$value);
        // se for URL, guarda como veio; se for handle, guarda só o handle
        if ($v && !str_starts_with($v, 'http://') && !str_starts_with($v, 'https://')) {
            $v = ltrim($v, '@/');
        }
        $this->attributes['facebook'] = $v ?: null;
    }

    public function setWebsiteAttribute($value): void
    {
        $v = trim((string)$value);
        $this->attributes['website'] = $v ?: null; // adicionamos protocolo apenas no accessor
    }

    public function setWhatsappAttribute($value): void
    {
        $digits = preg_replace('/\D+/', '', (string)$value);
        $this->attributes['whatsapp'] = $digits ?: null; // armazenar só dígitos
    }
}
