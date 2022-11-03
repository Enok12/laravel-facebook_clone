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
    public function a_user_can_send_a_friend_reqeust_only_once()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $response = $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        $friendrequest = Friend::all();
        $this->assertCount(1,$friendrequest);

 
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
                    'friend_id'=> $friendrequest->friend_id,
                    'user_id'=> $friendrequest->user_id,
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

        /** @test */
    public function a_friend_id_is_required_for_friend_reuqest(){

        $user = User::factory()->create();

        $response = $this->actingAs($user,'api')
        ->post('/api/friend-request',[
            'friend_id' => ''
        ]);

        $responsetrim = json_decode($response->getContent(),true);

        //Checks whether the friend_id key is present on the array which means there is a validation required
        $this->assertArrayHasKey('friend_id',$responsetrim['errors']['meta'] );
    }


    /** @test */
    public function a_user_id_and_status_are_required_for_friend_reqiest (){

        $user = User::factory()->create();

        $response = $this->actingAs($user,'api')
        ->post('/api/friend-request-response',[
            'user_id' => '',
            'status' => '' //1 Accepted, 0 Not Accepted
        ])->assertStatus(422);

        $responsetrim = json_decode($response->getContent(),true);

        //Checks whether the friend_id key is present on the array which means there is a validation required
        $this->assertArrayHasKey('user_id',$responsetrim['errors']['meta'] );
        $this->assertArrayHasKey('status',$responsetrim['errors']['meta'] );

    }

 /** @test */
    public function a_friendhsip_is_retrieved_when_fetching_profile(){
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $friendrequest = Friend::create([
            'user_id'=>$user->id,
            'friend_id'=>$anotheruser->id,
            'confirmed_at'=>now()->subDay(),
            'status'=>1,
        ]);

        $this->get('/api/users/'.$anotheruser->id)
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'attributes' => [
                    'friendship' => [
                        'data' => [
                           'friend_request_id' => $friendrequest->id,
                           'attributes' => [
                                'confirmed_at' => '1 day ago',
                           ]
                        ]
                    ]
                ]
                ],
                
        ]);


    }

    /** @test */
    public function an_inverse_friendhsip_is_retrieved_when_fetching_profile(){
        // $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $friendrequest = Friend::create([
            'friend_id'=>$user->id,
            'user_id'=>$anotheruser->id,
            'confirmed_at'=>now()->subDay(),
            'status'=>1,
        ]);

        $this->get('/api/users/'.$anotheruser->id)
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'attributes' => [
                    'friendship' => [
                        'data' => [
                           'friend_request_id' => $friendrequest->id,
                           'attributes' => [
                                'confirmed_at' => '1 day ago',
                           ]
                        ]
                    ]
                ]
                ],
                
        ]);


    }


       /** @test */
       public function friend_request_can_be_ignored(){

        // $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();

        $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        $response = $this->actingAs($anotheruser,'api')
        ->delete('/api/friend-request-response/delete',[
            'user_id' => $user->id,
        ])->assertStatus(204);

        $friendrequest = Friend::first();
        $this->assertNull($friendrequest);
        $response->assertNoContent();

       



    }


     /** @test */
     public function only_the_receiptants_can_ignore_a_friend_request(){
    
        $user = User::factory()->create();
        $this->actingAs($user,'api');

        $anotheruser = User::factory()->create();
        $thirduser = User::factory()->create();
  
        $this->post('/api/friend-request',[
            'friend_id' => $anotheruser->id
        ])->assertStatus(200);

        
        $response = $this->actingAs($thirduser,'api')
        ->delete('/api/friend-request-response/delete',[
            'user_id' => $user->id,
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

        /** @test */
        public function a_user_id_is_required_to_ignore_a_friend_request_response (){
            // $this->withoutExceptionHandling();
            $user = User::factory()->create();
    
            $response = $this->actingAs($user,'api')
            ->delete('/api/friend-request-response/delete',[
                'user_id' => '',
            ])->assertStatus(422);
    
            $responsetrim = json_decode($response->getContent(),true);
    
            //Checks whether the friend_id key is present on the array which means there is a validation required
            $this->assertArrayHasKey('user_id',$responsetrim['errors']['meta'] );
        }


}
