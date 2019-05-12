<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/8/17
 * Time: 12:20 AM
 */

namespace App\Transformers\Market;


use App\Helpers\Transformer;

class LatestTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => (string)$item->_id,
            'instrument_id' => (array_key_exists('mabna_id',$item))? $item->mabna_id :'',
            'title' => $item->title,
            'image' => $item->image,
            'alert' => (is_null($item->alert)) ? [] : ['rate' => $item->alert->rate],
            'sell' => $item->sell->price,
            'buy' => $item->buy->price,
            'category' =>array_key_exists('category',$item) ? [
                'id' =>(array_key_exists(0,$item['category'])) ? $item['category'][0]->category_id:'',
                'title' =>(array_key_exists(0,$item['category'])) ?  $item['category'][0]->title :'',
                'description' => (array_key_exists(0,$item['category'])) ? $item['category'][0]->description :''
            ] :[],
            'parent'=>(array_key_exists('parent',$item))? [
                'id' => $item['parent'][0]->id,
                'title' => $item['parent'][0]->title,
                'description' => $item['parent'][0]->description
            ]:[],
            'change' => $item->change,
            'sentiment' => $item->sentiment,
            'order' =>(array_key_exists('order',$item))? $item->order:''

        ];
    }
}