<?php

namespace App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

     /** @test */
    public function a_user_can_view_user_profiles()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $posts = Post::factory(1)->create();

        $response = $this->get('/api/users/'.$user->id);
        $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' =>$user->id,
                'attributes' => [
                    'name' => $user->name
                ]
                ],
                'links' => [
                    'self' => url('/users/'.$user->id)
                ]
        ]);
    }


    /** @test */
    public function a_user_can_fetch_posts_for_a_profile(){
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $post = Post::factory()->create(['user_id' => $user->id]);
   

        $response = $this->get('/api/users/'.$user->id.'/posts');
        $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'data' => [
                    'type' => 'posts',
                    'post_id' =>$post->id,
                        'attributes' => [
                            'body' => $post->body,
                            'image'=>$post->image,
                            'posted_at' => $post->created_at->diffForHumans(),
                            'posted_by' => [
                                'data' => [
                                    'attributes' =>[
                                        'name' => $user->name
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => url('/post/'.$post->first()->id)
                    ]
                    
                ]
            ]
                
        ]);
       
    }
}
