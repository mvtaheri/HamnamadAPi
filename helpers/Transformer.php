<?php

namespace App\Helpers;


/**
 * Class Transformers
 * @package App\Helpers
 */
abstract class Transformer
{
    /**
     * @param $items
     * @return array
     */
    public static function transformArray($items)
    {
        return array_map([ new static, 'transform'], (array) $items);
    }

    /**
     * @param $item
     * @return mixed
     */
    public static abstract function transform($item);
}