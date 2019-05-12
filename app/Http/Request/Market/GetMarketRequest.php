<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/17/17
 * Time: 9:09 PM
 */

namespace App\Requests\Market;


use App\Helpers\FormRequest;

class GetMarketRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return[
            'id'=>'required|object_id',
        ];
    }
}