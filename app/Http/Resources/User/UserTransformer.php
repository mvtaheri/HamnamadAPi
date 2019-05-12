<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/7/2018
 * Time: 7:43 AM
 */

namespace App\Transformers\User;


use App\Helpers\Transformer;
use App\Helpers\UserStatus;

class UserTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'username' => $item->username,
            'email' => $item->email,
            'gender' => $item->gender,
            'avatar' => $item->avatar,
            'birthday' => $item->birthday,
            'country' => $item->country,
            'address' => $item->address,
            'experience_level' => $item->experience_level,
            'know_level' => $item->know_level,
            'people_follow' => $item->data->people_follow ?? 0,
            'order' => $item->order ?? '',
            'risk' => UserStatus::risk($item->id),
            'efficiency' => UserStatus::efficiency($item->id)
        ];
    }
}