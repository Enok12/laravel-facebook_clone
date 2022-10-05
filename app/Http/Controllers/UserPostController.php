<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Resources\PostCollection;
use App\Http\Resources\Post as PostResource;



use Illuminate\Http\Request;

class UserPostController extends Controller
{
    public function index(User $user){

        return new PostCollection($user->posts); 
    }
}
