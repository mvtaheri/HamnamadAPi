<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/12/17
 * Time: 1:17 AM
 */

namespace App\Requests\Market;


use App\Helpers\FormRequest;

class GetAlertRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'market_id' => 'required|object_id',
        ];
    }
}