<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/26/2018
 * Time: 7:59 PM
 */

namespace App\Transformers\Efficiency;


use App\Helpers\Transformer;

class UserEfficiencyTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'year' => $item['year'],
            'months' => $item['month']
        ];
    }
}