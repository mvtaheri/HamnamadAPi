<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/28/17
 * Time: 12:46 AM
 */

namespace App\Requests\Parent;


use App\Helpers\FormRequest;

class GetCategoryOfParentRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'id' => 'required|integer',
        ];
    }
}