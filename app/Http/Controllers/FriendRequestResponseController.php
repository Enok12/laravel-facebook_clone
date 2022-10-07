<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use App\Http\Resources\Friend as FriendsResource;


class FriendRequestResponseController extends Controller
{
    public function store(){
        $data = request()->validate([
            'user_id' => '',
            'status' => '',
        ]);

        $friendrequest = Friend::where('user_id',$data['user_id'])
        ->where('friend_id',auth()->user()->id)
        ->firstOrFail();

        $friendrequest->update(array_merge($data,[
          'confirmed_at' => now(),  
        ]));

        return  new FriendsResource($friendrequest);

    }
}
