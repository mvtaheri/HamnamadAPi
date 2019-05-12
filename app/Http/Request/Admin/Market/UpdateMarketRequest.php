<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/17/17
 * Time: 9:09 PM
 */

namespace App\Requests\Admin\Market;


use App\Helpers\FormRequest;

class UpdateMarketRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'id' => 'required|object_id',
            'buy_price' => 'required|integer',
            'sell_price' => 'required|integer',
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'special' => 'required|string',
            'status' => 'required|string',
        ];
    }
}