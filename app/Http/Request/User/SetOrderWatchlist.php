<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 4/7/2018
 * Time: 9:58 PM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class SetOrderWatchlist extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'category' => 'required',
            'type' => 'required'
        ];
    }
}