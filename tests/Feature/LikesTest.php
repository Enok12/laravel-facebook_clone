<?php

namespace App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;



class LikesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function a_user_can_like_a_post()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $post = Post::factory()->create([
            'id' => 123
        ]); //Id is customized to avoid confusion with user id when debugging


        $response = $this->post('/api/posts/'.$post->id.'/like')
        ->assertStatus(200);

        $this->assertCount(1,$user->likedPosts);

        $response-> assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'Likes',
                        'like_id' => 1,
                        'attributes' => []
                    ],
                    'links' => [
                        'self' => url('/posts/123'),
                    ]
                ]
            ],
            'links' =>[
                'self' => url('/posts'),
            ]

        ]);
    }
}
