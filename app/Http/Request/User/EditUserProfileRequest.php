<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/13/17
 * Time: 12:32 AM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class EditUserProfileRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'name' => 'string',
            'password' => 'string',
            'repeat_password' => 'string',
            'old_password' => 'string',
            'gender' => 'string',
            'avatar' => 'string',
            'birthday' => 'string',
            'country' => 'string',
            'address' => 'string',
            'experience_level' => 'string',
            'know_level' => 'string',
        ];
    }
}