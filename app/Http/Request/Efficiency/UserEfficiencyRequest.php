<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/23/2018
 * Time: 11:03 PM
 */

namespace App\Requests\Efficiency;


use App\Helpers\FormRequest;

class UserEfficiencyRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'wallet_type' => 'require',
            'limit' => 'require|integer'
        ];
    }
}