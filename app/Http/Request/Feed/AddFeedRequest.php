<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/7/2018
 * Time: 7:25 AM
 */

namespace App\Requests\Feed;


use App\Helpers\FormRequest;

class AddFeedRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'description' => 'required'
        ];
    }
}