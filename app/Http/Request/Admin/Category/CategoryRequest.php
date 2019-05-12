<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/6/2017
 * Time: 1:15 PM
 */
namespace App\Requests\Admin\Category;


use App\Helpers\FormRequest;

class CategoryRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return[
            'id'=>'required|integer',
        ];
    }
}