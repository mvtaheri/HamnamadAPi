<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/7/17
 * Time: 1:53 AM
 */

namespace App\Requests\People;


use App\Helpers\FormRequest;

class CopyPeopleRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function  rules()
    {
        return[
            'following_id'=>'required|integer',
            'amount'=>'required|integer',
            'stop'=>'required|integer',
        ];
    }
}