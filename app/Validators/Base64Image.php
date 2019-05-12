<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/11/2019
 * Time: 12:53 PM
 */

namespace App\Validators;


class Base64Image
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value)) {
            return true;
        } else {
            return false;
        }
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return "the $attribute must have valid base64 image";
    }
}
