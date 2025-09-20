<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name','email','password','phone','role'];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at'=>'datetime'];

    public function businesses()
    {
        return $this->belongsToMany(\App\Models\Business::class, 'business_user')
            ->withPivot('role')
            ->withTimestamps();
    }


}
