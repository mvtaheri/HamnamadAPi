<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 3/27/2019
 * Time: 4:02 PM
 */

namespace App\Requests\Market;

use App\Helpers\FormRequest;

class RemoveWatchlistItemRequest extends FormRequest
{


    public static function rules()
    {
        /**
         * item_id => [user_id =>int || market_id => ObjectId]
         * type    => ['user' || 'market']
         */
        return [
            'item_id' => 'required',
            'type' => 'required|string|max:6'
        ];
    }

}