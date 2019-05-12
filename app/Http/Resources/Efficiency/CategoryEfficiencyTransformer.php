<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/23/2018
 * Time: 12:52 AM
 */

namespace App\Transformers\Efficiency;


use App\Helpers\Transformer;
use App\Transformers\Admin\Market\CategoryTransformer;

class CategoryEfficiencyTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'category_id' => (string)$item['data'],
            'category' => CategoryTransformer::transform($item['category']),
            'efficiency' => $item['sum']
        ];
    }
}