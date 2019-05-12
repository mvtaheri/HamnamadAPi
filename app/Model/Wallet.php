<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 5/5/2019
 * Time: 1:43 PM
 */

namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Wallet extends Eloquent
{
    protected $connection ='mongodb';

    protected $collection ="wallet";

    protected $guarded=['_id'];

    public function people(){
    return $this->belongsTo(People::class,'people_id');
   }

}
