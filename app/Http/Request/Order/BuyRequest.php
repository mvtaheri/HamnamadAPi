<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/25/17
 * Time: 8:49 PM
 */

namespace App\Requests\Order;


use App\Helpers\FormRequest;

class BuyRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'market_id' => 'required|object_id',
            'count' => 'required|integer',
            'type' => 'required|string',
            'price' => 'required|integer'
        ];
    }
}