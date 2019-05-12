<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/28/2018
 * Time: 8:54 AM
 */

namespace App\Transformers\Efficiency;


use App\Helpers\Transformer;

class UserTradesTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'total_trades' => $item['total_trades'],
            'positive_trades_average' => $item['positive_trades_average'],
            'negative_trades_average' => $item['negative_trades_average'],
            'positive_trades_percent' => $item['positive_trades_percent']
        ];
    }
}