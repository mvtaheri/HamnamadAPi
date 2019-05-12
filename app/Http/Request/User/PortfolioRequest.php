<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/13/2018
 * Time: 6:39 AM
 */

namespace App\Requests\User;


use App\Helpers\FormRequest;

class PortfolioRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'type' => 'required'
        ];
    }
}