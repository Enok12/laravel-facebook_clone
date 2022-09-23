<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'users',
                'post_id' => $this->id,
                'attributes' => [
                    'name' => $this->name,
                ] 
                ],
                'links' => [
                    'self' => url('/users/'.$this->id),
                ]
        ];
    }
}
