<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/13/17
 * Time: 10:35 PM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class UserWalletRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return[
            'type'=>'required',
        ];
    }
}