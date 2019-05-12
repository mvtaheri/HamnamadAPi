<?php
/**
 * Created by PhpStorm.
 * User: Hamnamad
 * Date: 4/28/2019
 * Time: 1:07 PM
 */

namespace App\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Copy  extends Eloquent
{
    protected $connection ='mongodb';

    protected $collection ='copys';

    protected $fillable =['user_id','amount' ,'stop' ,'type'];



//    public function people(){
//        return $this->belongsTo(People::class);
//    }
}
