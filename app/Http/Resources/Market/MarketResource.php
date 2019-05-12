<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/24/2019
 * Time: 4:25 PM
 */

namespace App\Http\Resources\Market;


use Illuminate\Http\Resources\Json\Resource;

class MarketResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' =>$this->image,
            'sell' =>$this->sell,
            'status' => $this->title
        ];

    }

}
