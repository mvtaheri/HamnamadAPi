<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Alipour
 * Date: 7/28/2017
 * Time: 4:28 PM
 */

namespace App\Requests\Auth;


use App\Helpers\FormRequest;

class RegisterRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
       return[
           'username'=>'required|unique:users',
           'password'=>'required',
           'email'=>'required|email',
           'name'=>'required',
           'type'=>'integer|max:1',
           'gender'=>'valid_char',
           'birthday'=>'max:10',
           'country'=>'valid_char',
           'experience_level'=>'integer|max:1',
           'know_level'=>'integer|max:1',
           'address'=>'valid_char',
       ];
    }
}