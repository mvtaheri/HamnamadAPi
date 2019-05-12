<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/12/17
 * Time: 11:41 PM
 */

namespace App\Transformers\User;


use App\Helpers\Transformer;

class UserProfileTransformer extends Transformer
{

    /**
     * @param $item
     * @return mixed
     */
    public static function transform($item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'username' => $item['username'],
            'email' => $item['email'],
            'gender' => $item['gender'],
            'description' => $item['description'],
            'avatar' => $item['avatar'],
            'birthday' => $item['birthday'],
            'following' => count($item['following']),
            'feed' => $item['feed'],
            'copied' => $item['copied'],
            'country' => $item['country'],
            'watchlist_setting' => $item['watchlist_setting'],
            'address' => $item['address'],
            'experience_level' => $item['experience_level'],
            'know_level' => $item['know_level'],
            'user_setting' => $item['setting']
        ];
    }
}