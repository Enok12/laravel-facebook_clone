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

    /** @test */
    public function friend_request_can_be_accepted(){

        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        $response = $this->actingAs($anotheruser,'api')
        ->post('/api/friend-request-response',[
            'user_id' => $user->id,
            'status' => 1 //1 Accepted, 0 Not Accepted
        ])->assertStatus(200);

        $friendrequest = Friend::first();
        $this->assertNotNull($friendrequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(),$friendrequest->confirmed_at); //now()->startOfSecond() will output date along with seconds
        $this->assertEquals(1,$friendrequest->status);

        $response->assertJson([
            'data'=> [
                'type' => 'friend-request',
                'friend_request_id' => $friendrequest->id,
                'attributes' => [
                    'confirmed_at'=> $friendrequest->confirmed_at->diffForHumans(),
                ]
                ],
                'links' => [
                    'self'=> url('/user/'.$anotheruser->id)
                ] 
        ]);



    }
    /** @test */
    public function only_valid_friend_requests_can_be_accrpted(){

        $anotheruser = User::factory()->create();

        $response = $this->actingAs($anotheruser,'api')
        ->post('/api/friend-request-response',[
            'user_id' => '123',
            'status' => 1 //1 Accepted, 0 Not Accepted
        ])->assertStatus(404);

        $friendrequest = Friend::first();
        $this->assertNull($friendrequest);

        $response->assertJson([
            'errors'=> [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to find friend request with the information provided'
            ]
        ]);
    }

    /** @test */
    public function only_valid_receiptants_can_accept_a_friend_request(){
    
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();
        $thirduser = User::factory()->create();
  
        $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        
        $response = $this->actingAs($thirduser,'api')
        ->post('/api/friend-request-response',[
            'user_id' => $user->id,
            'status' => 1 //1 Accepted, 0 Not Accepted
        ])->assertStatus(404);

    
        $friendrequest = Friend::first();
        $this->assertNull($friendrequest->confirmed_at);
        $this->assertNull($friendrequest->status);

        $response->assertJson([
            'errors'=> [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to find friend request with the information provided'
            ]
        ]);



    }


}
