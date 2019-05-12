<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/6/2017
 * Time: 1:52 PM
 */

namespace App\Transformers\Parent;


use App\Helpers\Transformer;

class CategoryTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => $item->id,
            'title' => $item->title
        ];
    }
}