<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/6/2018
 * Time: 6:21 AM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class UserProfileRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'user_id' => 'require|integer'
        ];
    }
}