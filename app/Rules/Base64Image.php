<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/11/2019
 * Time: 12:26 PM
 */

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class Base64Image implements Rule
{
    public function passes($attribute, $value)
    {
        $explode = explode(',', $value);
        $allow = ['png'];
        $format = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
        // check file format
        if (!in_array($format, $allow)) {
            return false;
        }
        // check base64 format
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            return false;
        }
        return true;
    }

    public function message()
    {
       return "the :attribute must have valid base64 image";
    }
}
