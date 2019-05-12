<?php

namespace App\Http\Resources\Feeds;


use Illuminate\Http\Resources\Json\Resource;

class FeedResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' =>$this->description,
            'like_count' => $this->like_count,
            'user_id' =>$this->user_id
        ];
    }
}
