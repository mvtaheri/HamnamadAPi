<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/15/17
 * Time: 6:44 PM
 */

namespace App\Transformers\Category;


use App\Helpers\Transformer;
use App\Transformers\Parent\ParentTransformer;

class AllCategories extends Transformer
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
            'parent' => ParentTransformer::transform($item->parent[0] ?? [])
        ];
    }
}