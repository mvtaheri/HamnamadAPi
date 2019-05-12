<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/25/17
 * Time: 8:49 PM
 */

namespace App\Requests\Order;


use App\Helpers\FormRequest;

class SellRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return[
            'market_id'=>'required|object_id',
            'price'=>'required|integer',
            'type'=>'required|string',
            'count'=>'required|integer'
        ];
    }
}