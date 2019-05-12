<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/15/17
 * Time: 1:07 AM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class AddWatchListRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'user_id' => 'required',
            'category' => 'required'
        ];
    }
}