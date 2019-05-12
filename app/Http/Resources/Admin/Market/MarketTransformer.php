<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/19/17
 * Time: 10:34 PM
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
             'image' => $this->image,
             'sell' =>$this->sell->price,
             'buy' => $this->buy->price,
             'change' => $this->change,
             'sentiment' =>$this->sentiment,
             'special' =>$this->special
        ];
    }
}
