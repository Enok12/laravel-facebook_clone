<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Friend;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection as PostCollection;
use Intervention\Image\Facades\Image;



class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $friends = Friend::friendships();

       if($friends->isEmpty()){
        return new PostCollection(request()->user()->posts);
       }

       return new PostCollection(
        Post::whereIn('user_id',[$friends->pluck('user_id'),$friends->pluck('friend_id')])
        ->get()
       );
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            'body'=> '',
            'image'=> '',
            'width'=> '',
            'height'=> '',

        ]);

        if(isset($data['image'])){
            $image = $data['image']->store('post-images','public');

            Image::make($data['image'])
            ->fit($data['width'],$data['height'])
            ->save(storage_path('app/public/post-images/'.$data['image']->hashName()));
        }
        $post = request()->user()->posts()->create([
            'body'=> $data['body'],
            'image'=> $image ?? null ,
        ]);
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
