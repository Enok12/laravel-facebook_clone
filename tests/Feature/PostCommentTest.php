<?php

namespace App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;


class PostCommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function a_user_can_comment_on_a_post()
    {

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $post = Post::factory()->create([
            'id' => 123
        ]); //Id is customized to avoid confusion with user id when debugging


        $response = $this->post('/api/posts/'.$post->id.'/comment',[
            'body' => 'A great comment here',
        ])
        ->assertStatus(200);

        $comment = Comment::first();

        $this->assertCount(1,Comment::all());
        $this->assertEquals($user->id,$comment->user_id);
        $this->assertEquals($post->id,$comment->post_id);
        $this->assertEquals('A great comment here',$comment->body);

        $response-> assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'body' => 'A great comment here',
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ]
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

    /** @test */
    public function a_nody_is_required_to_leave_a_comment_on_a_post(){
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $post = Post::factory()->create([
            'id' => 123
        ]); //Id is customized to avoid confusion with user id when debugging


        $response = $this->post('/api/posts/'.$post->id.'/comment',[
            'body' => '',
        ])
        ->assertStatus(422);

        $responsetrim = json_decode($response->getContent(),true);

        //Checks whether the friend_id key is present on the array which means there is a validation required
        $this->assertArrayHasKey('body',$responsetrim['errors']['meta'] );
    }

     /** @test */
     public function posts_are_returned_with_comments(){
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $post = Post::factory()->create([
            'id' => 123,
            'user_id' => $user->id,
        ]); //Id is customized to avoid confusion with user id when debugging


        $this->post('/api/posts/'.$post->id.'/comment',[
            'body' => 'A great comment here',
        ])
        ->assertStatus(200);

        $response = $this->get('/api/posts');

        $comment = Comment::first();
        $response->assertStatus(200)
        ->assertJson([
            'data' =>[
                [
                    'data' => [
                        'type'=> 'posts',
                        'attributes' => [
                            'comments' => [
                               'data' => [
                                [
                                    'data' => [
                                        'type' => 'comments',
                                        'comment_id' => 1,
                                        'attributes' => [
                                            'commented_by' => [
                                                'data' => [
                                                    'user_id' => $user->id,
                                                    'attributes' => [
                                                        'name' => $user->name,
                                                    ]
                                                ]
                                            ],
                                            'body' => 'A great comment here',
                                            'commented_at' => $comment->created_at->diffForHumans(),
                                        ]
                                    ],
                                    'links' => [
                                        'self' => url('/posts/123'),
                                    ]
                                ]
                                    ],
                                    'comment_count' => 1,
                                ],
                       
                        ]
                        
                    ]
                ]
            ]
        ]);
       
     }
}
