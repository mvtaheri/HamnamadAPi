<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/15/17
 * Time: 1:21 AM
 */

namespace App\Requests\Market;


use App\Helpers\FormRequest;

class removeWatchListRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return[
            'market_id'=>'required|object_id',
        ];
    }
}