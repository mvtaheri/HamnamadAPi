<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 9/15/17
 * Time: 1:21 AM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class UpdateWatchListRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'category' => 'required',
            'new_name' => 'required'
        ];
    }
}