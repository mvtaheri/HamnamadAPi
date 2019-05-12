<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/17/2018
 * Time: 11:30 PM
 */

namespace App\Http\Resources\Feeds;


use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\Resource;

class CommentResource extends Resource
{

    public  function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'description' => $this->description
        ];
    }
}
