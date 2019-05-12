<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/19/17
 * Time: 10:34 PM
 */

namespace App\Transformers\Market;


use App\Helpers\Transformer;

class OrderMarketTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => (string)self::arrayKeyExist('_id', $item),
            'title' => self::arrayKeyExist('title', $item),
            'instrument_id' => self::arrayKeyExist('mabna_id', $item),
            'image' => self::arrayKeyExist('image', $item),
            'description' => self::arrayKeyExist('description', $item),
            'sell' => (array_key_exists('sell', $item)) ? $item['sell']->price : [],
            'buy' => (array_key_exists('buy', $item)) ? $item['buy']->price : [],
            'followers' => (array_key_exists('follower', $item)) ? count($item['follower']) : 0,
            'investors' => self::arrayKeyExist(1, $item),
            'change' => self::arrayKeyExist('chang', $item),
            'sentiment' => self::arrayKeyExist('sentiment', $item),
            'alert' => ($item['alert']) ? AlertTransformer::transform($item['alert']) : null,
            'category' => (array_key_exists('category', $item)) ? [
                'id' => $item['category']->category_id,
                'title' => (array_key_exists('title', $item['category'])) ? $item['category']->title : '',
                'description' => (array_key_exists('description', $item['category'])) ? $item['category']->description : ''
            ] : [],
            'feeds' => self::arrayKeyExist('feeds', $item)

        ];
    }

    public static function arrayKeyExist($key, $array)
    {
        return array_key_exists($key, $array) ? $array[$key] : '';
    }
}