<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/17/17
 * Time: 9:09 PM
 */

namespace App\Requests\Admin\User;


use App\Helpers\FormRequest;

class UserRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'user_id' => 'required|integer'
        ];
    }
}