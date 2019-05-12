<?php

namespace App\Helpers;


/**
 * Class FormRequest
 * @package App\Helpers
 */
abstract class FormRequest
{
    /**
     * @return mixed
     */
   public static abstract function rules();

    /**
     * @param bool $die
     * @return array|bool|null|string
     */
    public static function validate($die = true)
    {
        $result = validate(request(), static::rules());

        if (is_array($result)) {
            return $die == true ? die(respond()->fail($result, 422)) : $result;
        }

        return $result;
    }
}