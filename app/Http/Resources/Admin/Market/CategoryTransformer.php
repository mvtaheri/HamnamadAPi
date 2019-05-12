<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/6/2017
 * Time: 1:52 PM
 */

namespace App\Transformers\Admin\Market;


use App\Helpers\Transformer;

class CategoryTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return[
            'id'=>(array_key_exists('id',$item)) ? $item->id :'',
            'title'=>(array_key_exists('title',$item)) ? $item->title : '',
            'selected'=>$item->selected ?? false,
        ];
    }
}