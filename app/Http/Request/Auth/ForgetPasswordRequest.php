<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 3/13/2019
 * Time: 1:43 PM
 */

namespace App\Requests\Auth;


use App\Helpers\FormRequest;

class ForgetPasswordRequest extends FormRequest{

    public static function rules()
    {
    // TODO: Implement rules() method.

        return [
            'email'=>'required|email'
        ];
     }

}
{

}