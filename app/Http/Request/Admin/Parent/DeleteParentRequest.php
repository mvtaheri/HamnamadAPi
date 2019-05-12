<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/2/2018
 * Time: 1:18 AM
 */

namespace App\Requests\Admin\Parent;


use App\Helpers\FormRequest;

class DeleteParentRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return ['id' => 'required'];
    }
}