<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
     protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $dates = ['last_used_at'];

    //一个地址只能属于一个用户
    public function user()
    {
    	return $this->belongsTo(user::class);
    }

    public function getFullAddressAttribute()
    {
    	return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
