<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 9/19/2018
 * Time: 6:37 PM
 */

namespace App\Transformers\Wallet;


use App\Helpers\Transformer;
use App\Transformers\Market\OrderMarketTransformer;

class WalletTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => (string)$item['_id'],
            'market' => OrderMarketTransformer::transform((array)$item['market']),
            'price' =>(array_key_exists('price',$item))? $item['price'] : 0,
            'count_unit' => (array_key_exists('total',$item)) ? $item['total']:0,
            'count' => (array_key_exists('total',$item))? $item['total']:0,
            'wallet_type' => (array_key_exists('wallet_type',$item)) ? $item['wallet_type']:'',
            'type' => (array_key_exists('type',$item)) ? $item['type'] :'',
            'date' =>(array_key_exists('date',$item)) ? $item['date']:'',
            'created_at' =>( array_key_exists('created_at',$item)) ? $item['created_at']:''
        ];
    }
}