<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 12/24/2017
 * Time: 10:38 PM
 */

namespace App\Requests\Admin\Category;


use App\Helpers\FormRequest;

class AddCategoryRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'title' => 'required',
            'parent_id' => 'required',
            'description' => 'required',
            'type' => 'required|integer'
        ];
    }
}