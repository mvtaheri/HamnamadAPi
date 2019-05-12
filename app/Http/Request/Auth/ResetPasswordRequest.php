<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 3/16/2019
 * Time: 12:02 PM
 */

namespace App\Requests\Auth;

use App\Helpers\FormRequest;


class ResetPasswordRequest extends FormRequest
{
    public static function rules()
    {
        // TODO: Implement rules() method.
        return [
            'email'=>'required|email',
            'password'=>'required',
             'token'=>'required'
        ];
    }

}