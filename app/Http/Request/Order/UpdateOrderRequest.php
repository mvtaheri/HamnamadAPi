<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/12/2018
 * Time: 7:21 AM
 */

namespace App\Requests\Order;


use App\Helpers\FormRequest;

class UpdateOrderRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'order_id' => 'required',
            'stop_loss' => 'required|integer',
            'take_profit' => 'required|integer'
        ];
    }
}