<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/16/2018
 * Time: 11:02 PM
 */

namespace App\Transformers\Market;


use App\Helpers\Transformer;

class TradesTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            "date" => $item['date_time'],
            "open" => $item['open_price'],
            "high" => $item['high_price'],
            "low" => $item['low_price'],
            "close" => $item['close_price'],
            "volume" => $item['volume'],
            "split" => "",
            "dividend" => $item['close_price_change']
        ];
    }
}