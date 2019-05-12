<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/28/2017
 * Time: 4:28 PM
 */

namespace App\Requests\Auth;


use App\Helpers\FormRequest;

class AuthRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
       return[
           'username'=>'required',
           'password'=>'required'
       ];
    }
}