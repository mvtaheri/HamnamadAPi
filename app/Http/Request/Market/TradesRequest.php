<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 2/16/2018
 * Time: 11:11 PM
 */

namespace App\Requests\Market;


use App\Helpers\FormRequest;

class TradesRequest extends FormRequest
{

    /**
     * @return mixed
     */
    public static function rules()
    {
        return [
            'instrument_id' => 'require',
            'limit' => 'require'
        ];
    }
}