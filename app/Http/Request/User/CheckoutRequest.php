<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/1/2018
 * Time: 6:50 PM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class CheckoutRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'amount' => 'required'
        ];
    }

}
