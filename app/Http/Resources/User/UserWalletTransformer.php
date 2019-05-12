<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/16/17
 * Time: 1:03 AM
 */

namespace App\Transformers\User;


use App\Helpers\Transformer;

class UserWalletTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'available'=>$item['available'],
            'allocate'=>$item['allocate'],
            'profit'=>$item['profit'],
            'equity'=>$item['equity']

        ];
    }
}