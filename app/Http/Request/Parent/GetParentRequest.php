<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 8/28/17
 * Time: 12:46 AM
 */

namespace App\Requests\Parent;


use App\Helpers\FormRequest;

class GetParentRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'parent_id' => 'required|integer',
        ];
    }
}