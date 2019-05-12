<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/16/2018
 * Time: 10:27 PM
 */

namespace App\Helpers;


/**
 * Class FileContent
 * @package App\Helpers
 */
class FileContent
{

    /**
     * @param $url
     * @param $item
     * @param $params
     * @return mixed
     */
    public static function get($url, $item, $params)
    {
        $fields = '';
        foreach ($params as $key => $param) {
            $fields .= "{$key}={$param}&";
        }
        $fields = substr($fields, 0, -1);

        return json_decode(file_get_contents("$url$item?{$fields}"), true);

    }

}