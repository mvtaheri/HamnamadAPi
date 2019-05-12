<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/23/2018
 * Time: 12:52 AM
 */

namespace App\Transformers\Efficiency;


use App\Helpers\Transformer;
use App\Transformers\Market\MarketTransformer;

class MarketEfficiencyTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'market_id' => (string)$item['data'],
            'market' => MarketTransformer::transform($item['market']),
            'efficiency' => $item['sum']
        ];
    }
}