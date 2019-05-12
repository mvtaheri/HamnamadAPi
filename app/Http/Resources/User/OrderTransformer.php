<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/5/2018
 * Time: 7:07 AM
 */

namespace App\Transformers\User;


use App\Helpers\Transformer;
use App\Models\Market;
use App\Transformers\Market\LatestTransformer;
use App\Transformers\Market\MarketTransformer;
use App\Transformers\Market\OrderMarketTransformer;

class OrderTransformer extends Transformer
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
            'price' => $item['price'],
            'count_unit' => $item['count_unit'],
            'wallet_type' => $item['wallet_type'],
            'type' => $item['type'],
            'date' => $item['date'],
            'created_at' => $item['created_at']
        ];
    }
}