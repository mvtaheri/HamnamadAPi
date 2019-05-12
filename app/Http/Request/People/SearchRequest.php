<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/18/2018
 * Time: 9:14 AM
 */

namespace App\Requests\People;


use App\Helpers\FormRequest;
use Slim\Http\Request;

class SearchRequest extends FormRequest
{
    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'parent_id' => 'required',
        ];
    }
}