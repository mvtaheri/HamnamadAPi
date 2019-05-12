<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/24/2017
 * Time: 10:18 PM
 */

namespace App\Transformers\Admin\Category;


use App\Helpers\Transformer;

class GetCategoryTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'parent_id' => $item->parent_id,
            'parent_title' => $item->parent_title,
            'parents' => $item->parents
        ];
    }
}