<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use App\Http\Resources\Friend as FriendsResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\FriendRequestNotFoundException;


class FriendRequestResponseController extends Controller
{
    public function store(){
        $data = request()->validate([
            'user_id' => 'required',
            'status' => 'required',
        ]);


        try {
            $friendrequest = Friend::where('user_id',$data['user_id'])
            ->where('friend_id',auth()->user()->id)
            ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            
            throw new FriendRequestNotFoundException();
        }

        $friendrequest->update(array_merge($data,[
          'confirmed_at' => now(),  
        ]));

        return  new FriendsResource($friendrequest);

    }

    public function destroy(){

        $data = request()->validate([
            'user_id'=>'required',
        ]);

            try {
            Friend::where('user_id',$data['user_id'])
            ->where('friend_id',auth()->user()->id)
            ->firstOrFail()->delete();
            } catch (ModelNotFoundException $e) {
            
            throw new FriendRequestNotFoundException();
        }
        return response()->json([],204);
    }



}
