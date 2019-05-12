<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/1/2017
 * Time: 7:14 AM
 */

namespace App\Traits\Admin;


use App\Models\User;

trait AdminValidate
{

    private function AdminUserValidation(int $userId)
    {
        $user = User::findById($userId);
        if ($user->is_admin == 0)
            die(respond()->fail('Token is not valid',401));
    }
}