<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 3/11/2018
 * Time: 7:11 AM
 */

namespace App\Requests\Feed;


use App\Helpers\FormRequest;

class LikeCommentRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'feed_id' => 'required|integer'
        ];
    }
}