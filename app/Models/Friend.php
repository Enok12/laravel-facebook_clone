<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['confirmed_at'];

    public static function friendship($userid){
        return (new static())
        ->where(function($query) use ($userid){
            return $query->where('user_id',auth()->user()->id)
            ->where('friend_id',$userid);
        })
        ->orWhere(function($query) use ($userid){
            return $query->where('friend_id',auth()->user()->id)
            ->where('user_id',$userid);
        })
        ->first();
        
    }

}
