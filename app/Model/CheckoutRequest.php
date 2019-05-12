<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 7/1/2018
 * Time: 9:56 PM
 */

namespace App\Model;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CheckoutRequest extends Eloquent
{
    protected $collection ='checkout';

    protected $connection ="mongodb";

    protected $guarded =['_id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
