<?php

namespace App\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Friend;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_reqeust()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $response = $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ]);
        $response->assertStatus(200);

        $friendrequest = Friend::first();
        $this->assertNotNull($friendrequest);

        $this->assertEquals($anotheruser->id,$friendrequest->friend_id);
        $this->assertEquals($user->id,$friendrequest->user_id);
        $response->assertJson([
            'data'=> [
                'type' => 'friend-request',
                'friend_request_id' => $friendrequest->id,
                'attributes' => [
                    'confirmed_at'=> null,
                ]
                ],
                'links' => [
                    'self'=> url('/user/'.$anotheruser->id)
                ]

        ]);

 
    }

    /** @test */
    public function only_valid_users_can_be_friend_requested(){

        // $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');


        $response = $this->post('/api/friend-request',[
            'friend_id' => '123'
        ])->assertStatus(404);

        $friendrequest = Friend::first();
        $this->assertNull($friendrequest);

        $response->assertJson([
            'errors'=> [
                'code' => 404,
                'title' => 'User not Found',
                'detail' => 'Unable to locate the user with the given information'
            ]
        ]);
    }

}
