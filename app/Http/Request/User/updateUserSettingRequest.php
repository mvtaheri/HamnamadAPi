<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 4/25/2018
 * Time: 11:05 PM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class updateUserSettingRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'full_name' => 'required',
            'value' => 'required|integer'
        ];
    }
}