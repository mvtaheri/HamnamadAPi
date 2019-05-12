<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/12/2018
 * Time: 7:21 AM
 */

namespace App\Requests\Order;


use App\Helpers\FormRequest;

class CancelOrderRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'order_id' => 'required'
        ];
    }
}