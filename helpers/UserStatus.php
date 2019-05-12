<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/21/2018
 * Time: 6:36 PM
 */

namespace App\Helpers;


use App\Models\People;

class UserStatus
{
    public static function risk($userId)
    {
        $user = People::findByUserId($userId);

        return $user->risk ?? 0;
    }

    public static function efficiency($userId)
    {
        $user = People::findByUserId($userId);

        return $user->efficiency ?? 0;
    }
}