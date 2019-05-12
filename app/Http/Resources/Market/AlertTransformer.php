<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 9/29/2018
 * Time: 7:06 AM
 */

namespace App\Transformers\Market;


use App\Helpers\Transformer;

class AlertTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'user_id' => $item['user_id'],
            'market_id' => (string)$item['market_id'],
            'rate' => $item['rate']
        ];
    }
}