<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/6/2017
 * Time: 3:25 PM
 */

namespace App\Transformers\Admin\User;


use App\Helpers\Transformer;
use App\Jobs\Efficiency\UserRiskDetail;

class UserTransformer extends Transformer
{

    /**
     * @param $item
     * @return array|mixed
     * @throws \App\Exceptions\Job\UnexpectedJobException
     */
    public static function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'username' => $item->username,
            'type' => $item->type,
            'email' => $item->email,
            'gender' => $item->gender,
            'avatar' => $item->avatar,
            'birthday' => $item->birthday,
            'country' => $item->country,
            'address' => $item->address,
            'experience_level' => $item->experience_level,
            'know_level' => $item->know_level,
            'created_at' => $item->created_at,
            'risk' => [
                'real' => dispatch(new UserRiskDetail($item->id, ['limit' => 1, 'wallet_type' => 'real'])),
                'virtual' => dispatch(new UserRiskDetail($item->id, ['limit' => 1, 'wallet_type' => 'virtual'])),
            ],
            'is_admin' => $item->is_admin,
        ];
    }
}