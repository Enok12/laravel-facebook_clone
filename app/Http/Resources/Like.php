<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Like extends JsonResource
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
                'type' => 'Likes',
                'like_id' => $this->id,
                'attributes' => []
            ],
            'links' => [
                'self' => url('/posts/'.$this->pivot->post_id),
            ]
        ];
    }
}
